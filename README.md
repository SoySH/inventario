# Sistema de Inventario – Instalador Base

## 1. Dependencias incluidas

La instalación es portable.  
Todos los binarios necesarios están incluidos en el paquete.

- **PHP 8.5.2 NTS**
- **Nginx 1.29.5**
- **MariaDB Server 11.8.6**
- **VC Runtimes 2005 a 2022** (ejecutar bat para automatizar su instalación de todas las versiones)

## 2. Requisitos mínimos

- **Sistema operativo:** Windows 10 versión 1903 o superior
- **Arquitectura:** x64 (64 bits)
- **Usuario:** permisos de Administrador (según ruta de instalación)

## 3. Permisos de ejecución

Si se instala en:

- **C:\Program Files**
- **C:\Program Files (x86)**

→ Ejecutar el instalador como Administrador

Si se instala en una ruta de usuario:

- **C:\Users\…**

→ Se puede ejecutar con doble clic

## 4. Reglas de ruta de instalación

La ruta final no debe contener caracteres especiales.  
Solo se permiten: letras, números, espacios y guiones bajos (_) y puede ser copiada la ruta y pegar para agilizar.

### Ejemplos válidos

- C:\inventario
- C:\Program Files\inventario
- D:\Proyectos\inventario

### Ejemplos inválidos

- C:\P1Ñ4
- C:\OTHER#Test
- C:\Mis-Proyectos\OTHER!

## Recomendaciones de configuración

- **Usar el subdominio real**: Para evitar alertas de TLS en el navegador, utiliza el subdominio que se muestra como ejemplo en la configuración.
  
- **Editar el archivo `hosts`**: Agrega una entrada en tu archivo `hosts` (ubicado en `C:\Windows\System32\drivers\etc`) para apuntar tu IP interna al subdominio.

  **Nota**: Asegúrate de que los puertos predeterminados no estén ocupados por otros servicios o elige otros disponibles.

## 5. Componentes incluidos

- **PHP:** versión 8.5.2 NTS (incluida en `php\`)
- **NGINX:** incluido en `nginx\` (configuración lista)
- **ManagerApp.exe:** orquestador para gestionar PHP y Nginx
- **instalador.bat:** instalador interactivo

## 6. Estructura del proyecto

Base  
│  
├─ Proyecto  
│   ├─ php\                # PHP 8.5.2 NTS portable  
│   │   ├─ php-cgi.exe  
│   │   ├─ php.ini         # Configurado por el instalador  
│   │   └─ ext\            # Extensiones de PHP  
│   │  
│   ├─ nginx\  
│   │   ├─ nginx.exe       # 1.29.5  
│   │   └─ conf\  
│   │       └─ nginx.conf  # Configurado por el instalador  
│   │  
│   ├─ start_all.bat       # Inicia PHP + Nginx  
│   ├─ start_php.vbs       # Ejecuta PHP-CGI oculto  
│   └─ start_nginx.vbs     # Ejecuta Nginx  
│  
├─ ManagerApp.exe          # Orquestador (Reco: anclar a barra de tareas)  
└─ instalador.bat          # Instalador


## 7. Funcionamiento del instalador (`instalador.bat`)

El instalador realiza los siguientes pasos:

1. **Solicita:**
   - Ruta de instalación final
   - Nombre de la carpeta

2. **Valida:**
   - Que la ruta no tenga caracteres especiales

3. **Copia:**
   - La carpeta `Proyecto` a la ruta seleccionada

4. **Modifica automáticamente:**
   - `php\php.ini`
     - `extension_dir`
     - `session.save_path`
     - `error_log`
   - `nginx\conf\nginx.conf`
     - Ruta root
     - Dominio
     - Puertos 80 y 443
   - `start_php.vbs`
     - Apunta a `php-cgi.exe` y `php.ini`
     - Soporta rutas con espacios y paréntesis
   - `start_nginx.vbs`
     - Apunta al `nginx.exe` correcto
   - Crea un acceso directo de `start_all.bat` en:
     - `%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup`

## 8. Proceso de instalación

1. Descomprimir `Server Inventario.rar` en una ruta válida.
2. Ejecutar `instalador.bat`:
   - Como Administrador si la ruta está en `Program Files`.
3. Seguir las instrucciones:
   - Seleccionar ruta final
   - Configurar dominio y puertos
4. Al finalizar:
   - Se generan los archivos configurados
   - Se crea el acceso directo de inicio automático

## 9. Uso

Se recomienda iniciar los procesos de servidor mediante `start_all.bat`  
o cerrar sesión e iniciar sesión para que se carguen de forma automática.

## 10. Base de datos

Importar el archivo `inventario.sql` en el cliente MySQL de MariaDB.  
Se puede abrir el archivo `.sql` y copiar el contenido directamente en el cliente para crear la base de datos y utilizar el sistema de inventario.

## 11. Notas importantes

- Las rutas con espacios y paréntesis están soportadas.
- Las rutas con caracteres especiales (#, @, acentos, símbolos) no están soportadas.
- El instalador está pensado para:
  - Entornos internos
  - Máquinas con restricciones
- Funciona siempre que se cumplan los requisitos de ruta y permisos.
