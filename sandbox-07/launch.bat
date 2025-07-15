if "%FW__SITE_DIR%"=="" SET FW__SITE_DIR=%~dp0
if exist %FW__SITE_DIR%\.config.bat call %FW__SITE_DIR%\.config.bat
if "%FW__LIBRARY_PLUGIN%"=="" SET FW__LAUNCH_PLUGIN=epx-250712_01_library
if exist %~dp0--%FW__LIBRARY_PLUGIN%\.start.bat goto :launch
SET ZIP_FILE=%~dp0.local-%FW__LIBRARY_PLUGIN%.zip
curl --globoff -o %ZIP_FILE% https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/%FW__LIBRARY_PLUGIN%.zip
if errorlevel 1 goto :abort_download_failed
powershell -nologo -noprofile -command ^
    "Expand-Archive -Path '%ZIP_FILE%' -DestinationPath '%~dp0' -Force"
if errorlevel 1 goto :abort_extract_failed
if not exist %~dp0--%FW__LIBRARY_PLUGIN%\.start.bat goto :abort_invalid_start 

:launch
call %~dp0--%FW__LIBRARY_PLUGIN%\.start.bat %*
if %errorlevel%==0 goto :exit_ok
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
goto :exit_ok


:abort_extract_failed
echo [91m!!! Unable to extract libarary %FW__LIBRARY_PLUGIN%[0m
exit /b 1

:abort_download_failed
echo [91m!!! Unable to download libarary %FW__LIBRARY_PLUGIN%[0m
exit /b 1

:abort_invalid_start
echo [91m!!! Invalid Start %FW__LIBRARY_PLUGIN%[0m
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:exit_ok
exit /b 0