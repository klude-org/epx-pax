<?php
global $_;
(isset($_) && \is_array($_)) OR $_ = [];
1 AND \ini_set('display_errors', 0);
1 AND \ini_set('display_startup_errors', 1);
1 AND \ini_set('error_reporting', E_ALL);
\define('_\LIB_NAME_DEFAULT','__2');
if(\is_file($f = "--epx".($_['LIB_NAME'] ?? \_\LIB_NAME_DEFAULT).'/.start.php')){
    return include $f;
} else {
    return (function(){
        try {
            global $_;
            $_['LIB_TYPE'] ??= 'epx__250718_01__lib';
            $_['LIB_NAME'] ??= \_\LIB_NAME_DEFAULT;
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
    })() ;
}