@echo off
if "%FW__SITE_DIR%"=="" (
    set "FW__SITE_DIR=%CD%"
)
if "%FW__DEBUG%" EQU "" ( 
    SET "FW__PHP_EXE=php" 
) else ( 
    SET "FW__PHP_EXE=C:/xampp/current/php__xdbg/php.exe" 
)
%FW__PHP_EXE% %FW__SITE_DIR%index.php %*
if "%FW__PAUSE_ON_EXIT%" NEQ "" ( 
    pause 
) else if %ERRORLEVEL%==2 (
    pause
) else if defined FW__ENV_FILE (
    if exist "%FW__ENV_FILE%" (
        call "%FW__ENV_FILE%"
        del "%FW__ENV_FILE%"
    )
)