<?php
include 'db.php';

require_once('tcpdf/tcpdf.php');

$conn->set_charset("utf8mb4");

$buscar = "";
if (!empty($_GET['buscar'])) {
    $buscar = $conn->real_escape_string($_GET['buscar']);
    
    // First, check if search term matches product fields
    $productMatch = $conn->query("SELECT COUNT(*) as count FROM productos 
                                 WHERE nombre LIKE '%$buscar%' 
                                    OR marca LIKE '%$buscar%' 
                                    OR modelo LIKE '%$buscar%' 
                                    OR numero_serie LIKE '%$buscar%'")->fetch_assoc();
    
    if ($productMatch['count'] > 0) {
        // If searching for product attributes, show all matching products
        $res = $conn->query("SELECT p.* FROM productos p
                WHERE p.nombre LIKE '%$buscar%'
                   OR p.marca LIKE '%$buscar%'
                   OR p.modelo LIKE '%$buscar%'
                   OR p.numero_serie LIKE '%$buscar%'
                ORDER BY p.id DESC");
    } else {
        // If not matching product attributes, assume searching for assigned person
        // Only show products assigned to that specific person
        $res = $conn->query("SELECT DISTINCT p.* FROM productos p
                INNER JOIN asignaciones a ON p.id = a.producto_id
                WHERE a.asignado LIKE '%$buscar%'
                ORDER BY p.id DESC");
    }
} else {
    $res = $conn->query("SELECT * FROM productos ORDER BY id DESC");
}

class PDF extends TCPDF
{
    public function Header()
    {
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 15, 'Reporte de Inventario', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }
    
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new PDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Inventario');
$pdf->SetTitle('Reporte de Inventario');
$pdf->SetSubject('Inventario de Productos');

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage();

// Definir anchos de columna (total debe ser aprox. 270 para landscape)
$w = array(15, 45, 30, 30, 40, 15, 60, 25);

// Encabezados de tabla
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$header = array('ID', 'Nombre', 'Marca', 'Modelo', 'Nro Serie', 'Cant', 'Asignaciones', 'Fecha Reg.');

for($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Datos de la tabla
$pdf->SetFont('helvetica', '', 8);
$pdf->SetFillColor(255, 255, 255);

while($r = $res->fetch_assoc()){
    if (!empty($buscar)) {
        // Check if we're searching for a person (not product attributes)
        $productMatch = $conn->query("SELECT COUNT(*) as count FROM productos 
                                     WHERE (nombre LIKE '%$buscar%' 
                                        OR marca LIKE '%$buscar%' 
                                        OR modelo LIKE '%$buscar%' 
                                        OR numero_serie LIKE '%$buscar%') 
                                        AND id = ".$r['id'])->fetch_assoc();
        
        if ($productMatch['count'] == 0) {
            // Searching for person, only show assignments for that person
            $asigs = $conn->query("SELECT asignado, cantidad, fecha FROM asignaciones 
                                 WHERE producto_id=".$r['id']." 
                                 AND asignado LIKE '%$buscar%'");
        } else {
            // Searching for product, show all assignments
            $asigs = $conn->query("SELECT asignado, cantidad, fecha FROM asignaciones WHERE producto_id=".$r['id']);
        }
    } else {
        // No search term, show all assignments
        $asigs = $conn->query("SELECT asignado, cantidad, fecha FROM asignaciones WHERE producto_id=".$r['id']);
    }
    
    $texto_asig = "";
    while($a = $asigs->fetch_assoc()){
        $texto_asig .= $a['asignado'] . " (" . $a['cantidad'] . ")\n";
    }

    // Calcular altura necesaria para la fila basada en el contenido más largo
    $pdf->SetFont('helvetica', '', 8);
    $maxLines = 1;
    
    // Verificar cuántas líneas necesita cada campo
    $nombreLines = $pdf->getStringHeight($w[1], $r['nombre']);
    $asigLines = $pdf->getStringHeight($w[6], trim($texto_asig));
    
    $rowHeight = max(6, $nombreLines, $asigLines);

    // Verificar si necesitamos nueva página
    if($pdf->GetY() + $rowHeight > $pdf->getPageHeight() - 30) {
        $pdf->AddPage();
        // Repetir encabezados
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        for($i = 0; $i < count($header); $i++) {
            $pdf->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 8);
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // ID
    $pdf->Cell($w[0], $rowHeight, $r['id'], 1, 0, 'C');
    
    // Nombre
    $pdf->Cell($w[1], $rowHeight, $r['nombre'], 1, 0, 'L');
    
    // Marca
    $pdf->Cell($w[2], $rowHeight, $r['marca'], 1, 0, 'L');
    
    // Modelo
    $pdf->Cell($w[3], $rowHeight, $r['modelo'], 1, 0, 'L');
    
    // Número de Serie
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell($w[4], $rowHeight, $r['numero_serie'], 1, 0, 'L');
    $pdf->SetFont('helvetica', '', 8);
    
    // Cantidad
    $pdf->Cell($w[5], $rowHeight, $r['cantidad'], 1, 0, 'C');
    
    // Asignaciones - usar MultiCell para texto largo
    $pdf->SetXY($x + $w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5], $y);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->MultiCell($w[6], $rowHeight, trim($texto_asig), 1, 'L', false, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Fecha
    $pdf->SetXY($x + $w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5] + $w[6], $y);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->Cell($w[7], $rowHeight, date('d/m/Y', strtotime($r['fecha_registro'])), 1, 0, 'C');
    $pdf->SetFont('helvetica', '', 8);
    
    $pdf->Ln($rowHeight);
}

$pdf->Output('reporte_inventario.pdf', 'I');
?>
