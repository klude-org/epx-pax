@echo off
setlocal

:: CONFIGURATION
:: set "FW__ROOT_URL=localhost:8000"
:: set "FW__PHP_EXE=php"
:: set "FW__SITE_DIR=%~dp0public"
set "CHROME_EXE=C:\Program Files (x86)\Google\Chrome\Application\chrome.exe"

:: DEBUG: Show settings
echo FW__ROOT_URL: %FW__ROOT_URL%
echo FW__PHP_EXE:  %FW__PHP_EXE%
echo FW__SITE_DIR: %FW__SITE_DIR%
pause

:: POWERSHELL: Check if the PHP process with the -S argument is already running
powershell -nologo -noprofile -command ^
  "$procs = Get-CimInstance Win32_Process | Where-Object { $_.CommandLine -like '*-S %FW__ROOT_URL%*' -and $_.CommandLine -like '*%FW__PHP_EXE%*' }; if ($procs) { exit 1 } else { exit 0 }"

if %errorlevel%==0 (
    echo PHP server NOT running, starting it...
    start "PHP Server" /min "%FW__PHP_EXE%" -S %FW__ROOT_URL% -t "%FW__SITE_DIR%"
) else (
    echo PHP server already running.
)

:: OPEN CHROME
start "" "%CHROME_EXE%" --incognito --app=http://%FW__ROOT_URL%

endlocal
