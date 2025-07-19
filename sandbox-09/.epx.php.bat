::set FW__DEBUG=batch-trace
@echo off
if "%FW__DEBUG%"=="batch-trace" cls
if "%FW__DEBUG%"=="batch-trace" echo on
rem [93mRUNNING: %~f0[0m

if "%FX__SITE_DIR%"=="" set FX__SITE_DIR=%~dp0
if "%FW__START_DIR%"=="" set FW__START_DIR=%~dp0--epx
if "%FW__START_BAT%"=="" set FW__START_BAT=%~dp0--epx\.start.bat

if exist "%FW__START_BAT%" goto :start_ready
if not exist %FW__START_DIR% mkdir %FW__START_DIR%
curl --fail --globoff -o "%FW__START_BAT%" https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/epx__1/.start.bat
if %errorlevel%==0 goto :start_downloaded
echo [91m!!! EPX START DOWNLOAD ERROR[0m
goto :exit_error

:start_downloaded
if exist "%FW__START_BAT%" goto :start_ready
echo [91m!!! EPX START NOT FOUND[0m
goto :exit_error

:start_ready
call %FW__START_BAT% %*
if %errorlevel%==0 goto :exit_ok

:exit_error
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:exit_ok
exit /b 0
