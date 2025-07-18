::<?php echo "\r   \r"; if(0): ?>
set FW__DEBUG=batch-trace
@echo off
if "%FW__DEBUG%"=="batch-trace" echo on
rem RUNNING: %~f0

if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%FX__SITE_DIR%
if "%FW__SITE_DIR%"=="" set FW__SITE_DIR=%~dp0
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
rem [92mLaunching cmd[0m
cmd /k
goto :exit_ok
:no_cmd

set FW__INDEX_PHP=%FW__SITE_DIR%\index.php
set FW__CONFIG_PHP=%FW__SITE_DIR%\.config.php

if exist %FW__INDEX_PHP% goto :next_1
goto :do_setup
:next_1
if exist %FW__CONFIG_PHP% goto :next_2
goto :do_setup
:next_2
powershell -command ^
    "$f1 = Get-Item '%FW__INDEX_PHP%'; $f2 = Get-Item '%FW__CONFIG_PHP%'; if ($f1.LastWriteTime -gt $f2.LastWriteTime) { exit 1 } else { exit 0 }"
if %errorlevel%==1 goto :launch_php
goto :do_setup

:do_setup
rem [92mLaunching setup[0m
%FW__PHP_EXE% "%~f0"
if %errorlevel%==0 goto :setup_done
echo [91mError during setup[0m
goto :exit_error
:setup_done

:launch_php
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
            \is_file($site_env_fpath = "{$this->site_dir}/.config.php") AND include $site_env_fpath;
            $_['LIB_TYPE'] ??= 'epx__250718_01__lib';
            $_['LIB_NAME'] ??= '__2';
            $plugin_dpath= \str_replace('\\','/',__DIR__);
            $lib_type = $_['LIB_TYPE'];
            $lib_name = "--epx{$_['LIB_NAME']}";
            $lib_dpath = "{$plugin_dpath}/{$lib_name}";
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
            
            if(!\is_file($site_env_fpath)){
                \file_put_contents($site_env_fpath, <<<PHP
                <?php 
                \$_['LIB_TYPE'] = "{$_['LIB_TYPE']}";
                \$_['LIB_NAME'] = "{$_['LIB_NAME']}";
                PHP);
            }
            
            if(!\is_file($site_index_fpath = "{$site_dir}/index.php")){
                \file_put_contents($site_index_fpath, <<<PHP
                <?php (include "{$start_php_fpath}")();
                PHP);
            }

        } catch (\Throwable $ex){
            echo "[91m!!! {$ex->getMessage()} [0m\n";
            exit(1);
        }        
    }
})();
