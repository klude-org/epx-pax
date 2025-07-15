@echo off
set FW__LIB_TYPE=epx__250715_01__lib
set FW__LIB_NAME=__1
if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%~dp0
if exist "%FW__SITE_DIR%.local\.config.bat" call "%FW__SITE_DIR%.local\.config.bat"

set "FW__START_DIR=%~dp0--epx%FW__LIB_NAME%"
set "FW__START_BAT=%FW__START_DIR%\.start.bat"

:: Attempt Install Library
if exist %FW__START_BAT% goto :next_3
if not exist %FW__START_DIR% mkdir %FW__START_DIR%
echo [92mDownloading library %FW__LIB_TYPE% to --epx%FW__LIB_NAME%[0m
curl --fail --globoff -o %FW__START_BAT% https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/%FW__LIB_TYPE%/.start.php
if %errorlevel%==0 goto :next_3_a
echo [91m!!! LIB DOWNLOAD ERROR[0m
if exist %FW__START_BAT% del %FW__START_BAT%
goto :exit_error
:next_3_a
if exist %FW__START_BAT% goto :next_3
echo [91m!!! INVALID LIB[0m
goto :exit_error
:next_3

call %FW__START_BAT%
if %errorlevel%==0 goto :exit_ok

:exit_error
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:exit_ok
exit /b 0

