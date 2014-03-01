@ECHO OFF
ECHO Stopping LightTPD...
taskkill /f /IM lighttpd.exe
ECHO Stopping PHP FastCGI...
taskkill /f /IM php-cgi.exe
ECHO.
EXIT