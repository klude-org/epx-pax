::set FW__DEBUG=batch-trace
@echo off
if "%FW__DEBUG%"=="batch-trace" cls
if "%FW__DEBUG%"=="batch-trace" echo on
rem [93mRUNNING: %~f0[0m

if "%FW__EPX_LIB_TYPE%"=="" set FW__EPX_LIB_TYPE=epx__2
if "%FW__EPX_PHP_BAT%"=="" set FW__EPX_PHP_BAT=%~f0
if "%FX__SITE_DIR%"=="" set FX__SITE_DIR=%~dp0
if "%FW__SHELL_BAT%"=="" set FW__SHELL_BAT=%FX__SITE_DIR%\.local\.shell.bat
if not exist "%FW__SHELL_BAT%" goto :shell_bat_done
call %FW__SHELL_BAT%
if "%FW__DEBUG%"=="" set FW__DEBUG=%FX__DEBUG%
:shell_bat_done

@echo off
if "%FW__DEBUG%"=="batch-trace" echo on

if "%FW__START_DIR%"=="" set FW__START_DIR=%~dp0--epx
if "%FW__START_BAT%"=="" set FW__START_BAT=%~dp0--epx\.start.bat
if exist "%FW__START_BAT%" goto :start_ready
if not exist %FW__START_DIR% mkdir %FW__START_DIR%
curl --fail --globoff -o "%FW__START_BAT%" https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/%FW__EPX_LIB_TYPE%/.start.bat
if %errorlevel%==0 goto :start_downloaded
echo [91m!!! EPX START DOWNLOAD ERROR[0m
goto :exit_error

:start_downloaded
if exist "%FW__START_BAT%" goto :start_ready
echo [91m!!! EPX START NOT FOUND[0m
goto :exit_error

:start_ready
call %FW__START_BAT% %*
::if %errorlevel%==0 goto :exit_ok
goto :exit_ok

:exit_error
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:exit_ok
exit /b 0
