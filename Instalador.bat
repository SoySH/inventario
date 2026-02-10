@echo off
setlocal EnableDelayedExpansion

REM ===================================================
echo          Instalador INVENTARIO-W10-1903
echo ===================================================
echo.

REM Carpeta de origen (donde esta el script)
set "ORIGEN=%~dp0Proyecto"

REM Pedir ruta final de instalacion
set /p "DESTINO=Ingresa la ruta final donde se instalara Proyecto: "
if not exist "%DESTINO%" (
    echo La ruta no existe, creando...
    mkdir "%DESTINO%"
)

REM Pedir nombre final para la carpeta
set /p "NOMBRE_FINAL=Ingresa el nombre final: "

set "RUTA_FINAL=%DESTINO%\%NOMBRE_FINAL%"

REM Copiar Proyecto a la ruta final
xcopy "%ORIGEN%" "%RUTA_FINAL%" /E /I /Y >nul
echo Carpeta Proyecto copiada a: %RUTA_FINAL%
echo.

REM ===== Configuracion de PHP =====
set "PHP_INI=%RUTA_FINAL%\php\php.ini"
echo Modificando php.ini...
call powershell -Command ^
"(Get-Content '%PHP_INI%') ^| ForEach-Object { $_ -replace 'C:\\Proyecto','%RUTA_FINAL%' } ^| Set-Content '%PHP_INI%'"

REM ===== Configuracion de Nginx =====
set "NGINX_CONF=%RUTA_FINAL%\nginx\conf\nginx.conf"
echo Configuración de Nginx...
set /p "DOMINIO=Ingresa el dominio o subdominio que usarás (ej: apiqr.ddns.net): "

set /p "PUERTO80=Puerto HTTP (80 por defecto): "
if "%PUERTO80%"=="" set "PUERTO80=80"

set /p "PUERTO443=Puerto HTTPS (443 por defecto): "
if "%PUERTO443%"=="" set "PUERTO443=443"

REM Reemplazar rutas y dominios en nginx.conf de forma segura
call powershell -Command ^
"(Get-Content '%NGINX_CONF%') ^| ForEach-Object { $_ -replace 'C:/Proyecto','%RUTA_FINAL%' -replace 'apiqr.ddns.net','%DOMINIO%' -replace 'listen 80','listen %PUERTO80%' -replace 'listen 443 ssl','listen %PUERTO443% ssl' } ^| Set-Content '%NGINX_CONF%'"

REM ===== Crear acceso directo en carpeta de inicio =====
set "ACCESO=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\start_all.lnk"
echo Creando acceso directo en el inicio...
call powershell -Command ^
"$W = New-Object -ComObject WScript.Shell; ^
 $S = $W.CreateShortcut('%ACCESO%'); ^
 $S.TargetPath = '%RUTA_FINAL%\start_all.bat'; ^
 $S.WorkingDirectory = '%RUTA_FINAL%'; ^
 $S.Save()"

echo.
echo Instalacion completada exitosamente!
echo La carpeta final se encuentra en: %RUTA_FINAL%
echo El acceso directo se agrego al inicio de Windows.
pause
exit
