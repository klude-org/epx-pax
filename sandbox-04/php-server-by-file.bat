SETLOCAL
call %~dp0php-@.bat
start C:\%FW__PHP_PATH%\php.exe -d user_ini.filename=.user.ini -S %FW__ROOT_URL% index.php