::<?php echo "\r   \r"; if(0): ?>
::set FW__DEBUG=batch-trace
@echo off
if "%FW__DEBUG%"=="batch-trace" cls
if "%FW__DEBUG%"=="batch-trace" echo on
rem [93mRUNNING: %~f0[0m

::------------------------------------------------------------------------------
if "%FW__SITE_DIR%" NEQ "%FX__SITE_DIR%" goto :define_session
if not defined FW__SESSION goto :define_session
if not defined FW__SHELL_BAT goto :define_session
if not defined FW__INDEX_PHP goto :define_session
if not defined FW__EPX_START_PHP goto :define_session
if not exist %FW__SHELL_BAT% goto :define_session
if not exist %FW__INDEX_PHP% goto :define_session
if not exist %FW__EPX_START_PHP% goto :define_session
goto :session_defined

::------------------------------------------------------------------------------
:define_session
rem [93mDefining Session[0m
call :get_GUID FW__SESSION
call :get_site_dir FW__SITE_DIR
echo [92mNew Session for SITE: %FW__SITE_DIR%[0m
SET FW__RET_BAT=%FW__SITE_DIR%\.local\.ret.bat
SET FW__SHELL_BAT=%FW__SITE_DIR%\.local\.shell.bat
SET FW__INDEX_PHP=%FW__SITE_DIR%\index.php
SET FW__EPX_START_DIR=%~dp0--epx
set FW__EPX_START_PHP=%~dp0--epx\.start.php
if "%FX__ORIGINAL_PATH%"=="" set "FX__ORIGINAL_PATH=%Path%"
set PATH=%FW__EPX_START_DIR%\std-shell;%FX__ORIGINAL_PATH%
:do_setup
rem [93mLaunching setup[0m
::strange block behaviour stupid parser design tons of gotchas >:(
C:/xampp/current/php__xdbg/php.exe "%~f0" || (
    echo [94mERROR LEVEL %errorlevel%[0m
    echo [91mError during setup[0m
    goto :exit_error
)
if %errorlevel%==0 goto :setup_done
:setup_done
call %FW__SHELL_BAT%
if %errorlevel% NEQ 0 goto :exit_error
:session_defined

::------------------------------------------------------------------------------
rem [93mSelecting php.exe[0m
call :get_php FW__PHP_EXE
if "%1"==":fw" (
    if "%2"=="" (
        set FX__
        set FW__
    ) else (
        if "%2"=="clear" (
            call :fw_clear
        )
    )
    goto :exit_ok
) else (
    if "%1"==":debug" (
        if "%2"=="" (
            set FW__DEBUG=on
        ) else (
            if "%2"=="off" (
                set FW__DEBUG=
            ) else (
                set FW__DEBUG=%2
            )
        )
        goto :exit_ok
    ) else (
        %FW__PHP_EXE% %FW__INDEX_PHP% %*
    )
)
::------------------------------------------------------------------------------
set FW__ERROR=%errorlevel%
if not exist "%FW__RET_BAT%" goto :no_env_values
rem [93mSetting returned env variables[0m
call "%FW__RET_BAT%"
if %errorlevel%==0 goto :exit_error
del "%FW__RET_BAT%"
set errorlevel=%FW__ERROR%
:no_env_values
if %errorlevel%==0 goto :exit_ok
::------------------------------------------------------------------------------
:exit_error
rem [93mRunning error handler[0m
if %errorlevel%==2000 goto :launch_cmd
echo [91m!!! Encountered An Error !!![0m
echo %cmdcmdline% | findstr /i /c:" /c" >nul
if %errorlevel%==0 pause
exit /b 1
::------------------------------------------------------------------------------
:launch_cmd
rem [92mLaunching cmd[0m
echo [92mEPX CMD Version 1.00 (C) Klude Pty Ltd. All Rights Reserved[0m
cmd /k
exit /b 0
::------------------------------------------------------------------------------
:exit_ok
exit /b 0

::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:get_GUID
for /f "tokens=2 delims==" %%a in ('wmic os get localdatetime /value') do set dt=%%a
set "%~1=%dt:~0,8%-%dt:~8,4%-%dt:~12,2%%dt:~15,3%-%dt:~6,2%%dt:~8,2%%dt:~10,2%-%dt:~15,3%%dt:~12,2%%dt:~6,2%"
exit /b

::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:get_site_dir
:: %1 is the variable name to set
:: Set to FX__SITE_DIR if defined
if defined FX__SITE_DIR (
    set "%~1=%FX__SITE_DIR%"
) else (
    rem Set to current script's directory
    set "%~1=%~dp0"
)
set "FX__SITE_DIR=%~1"
exit /b

::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:get_php
:: Set defaults if not already defined
if "%FX__PHP_EXEC_STD_PATH%"=="" set "FX__PHP_EXEC_STD_PATH=C:/xampp/current/php/php.exe"
if "%FX__PHP_EXEC_XDBG_PATH%"=="" set "FX__PHP_EXEC_XDBG_PATH=C:/xampp/current/php__xdbg/php.exe"
:: If debugging is enabled, switch to Xdebug version
if "%FW__DEBUG%"=="" (
    if exist %FX__PHP_EXEC_STD_PATH% (
        set "%~1=%FX__PHP_EXEC_STD_PATH%"
    ) else (
        set "%~1=php"
    )
) else (
    if exist %FX__PHP_EXEC_XDBG_PATH% (
        set "%~1=%FX__PHP_EXEC_XDBG_PATH%"
    ) else (
        set "%~1=php"
    )
)
exit /b

::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:get_ret_bat
set "%~1=%FW__SITE_DIR%\___set_cli_env_vars__.bat"
exit /b

::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:get_shell_bat
set "%~1=FW__SHELL_BAT=%FW__SITE_DIR%\.local\.shell.bat"
exit /b

::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:fw_clear
SET FW__JUNK=SOME_JUNK_TO_AVOID_ERROR
for /f "tokens=1 delims==" %%A in ('set FW__') do (
    set VAR=%%A
    call set %%A=
    rem echo Deleting %%A
)
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
            0 AND error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED); //To silence warnings and notices but still catch fatal errors and exceptions:
            1 AND error_reporting(E_ERROR); //catch only fatal error
            0 AND set_error_handler(function($errno, $errstr, $errfile, $errline) { //handle manually
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });
            if(empty($_SERVER['FW__SITE_DIR'])){
                throw new \Exception("Invalid Site DIR");
            }
            if(!\is_dir($site_dir = \trim($_SERVER['FW__SITE_DIR'],'/'))){
                throw new \Exception("Invalid Site DIR");
            }
            $_['MASTER_LIB_TYPE'] ??= 'epx__1';
            $plugin_dpath= \str_replace('\\','/',__DIR__);
            $lib_type = $_['MASTER_LIB_TYPE'];
            $lib_name = "--epx";
            $lib_dpath = "{$plugin_dpath}/{$lib_name}";
            $start_php_fpath = "{$lib_dpath}/.start.php";
            $shell_bat_fpath = "{$site_dir}/.local/.shell.bat";
            if(!\is_file($shell_bat_fpath)){
                \is_dir($d = \dirname($shell_bat_fpath)) OR \mkdir($d, 0777, true);
                \file_put_contents($shell_bat_fpath, <<<BAT
                @echo off
                SET "FX__PHP_EXEC_STD_PATH=C:/xampp/current/php/php.exe"
                SET "FX__PHP_EXEC_XDBG_PATH=C:/xampp/current/php__xdbg/php.exe"
                SET "FX__DEBUG=0"
                SET "FX__CONFIG_LOADED=1"
                BAT);
            }
            if(!\is_dir($lib_dpath)){
                \is_dir($d = \dirname($start_php_fpath)) OR \mkdir($d, 0777, true);
                $url_base = "https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/{$lib_type}";
                echo "[93mDownloading From '{$url_base}'[0m\n";
                if(!($contents = \file_get_contents($url = "{$url_base}/.manifest.json"))){
                    throw new \Exception("Library --epx: Failed to download manifest from '{$url}'");
                }
                if(!($manifest = \json_decode($contents,true))){
                    throw new \Exception("Library --epx: Failed to decode manifest from '{$url}'");
                }
                $failed = false;
                foreach($manifest['files'] ?? [] as $rpath => $v){
                    echo "[33mDownloading '{$rpath}'[0m";
                    if(!($contents = \file_get_contents($url = "{$url_base}/{$rpath}"))){
                        $failed = true;
                        echo "[91m Download Failed !!![0m\n";
                    } else {
                        \is_dir($d = \dirname($fpath = "{$lib_dpath}/{$rpath}")) OR \mkdir($d, 0777, true);
                        if(\file_put_contents($fpath, $contents) == false){
                            $failed = true;
                            echo "[91m Write Failed !!![0m\n";
                        } else{
                            echo "[32m Downloaded[0m\n";
                        }
                    }
                }
                if($failed){
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
                    }})($lib_dpath);
                    throw new \Exception("Library --epx: Failed to install '{$url}'");
                }
            }
            if(!\is_file($start_php_fpath)){
                throw new \Exception("Library --epx: Missing .start.php");
            }
            if(!\is_file($site_index_fpath = "{$site_dir}/index.php")){
                \file_put_contents($site_index_fpath, <<<PHP
                <?php 
                \$_['LIB_TYPE'] = null;
                \$_['LIB_NAME'] = '';
                (include "{$start_php_fpath}")();
                PHP);
            }
            exit(0);
        } catch (\Throwable $ex){
            echo "[91m!!! {$ex->getMessage()} [0m\n";
            exit(1);
        }
    }
})();

__halt_compiler();