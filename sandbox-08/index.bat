::set FW__DEBUG=batch-trace
@echo off
if "%FW__DEBUG%"=="batch-trace" echo on
rem RUNNING: %~f0

if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%FX__SITE_DIR%
if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%~dp0
if "%FW__ENV_FILE%"=="" set FW__ENV_FILE=%FW__SITE_DIR%\___set_cli_env_vars__.bat
if "%FX__PHP_EXEC_STD_PATH%"=="" SET "FX__PHP_EXEC_STD_PATH=C:/xampp/current/php/php.exe"
if "%FX__PHP_EXEC_XDBG_PATH%"=="" SET "FX__PHP_EXEC_XDBG_PATH=C:/xampp/current/php__xdbg/php.exe"
set FW__PHP_EXE=%FX__PHP_EXEC_XDBG_PATH%
if "%FW__DEBUG%"=="" set FW__PHP_EXE=%FX__PHP_EXEC_STD_PATH%
if not exist %FW__PHP_EXE% set FW__PHP_EXE=php

if "%*"=="" goto :cmd_launcher
goto :no_cmd
:cmd_launcher
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 goto:launch_cmd
goto :no_cmd
:launch_cmd
echo [92mEPX CMD Version 1.00 (C) Klude Pty Ltd. All Rights Reserved[0m
rem [92mLaunching cmd[0m
cmd /k
goto :exit_ok
:no_cmd

if "%FW__EPX_START_DIR%"=="" SET FW__EPX_START_DIR=%~dp0--epx
if "%FW__EPX_START_PHP%"=="" set FW__EPX_START_PHP=%~dp0--epx\.start.php
if exist "%FW__EPX_START_PHP%" goto :next_3
if not exist %FW__EPX_START_DIR% mkdir %FW__EPX_START_DIR%
curl --fail --globoff -o "%FW__EPX_START_PHP%" https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/epx__1/.start.php
if %errorlevel%==0 goto :next_3_a
echo [91m!!! EPX PHP DOWNLOAD ERROR[0m
:next_3_a
if exist "%FW__EPX_START_PHP%" goto :next_3
echo [91m!!! EPX PHP NOT FOUND[0m
goto :exit_error
:next_3

:launch_php
set FW__INDEX_PHP=%FW__SITE_DIR%\index.php
if exist %FW__INDEX_PHP% goto :launch_php_1
echo ^<?php > "%FW__INDEX_PHP%"
echo $_['LIB_NAME'] = ''; >> "%FW__INDEX_PHP%"
echo (include '%FW__EPX_START_PHP%')();  >> "%FW__INDEX_PHP%"
:launch_php_1
rem [92mLaunching index.php[0m
%FW__PHP_EXE% %FW__SITE_DIR%\index.php %*
if %errorlevel%==0 goto :exit_ok
if %errorlevel%==2000 goto :return_env_values
goto :exit_error

:return_env_values
if not exist "%FW__ENV_FILE%" goto :no_env_values
"%FW__ENV_FILE%"
del "%FW__ENV_FILE%"
goto :exit_ok
:no_env_values

:exit_error
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:exit_ok
exit /b 0