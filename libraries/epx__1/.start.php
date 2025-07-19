<?php
#region PRE-START
namespace {
    \defined('_\MSTART') OR \define('_\MSTART', \microtime(true));
    if(\str_replace('\\','/',__FILE__) !== ($f = (function(){
        try {
            global $_;
            (isset($_) && \is_array($_)) OR $_ = [];
            $_['LIB_TYPE'] ??= 'epx__250718_01__lib';
            $_['LIB_NAME'] ??= '';
            $plugin_dpath= \str_replace('\\','/', \dirname(__DIR__));
            $lib_type = $_['LIB_TYPE'];
            $lib_name = "--epx{$_['LIB_NAME']}";
            $lib_dpath = "{$plugin_dpath}/{$lib_name}";
            $start_php_fpath = \str_replace('\\','/', "{$lib_dpath}/.start.php");
            
            if(\is_file($start_php_fpath)){
                return $start_php_fpath;
            }
            
            if(!\is_dir($lib_dpath)){
                if(!\is_file($start_php_fpath)){
                    $url = "https://raw.githubusercontent.com/klude-org/epx-pax/main/libraries/{$lib_type}/.start.php";
                    if(!($contents = \file_get_contents($url))){
                        throw new \Exception("Library '{$lib_type}': Failed to download repo from '{$url}'");
                    }
                    \is_dir($lib_dpath) OR \mkdir($lib_dpath, 0777, true);
                    if(\file_put_contents($start_php_fpath, $contents) == false){
                        throw new \Exception("Library '{$lib_type}': Failed to write .start.php ");
                    }
                }
            }
            
            if(!\is_file($start_php_fpath)){
                throw new \Exception("Library '{$lib_type}': Missing .start.php");
            }
            return $start_php_fpath;
        } catch (\Throwable $ex){
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
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                    echo <<<HTML
                        <pre style="overflow:auto; color:red;border:1px solid red;padding:5px;">{$ex}</pre>
                    HTML;
                    exit(1);
                } break;
                default:{
                    \http_response_code(500);
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                    \header('Content-Type: application/json');
                    echo \json_encode([
                        'status' => "error",
                        'message' => $ex->getMessage(),
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    exit(1);
                } break;
            }
        }
    })())){
        return include $f;
    } else {
        return include '.start-0.php';
    }
}
#endregion