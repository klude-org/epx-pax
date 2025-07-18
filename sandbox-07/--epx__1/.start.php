<?php 
########################################################################################################################
#region
    /* 
                                               EPX-PAX-START
    PROVIDER : KLUDE PTY LTD
    PACKAGE  : EPX-PAX
    AUTHOR   : BRIAN PINTO
    RELEASED : 2025-07-04
    
    Copyright (c) 2017-2023 Klude Pty Ltd. https://klude.com.au

    The MIT License

    Permission is hereby granted, free of charge, to any person obtaining
    a copy of this software and associated documentation files (the
    "Software"), to deal in the Software without restriction, including
    without limitation the rights to use, copy, modify, merge, publish,
    distribute, sublicense, and/or sell copies of the Software, and to
    permit persons to whom the Software is furnished to do so, subject to
    the following conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
    
    */
#endregion
# ######################################################################################################################
# i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP
# ######################################################################################################################
#region START
namespace {(function(){
    \defined('_\MSTART') OR \define('_\MSTART', \microtime(true));
    \define('_\START_FILE', \str_replace('\\','/', __FILE__));
    \define('_\START_DIR', \dirname(\_\START_FILE));
    \define('_\START_OB', \ob_get_level());
    \set_include_path(
        \_\START_DIR.PATH_SEPARATOR
        .(\is_dir($d = \dirname(\_\START_DIR,2).'/plugins') ? $d.PATH_SEPARATOR : '')
        .\get_include_path()
    );
})();}
namespace {(function(){
    1 AND \ini_set('display_errors', 0);
    1 AND \ini_set('display_startup_errors', 1);
    1 AND \ini_set('error_reporting', E_ALL);
    0 AND \error_reporting(E_ALL);
    $fault__fn = function($ex = null){
        switch($intfc = $GLOBALS['INTFC']
            ?? (empty($_SERVER['HTTP_HOST']) 
                ? 'cli'
                : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
            )
        ){
            case 'cli':{
                echo "\033[91m\n"
                    .$ex::class.": {$ex->getMessage()}\n"
                    ."File: {$ex->getFile()}\n"
                    ."Line: {$ex->getLine()}\n"
                    ."\033[31m{$ex}\033[0m\n"
                ;
                exit(1);
            } break;
            case 'web':{
                \http_response_code(500);
                while(\ob_get_level() > \_\START_OB){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                echo <<<HTML
                    <pre style="overflow:auto; color:red;border:1px solid red;padding:5px;">{$ex}</pre>
                HTML;
                exit(1);
            } break;
            default:{
                \http_response_code(500);
                while(\ob_get_level() > \_\START_OB){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                \header('Content-Type: application/json');
                echo \json_encode([
                    'status' => "error",
                    'message' => $ex->getMessage(),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                exit(1);
            } break;
        }
    };
    \set_exception_handler(function($ex) use($fault__fn){
        if(!\defined('_\SIG_END')){
            $fault__fn($ex);
        } else {
            throw $ex;
        }
    });
    \set_error_handler(function($severity, $message, $file, $line) use($fault__fn){
        try{
            throw new \ErrorException(
                $message, 
                0,
                $severity, 
                $file, 
                $line
            );
        } catch(\Throwable $ex) {
            if(!\defined('_\SIG_END')){
                $fault__fn($ex);
            } else {
                throw $ex;
            }
        }
    });
    \register_shutdown_function(function() use($fault__fn){
        if(\class_exists(\_\dx::class, false)){
            // do nothing - assuming if dx exists it will have handled this!
        } else if(\defined('_\DISABLE_ORIGIN_EXIT_HANDLER')){
            // do nothing - assuming if dx exists it will have handled this!
        } else {            
            if(\defined('_\SIG_ABORT') && \_\SIG_ABORT < 0){
                exit();
            }
            if(\defined('_\SIG_END')){
                $GLOBALS['_TRACE'][] = "Invalid SIG_END setting or Duplicate call to Root Finalizer";
                exit();
            } else {
                \define('_\SIG_END', \microtime(true));
            };
            \register_shutdown_function(function() use($fault__fn){
                try{
                    if($error = \error_get_last()){ 
                        \error_clear_last();
                        throw new \ErrorException(
                            $error['message'], 
                            0,
                            $error["type"], 
                            $error["file"], 
                            $error["line"]
                        );
                    } 
                } catch(\Throwable $ex) {
                    $fault__fn($ex);
                }
            });
        }
    });
})();}
namespace {$BOOT_FILEPATH = (function(){
    $_SERVER['FW__BOOT_PLUGIN'] ??= "epx__250712_01_std_boot_i__pax__klude_org__github";
    if(!($boot_plugin_name = $_SERVER['FW__BOOT_PLUGIN' ] ?? null)){
        throw new \Exception("Shell plugin name is  not specified");
    }
    
    if(
        !\is_file($f_path = \_\START_DIR."/".($f_stub = "{$boot_plugin_name}/.boot.php"))
        && !\is_file($f_path = \_\DEV_PLUGIN_DIR."/{$f_stub}")
    ){
        $url = "https://raw.githubusercontent.com/klude-org/epx-pax/main/plugins/{$f_stub}";
        if(!($contents = \file_get_contents($url))){
            throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Failed to download repo");
        }
        \is_dir($d = \dirname($f_path = \_\START_DIR."/{$f_stub}")) OR \mkdir($d, 0777, true);
        \file_put_contents($f_path, $contents);
    }
    
    if(
        !\is_file(\_\START_DIR."/".($f_stub = "{$boot_plugin_name}/.boot.bat"))
        && !\is_file(\_\DEV_PLUGIN_DIR."/{$f_stub}")
    ){
        $url = "https://raw.githubusercontent.com/klude-org/epx-pax/main/plugins/{$f_stub}";
        if(!($contents = \file_get_contents($url))){
            throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Failed to download repo");
        }
        \is_dir($d = \dirname(\_\START_DIR."/{$f_stub}")) OR \mkdir($d, 0777, true);
        \file_put_contents($f_path, $contents);
    }
    
    if(!\is_file($f_path)){
        throw new \Exception("Shell plugin error for '{$boot_plugin_name}': Failed to locate start file");
    }
    
    return $f_path;
})();}
namespace {
    if(($f1 = $_SERVER['SCRIPT_FILENAME']) !== ($f2 =__FILE__)){
        return include $BOOT_FILEPATH;
    }
}
