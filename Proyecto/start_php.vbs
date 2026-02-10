Set WshShell = CreateObject("WScript.Shell")
WshShell.Run """.\php\php-cgi.exe"" -b 127.0.0.1:9000 -c "".\php\php.ini""", 0, False
