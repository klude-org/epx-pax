@echo off
rem RUNNING: %~f0
if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%~dp0
call ..\index.bat %*
pause