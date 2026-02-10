@echo off
REM PHP oculto
cscript //nologo "%~dp0start_php.vbs"

REM nginx minimizado
call "%~dp0start_nginx.vbs"

exit
