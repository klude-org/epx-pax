@echo off
SETLOCAL
call %~dp0php-@.bat
C:\%FW__PHP_PATH%\php.exe -f %~dp0\index.php %*