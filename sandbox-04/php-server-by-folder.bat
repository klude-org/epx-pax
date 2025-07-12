SETLOCAL
call %~dp0php-@.bat
start C:\%FW__PHP_PATH%\php.exe -S %FW__ROOT_URL% -t %~dp0