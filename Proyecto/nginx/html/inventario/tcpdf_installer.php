<?php
echo "Instalando TCPDF...<br>";

if (!file_exists('tcpdf')) {
    echo "Descargando TCPDF...<br>";
    
    // Crear directorio tcpdf
    mkdir('tcpdf', 0755, true);
    
    // Descargar TCPDF desde GitHub
    $tcpdf_url = 'https://github.com/tecnickcom/TCPDF/archive/refs/heads/main.zip';
    $zip_file = 'tcpdf.zip';
    
    if (file_put_contents($zip_file, file_get_contents($tcpdf_url))) {
        echo "Descarga completada. Extrayendo...<br>";
        
        $zip = new ZipArchive;
        if ($zip->open($zip_file) === TRUE) {
            $zip->extractTo('.');
            $zip->close();
            
            // Mover archivos a la carpeta tcpdf
            if (is_dir('TCPDF-main')) {
                $files = scandir('TCPDF-main');
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        rename('TCPDF-main/' . $file, 'tcpdf/' . $file);
                    }
                }
                rmdir('TCPDF-main');
            }
            
            unlink($zip_file);
            echo "TCPDF instalado correctamente!<br>";
            echo '<a href="reporte.php">Probar reporte PDF</a>';
        } else {
            echo "Error al extraer el archivo ZIP.<br>";
        }
    } else {
        echo "Error al descargar TCPDF.<br>";
    }
} else {
    echo "TCPDF ya está instalado.<br>";
    echo '<a href="reporte.php">Probar reporte PDF</a>';
}
?>
