Set fso = CreateObject("Scripting.FileSystemObject")
Set shell = CreateObject("WScript.Shell")

' Obtener ruta donde está el VBS
base = fso.GetParentFolderName(WScript.ScriptFullName)

' Ruta al nginx.exe
exe = base & "\nginx\nginx.exe"

' Cambiar directorio de trabajo antes de ejecutar
shell.CurrentDirectory = base & "\nginx"

' Lanzar nginx oculto
shell.Run """" & exe & """", 0, False
