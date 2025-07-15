::@echo off
rem RUNNING: %~f0
if "%FW__SITE_DIR%"=="" set "FW__SITE_DIR=%CD%"

if "%FX__ORIGINAL_PATH%"=="" set "FX__ORIGINAL_PATH=%Path%"
SET PATH=%~dp0\std-shell;%FX__ORIGINAL_PATH%

if exist "%FW__SITE_DIR%\.local\.config.bat" "%FW__SITE_DIR%\.local\.config.bat"

if not exist "%FW__SITE_DIR%\index.php" copy %~dp0std-templates\index.php "%FW__SITE_DIR%\index.php"

if "%FX__PHP_EXEC_STD_PATH%"=="" SET "FX__PHP_EXEC_STD_PATH=C:/xampp/current/php/php.exe"
if "%FX__PHP_EXEC_XDBG_PATH%"=="" SET "FX__PHP_EXEC_XDBG_PATH=C:/xampp/current/php__xdbg/php.exe"
if "%FW__ROOT_URL%"=="" set "FW__ROOT_URL=127.0.20.20:8000"

set FW__PHP_EXE=%FX__PHP_EXEC_XDBG_PATH%
if "%FX__DEBUG%"=="" set FW__PHP_EXE=%FX__PHP_EXEC_STD_PATH%
:: echo %FW__ROOT_URL%
:: echo %FX__PHP_EXEC_STD_PATH%
:: echo %FX__PHP_EXEC_XDBG_PATH%
:: echo %FW__PHP_EXE%
:: pause
if "%*"=="" goto :cmd_launcher
    
%FW__PHP_EXE% %FW__SITE_DIR%index.php %*
if "%FW__PAUSE_ON_EXIT%" NEQ "" ( 
    pause
) else if %ERRORLEVEL%==2 (
    pause
) else if "%FW__ENV_FILE%"==""  (
    if exist "%FW__ENV_FILE%" (
        call "%FW__ENV_FILE%"
        del "%FW__ENV_FILE%"
    )
)

:cmd_launcher
:: %cmdcmdline% contains the command that launched the CMD session.
:: When double-clicked from Explorer, Windows launches it like:
:: cmd.exe /c "C:\Path\to\script.bat"
:: But if you run from a prompt or call, %cmdcmdline% won't contain /c "fullpath".
:: Check if script was run via double-click (explorer launches using 'cmd.exe /c "script.bat"')
:: Detect double-click using a loose match - stupid batch parser causes exact match issues
echo %cmdcmdline% | findstr /i /c:" /c" >nul
:: !!! use rem inside () dont use :: because bat parser goes kaplowee
if %errorlevel%==0 (
    rem Detected: Double-click
    rem start "" cmd.exe /k
    cmd /k
    exit /b 0
) else (
    rem Detected: Called from command prompt or another script
    exit /b 0
)




<?php endif; 
