@echo off
if "%FW__DEBUG%" EQU "" ( SET "FW__PHP_EXE=php" ) else ( SET "FW__PHP_EXE=C:/xampp/current/php__xdbg/php.exe" )
%FW__PHP_EXE% %~dp0index.php %*
if "%FW__PAUSE_ON_EXIT%" NEQ "" ( pause )
