::
:: Installed: #__FW_INSTALLED__#
:: #####################################################################################################################
:: #region LICENSE
::     /* 
::                                                EPX-WIN-SHELL
::     PROVIDER : KLUDE PTY LTD
::     PACKAGE  : EPX-PAX
::     AUTHOR   : BRIAN PINTO
::     RELEASED : 2025-03-10
::     
::     The MIT License
::     
::     Copyright (c) 2017-2025 Klude Pty Ltd. https://klude.com.au
::     
::     of this software and associated documentation files (the "Software"), to deal
::     in the Software without restriction, including without limitation the rights
::     to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
::     copies of the Software, and to permit persons to whom the Software is
::     furnished to do so, subject to the following conditions:
::     
::     The above copyright notice and this permission notice shall be included in
::     all copies or substantial portions of the Software.
::     
::     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
::     IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
::     FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
::     AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
::     LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
::     OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
::     THE SOFTWARE.
::         
::     */
:: #endregion
:: # ###################################################################################################################
:: # i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP

if "%FW__SITE_DIR%"=="" set "FW__SITE_DIR=%CD%"
if exist %FW__SITE_DIR%.local\.config.bat call %FW__SITE_DIR%.local\.config.bat

if "%FW__DEBUG%"=="" set FW__DEBUG=%FX__DEBUG%
if "%FW__DEBUG%"=="batch-trace" (
    @echo on
) else (
    @echo off
)
:: Set variables
rem RUNNING: %~f0
if "%FW__BOOT_PLUGIN%"=="" SET FW__BOOT_PLUGIN=epx__250712_01_std_boot_i__pax__klude_org__github
if "%FX__PHP_EXEC_STD_PATH%"=="" SET "FX__PHP_EXEC_STD_PATH=C:/xampp/current/php/php.exe"
if "%FX__PHP_EXEC_XDBG_PATH%"=="" SET "FX__PHP_EXEC_XDBG_PATH=C:/xampp/current/php__xdbg/php.exe"
set FW__PHP_EXE=%FX__PHP_EXEC_XDBG_PATH%
if "%FW__DEBUG%"=="" set FW__PHP_EXE=%FX__PHP_EXEC_STD_PATH%
if not exist %FW__PHP_EXE% set FW__PHP_EXE=php

:: Installed Plugin
if not exist %~dp0%FW__BOOT_PLUGIN%\.boot.bat goto :next_1
call %~dp0%FW__BOOT_PLUGIN%\.boot.bat %*
if %errorlevel%==0 goto :exit_ok
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
goto :exit_ok
:next_1

set "FW__START_PHP=%~dp0\.start.php"

:: Attempt Install Library
if exist %FW__START_PHP% goto :next_3
echo [92mDownloading start file from %FW__LIB_TYPE% .start.php[0m
curl --fail --globoff -o %FW__START_PHP% https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/%FW__LIB_TYPE%/.start.php
if %errorlevel%==0 goto :next_3_a
echo [91m!!! START_PHP DOWNLOAD ERROR[0m
if exist %FW__START_PHP% del %FW__START_PHP%
goto :exit_error
:next_3_a
if exist %FW__START_PHP% goto :next_3
echo [91m!!! INVALID LIB[0m
goto :exit_error
:next_3

:: Attempt Run Boot
%FW__PHP_EXE% "%~dp0.start.php"
if not exist %~dp0%FW__BOOT_PLUGIN%\.boot.bat goto :next_4
call %~dp0%FW__BOOT_PLUGIN%\.boot.bat %*
if %errorlevel%==0 goto :exit_ok
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
goto :exit_ok
:next_4

:abort
echo [91m!!! INVALID SHELL[0m
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:exit_ok
exit /b 0