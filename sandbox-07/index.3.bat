::<?php echo "\r   \r"; if(0): ?>
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
:: Set variables
:: set FW__DEBUG=batch-trace
@echo off
if "%FW__DEBUG%"=="batch-trace" echo on
rem RUNNING: %~f0

:: #############################################################################
if "%FX__ENVIROMENT%" NEQ "" goto :env_setting_done
set FX__ENVIROMENT=1
set FW__LIB_TYPE=epx__250715_01__lib
set FW__LIB_NAME=__3
if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%FX__SITE_DIR%
if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%~dp0
if exist "%FW__SITE_DIR%.local\.config.bat" call "%FW__SITE_DIR%.local\.config.bat"
if "%FW__DEBUG%"=="" set FW__DEBUG=%FX__DEBUG%
if "%FX__PHP_EXEC_STD_PATH%"=="" SET "FX__PHP_EXEC_STD_PATH=C:/xampp/current/php/php.exe"
if "%FX__PHP_EXEC_XDBG_PATH%"=="" SET "FX__PHP_EXEC_XDBG_PATH=C:/xampp/current/php__xdbg/php.exe"
set FW__PHP_EXE=%FX__PHP_EXEC_XDBG_PATH%
if "%FW__DEBUG%"=="" set FW__PHP_EXE=%FX__PHP_EXEC_STD_PATH%
if not exist %FW__PHP_EXE% set FW__PHP_EXE=php
set "FW__START_DIR=%~dp0--epx%FW__LIB_NAME%"
set "FW__START_BAT=%FW__START_DIR%\.start.bat"
:: ##############################################################
if exist %FW__START_DIR% goto :start_dir_exists
echo [91mStart DIR does not exist[0m
goto :exit_error
:start_dir_exists
:: ##############################################################
if exist %FW__START_BAT% goto :start_bat_exists
%FW__PHP_EXE% "%~f0"
if exist %FW__START_BAT% goto :next_3
echo [91mStart BAT does not exist[0m
goto :exit_error
:start_bat_exists
:: ##############################################################
if "%FX__ORIGINAL_PATH%"=="" set "FX__ORIGINAL_PATH=%Path%"
set PATH=%FW__START_DIR%\std-shell;%FX__ORIGINAL_PATH%
:env_setting_done
:: #############################################################################

if "%*"=="" goto :cmd_launcher

set FW__PHP_EXE=%FX__PHP_EXEC_XDBG_PATH%
if "%FW__DEBUG%"=="" set FW__PHP_EXE=%FX__PHP_EXEC_STD_PATH%
if not exist %FW__PHP_EXE% set FW__PHP_EXE=php


if exist %FW__START_BAT% goto :next_3
echo [91m!!! INVALID LIB[0m
goto :exit_error
:next_3
if "%FW__DEBUG%"=="batch-trace" echo [92mCalling .start.bat[0m
call %FW__START_BAT% %*
if %errorlevel%==0 goto :exit_ok

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
    echo [92m250718_01 Library CMD[0m
    cmd /k
    exit /b 0
) else (
    rem Detected: Called from command prompt or another script
    exit /b 0
)

:exit_error
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:exit_ok
exit /b 0
<?php endif;
try {
    1 AND \ini_set('display_errors', 0);
    1 AND \ini_set('display_startup_errors', 1);
    1 AND \ini_set('error_reporting', E_ALL);
    $plugin_dpath= \str_replace('\\','/',__DIR__);
    
    if(empty($_SERVER['FW__LIB_TYPE'])){
        throw new \Exception("Library type is not specified");
    }
    if(empty($_SERVER['FW__LIB_NAME'])){
        throw new \Exception("Library name is not specified");
    }
    try{
        $lib_type = $_SERVER['FW__LIB_TYPE'];
        $lib_name = "--epx{$_SERVER['FW__LIB_NAME']}";
        $lib_dpath = "{$plugin_dpath}/{$lib_name}";
        $url = "https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/{$lib_type}.zip";
        \is_dir($l_tmp_dpath = "{$plugin_dpath}/.local-".uniqid()) OR \mkdir($l_tmp_dpath, 0777, true);
        $l_zip_fpath = "{$l_tmp_dpath}/downloaded.zip";
        $l_zip_dpath = "{$l_tmp_dpath}/extracted";
        if(!($contents = \file_get_contents($url))){
            throw new \Exception("Library '{$lib_type}': Failed to download repo from '{$url}'");
        }
        if(\file_put_contents($l_zip_fpath, $contents) == false){
            throw new \Exception("Library '{$lib_type}': Failed to write zip '{$l_zip_fpath}'");
        }
        try{
            if (!(($zip = new \ZipArchive)->open($l_zip_fpath) === true)) {
                throw new \Exception("Library '{$lib_type}': Failed to open zip '{$l_zip_fpath}'");
            }
            if(!$zip->extractTo($l_zip_dpath)){
                throw new \Exception("Library '{$lib_type}': Failed to extract '{$l_zip_fpath}' to '{$l_zip_dpath}'");
            }
        } finally {
            $zip->close();
        }
        if(!\is_dir($lib_plugin_dpath = "{$l_zip_dpath}/{$lib_type}")){
            throw new \Exception("Library '{$lib_type}': Missing lib folder in '{$l_zip_dpath}'");
        }
        if(!\rename($lib_plugin_dpath, $lib_dpath)){
            throw new \Exception("Library '{$lib_type}': Unable to install '{$lib_name}'");
        }
    } finally {
        1 AND (function($d){if(\is_dir($d)){
            foreach(new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($d, \RecursiveDirectoryIterator::SKIP_DOTS)
                , \RecursiveIteratorIterator::CHILD_FIRST
            ) as $f) {
                if ($f->isDir()){
                    rmdir($f->getRealPath());
                } else {
                    unlink($f->getRealPath());
                }
            }
            rmdir($d);
        }})($l_tmp_dpath);
    }
} catch (\Throwable $ex){
    echo "[91m!!! {$ex->getMessage()} [0m\n";
    exit(1);
}

