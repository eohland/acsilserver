@ECHO OFF
ECHO Starting PHP FastCGI...
set PHP_FCGI_MAX_REQUESTS=0
start PHP\php-cgi.exe -b 127.0.0.1:9123 -c php-acsil.ini
ECHO Starting LightTPD...
ECHO.
LightTPD\lighttpd.exe -D -f lighttpd-acsil.conf -m LightTPD\modules
EXIT
