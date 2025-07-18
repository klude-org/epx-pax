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
set FW__DEBUG=batch-trace
@echo off
if "%FW__DEBUG%"=="batch-trace" echo on
rem RUNNING: %~f0
if "%FX__ENV%"=="" goto :env_load
if "%FX__ENV%"=="%FW__SITE_DIR%" goto :env_done
:env_load
rem [92mLoading Environment[0m
if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%FX__SITE_DIR%
if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%~dp0
set "FW__SHELL_BAT=%FW__SITE_DIR%\.local\.shell.bat"
set FW__DEBUG=%FX__DEBUG%
if "%FX__PHP_EXEC_STD_PATH%"=="" SET "FX__PHP_EXEC_STD_PATH=C:/xampp/current/php/php.exe"
if "%FX__PHP_EXEC_XDBG_PATH%"=="" SET "FX__PHP_EXEC_XDBG_PATH=C:/xampp/current/php__xdbg/php.exe"
set FW__PHP_EXE=%FX__PHP_EXEC_XDBG_PATH%
if "%FW__DEBUG%"=="" set FW__PHP_EXE=%FX__PHP_EXEC_STD_PATH%
if not exist %FW__PHP_EXE% set FW__PHP_EXE=php
if exist "%FW__SITE_DIR%\.local\.shell.bat" goto :config_exists
rem [92mLaunching self php[0m
%FW__PHP_EXE% "%~f0"
if %errorlevel%==0 goto :self_done
echo [91mError in installation[0m
goto :exit_error
:self_done
if exist "%FW__SITE_DIR%\.local\.shell.bat" goto :config_exists
echo [91mMissing .shell.bat[0m
goto :exit_error
:config_exists
call "%FW__SITE_DIR%\.local\.shell.bat"
if %errorlevel%==0 goto :config_loaded
echo [91mEncountered and error reading config[0m
goto :exit_error
set FX__ENV=%FW__SITE_DIR%
:config_loaded
:env_done


powershell -command ^
    "$f1 = Get-Item '%file1%'; $f2 = Get-Item '%file2%'; if ($f1.LastWriteTime -gt $f2.LastWriteTime) { exit 1 } else { exit 0 }"

if %errorlevel%==1 (
    echo %file1% is newer than %file2%
) else (
    echo %file1% is not newer than %file2%
)

rem [92m
rem FW__SITE_DIR %FW__SITE_DIR%
rem FX__ENV %FX__ENV%
rem FW__DEBUG %FW__DEBUG%
rem FX__PHP_EXEC_STD_PATH %FX__PHP_EXEC_STD_PATH%
rem FX__PHP_EXEC_XDBG_PATH %FX__PHP_EXEC_XDBG_PATH%
rem FW__PHP_EXE %FW__PHP_EXE%
rem [0m

if "%*"=="" goto :cmd_launcher
goto :no_cmd
:cmd_launcher
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 goto:launch_cmd
goto :no_cmd
cmd /k
goto :exit_ok
:no_cmd

:launch_php
rem [92mLaunching index.php[0m
%FW__PHP_EXE% "%~dp0index.php" %*
if %errorlevel%==0 goto :exit_ok

:exit_error
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1

:exit_ok
exit /b 0
<?php endif;
(new class extends \stdClass {
    public function __invoke(){
        try {
            global $_;
            (isset($_) && \is_array($_)) OR $_ = [];
            1 AND \ini_set('display_errors', 0);
            1 AND \ini_set('display_startup_errors', 1);
            1 AND \ini_set('error_reporting', E_ALL);
            if(empty($_SERVER['FW__SITE_DIR'])){
                throw new \Exception("Invalid Site DIR");
            }
            if(!\is_dir($this->site_dir = \trim($_SERVER['FW__SITE_DIR'],'/'))){
                throw new \Exception("Invalid Site DIR");
            }
            \is_file($config_php_fpath = "{$this->site_dir}/.config.php") AND include $config_php_fpath;
            $_['LIB_TYPE'] ??= 'epx__250718_01__lib';
            $_['LIB_NAME'] ??= '__2';
            $plugin_dpath= \str_replace('\\','/',__DIR__);
            $lib_type = $_['LIB_TYPE'];
            $lib_name = "--epx{$_['LIB_NAME']}";
            $lib_dpath = "{$plugin_dpath}/{$lib_name}";
            if(!\is_file($shell_bat_fpath = "{$this->site_dir}/.local/.shell.bat")){
                \is_dir($d = \dirname($shell_bat_fpath)) OR \mkdir($d, 0777, true);
                $cli_dir = __DIR__.'\\cli';
                $session_uniqid=\uniqid();
                \file_put_contents($shell_bat_fpath, <<<BAT
                @echo off
                SET "FX__SESSION={$session_uniqid}"
                SET "FX__DOCUMENT_ROOT={$root_dir}"
                SET "FX__PHP_EXEC_STD_PATH=C:/xampp/current/php/php.exe"
                SET "FX__PHP_EXEC_XDBG_PATH=C:/xampp/current/php__xdbg/php.exe"
                SET "FX__ENV_FILE=___set_cli_env_vars__.bat"
                SET "FX__DEBUG=0"
                SET "FX__CONFIG_LOADED=1"
                if not defined FX__ORIGINAL_PATH SET "FX__ORIGINAL_PATH=%Path%"
                SET PATH={$cli_dir};%FX__ORIGINAL_PATH%
                echo [92mEPX WIN SHELL 250718-013[0m
                cmd /k
                exit /b 0
                BAT);
            }
            if(!\is_dir($lib_dpath)){
                try{
                    \is_dir($l_tmp_dpath = "{$plugin_dpath}/.local-".uniqid()) OR \mkdir($l_tmp_dpath, 0777, true);
                    $url = "https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/{$lib_type}.zip";
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
            }
            
            if(!\is_file($start_php_fpath = "{$lib_dpath}/.start.php")){
                throw new \Exception("Library '{$lib_type}': Missing .start.php");
            }
            
            if(!\is_file($site_index_fpath = "{$site_dir}/index.php")){
                \file_put_contents($shell_bat_fpath, <<<PHP
                <?php (include "{$start_php_fpath}")();
                PHP);
            }

        } catch (\Throwable $ex){
            echo "[91m!!! {$ex->getMessage()} [0m\n";
            exit(1);
        }        
    }
})();