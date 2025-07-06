<?php 

final class epx__start_0__pax__klude_org__github {
    
    public static function _(){
        
        \defined('_\MSTART') OR \define('_\MSTART', \microtime(true));
        \define('_\CWD', \str_replace('\\','/', \getcwd()));
        \define('_\START_FILE', \str_replace('\\','/', __FILE__));
        \define('_\START_DIR', \dirname(\_\START_FILE,2));
        \define('_\ABACA_DIR', \_\START_DIR.'/abaca');
        \define('_\SCRATCH_DIR', \dirname(\_\START_DIR).'/.local');
        \define('_\OB_OUT', \ob_get_level());
        !empty($_SERVER['HTTP_HOST']) AND \ob_start();
        \define('_\OB_TOP', \ob_get_level());
        \define('_\REGEX_CLASS_FQN', "/^(([a-zA-Z_\x80-\xff][\\\\a-zA-Z0-9_\x80-\xff]*)\\\)?([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]+)$/");
        \define('_\REGEX_CLASS_QN', "^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$");
        \define('_\BR', '<br>');
        \define('_\PHP_TSP_DEFAULTS', [
            'handler' => 'spl_autoload',
            'extensions' => \spl_autoload_extensions(),
            'path' =>  \get_include_path(),
        ]);
        1 AND \set_include_path(
            (\is_dir($d = \_\ABACA_DIR) ? $d.PATH_SEPARATOR : '')
            .\_\START_DIR.PATH_SEPARATOR
            .\get_include_path());
        1 AND \spl_autoload_extensions("-#.php,/-#.php");
        1 AND \spl_autoload_register();
        1 AND \spl_autoload_register($autoload_fn = function($n) use(&$autoload_fn) { 
            global $_ALT;
            global $_;
            if(\is_callable($alt = $_ALT[$n] ?? null)){
                ($alt)();
            } else if(\is_string($alt) && \str_starts_with($alt, '/')){
                return include $alt;
            } else if(\preg_match(
                "#^(?<w_plugin>epx__(?<w_partno>.+)__(?<w_repo>.+)__(?<w_owner>.+)__(?<w_domain>[^/]+))(?<w_sub>/[^/]+)?#",
                $p = \str_replace('\\','/', $n),
                $m
            )){
                \extract($mx = \array_filter($m, fn($k) => !is_numeric($k), \ARRAY_FILTER_USE_KEY)); 
                $alt_version = ($a = $_[$m['w_plugin']]['@alt']['version'] ?? null) ? "-{$a}" : "";
                $m['w_sub'] ??= "";
                if(!($plugin_path = \stream_resolve_include_path("{$m['w_plugin']}{$alt_version}/-#.php"))){
                    $w_owner = \str_replace('_','-',$w_owner);
                    $w_repo = "epx-".\str_replace('_','-',$w_repo);
                    $u_path = "{$w_plugin}{$alt_version}.zip";
                    $url = match($w_domain){
                        'github' => "https://raw.githubusercontent.com/{$w_owner}/{$w_repo}/main/plugins/{$u_path}",
                        'epx' => "https://epx-modules.neocloud.com.au/{$w_owner}/{$w_repo}/live/{$u_path}", 
                    };
                    $api_token = $_[\_\api_tokens::class]['$']["{$w_domain}/{$w_owner}/{$w_repo}"]['tsp'] ?? null;
                    $payload = $api_token
                        ? [
                            $url,
                            true,
                            [
                                "http" => [
                                    "method" => "GET",
                                    "header" => "Authorization: token $api_token\r\n"
                                ]
                            ]
                        ] : [
                            $url
                        ]
                    ;
                    if(!($contents = \file_get_contents(...$payload))){
                        throw new \Exception("Failed: Unable to download plugin '{$w_plugin}' for type '{$n}'");
                    }
                    $zip_path = \_\SCRATCH_DIR."/temp/https-".\str_replace('/','~', \substr(\strtok($url,'?'),8));
                    \is_dir($d = \dirname($zip_path)) OR @mkdir($d,0777,true);
                    \file_put_contents($zip_path, $contents);
                    if(\is_file($zip_path)){
                        try {
                            if (!(($zip = new \ZipArchive)->open($zip_path) === true)) {
                                throw new \Exception("Failed: Plugin is corrupted");
                            }
                            if(\is_dir($m_dir = \_\START_DIR.'/'.$w_plugin)){
                                foreach(new \RecursiveIteratorIterator(
                                    new \RecursiveDirectoryIterator($m_dir, \RecursiveDirectoryIterator::SKIP_DOTS)
                                    , \RecursiveIteratorIterator::CHILD_FIRST
                                ) as $f) {
                                    if ($f->isDir()){
                                        rmdir($f->getRealPath());
                                    } else {
                                        unlink($f->getRealPath());
                                    }
                                }
                                rmdir($m_dir);
                            }
                            $m_offset = $options['offset'] ?? null ?: '';
                            $extracted = false;
                            $zip_offset = \substr($s = $zip->getNameIndex(0), 0, \strpos($s, '/'));
                            $sub_offset = \rtrim("{$zip_offset}/{$m_offset}",'/');
                            for ($i = 0; $i < $zip->numFiles; $i++) {
                                $fileName = $zip->getNameIndex($i);
                                if (\str_starts_with($fileName, $sub_offset)) {
                                    $target_path = $m_dir.substr($fileName, strlen($sub_offset));
                                    if (str_ends_with($fileName, '/')) {
                                        is_dir($target_path) OR @mkdir($target_path, 0777, true);
                                    } else {
                                        is_dir($d = dirname($target_path)) OR @mkdir($d, 0777, true);
                                        file_put_contents($target_path, $zip->getFromIndex($i));
                                    }
                                    $extracted = true;
                                }
                            }
                            if(!$extracted){
                                throw new \Exception("Failed: Library couldn't be extracted");
                            }
                        } finally {
                            $zip->close();
                            1 AND \unlink($zip_path);
                        }
                    }
                }
                
                if(
                    ($n_path = \stream_resolve_include_path("{$w_plugin}{$alt_version}{$m['w_sub']}/-#.php"))
                    || ($n_path = \stream_resolve_include_path("{$w_plugin}{$alt_version}{$m['w_sub']}-#.php"))
                ){
                    include $n_path;
                }
            }
        },true,false);

        1 AND \ini_set('display_errors', 0);
        1 AND \ini_set('display_startup_errors', 1);
        1 AND \ini_set('error_reporting', E_ALL);
        0 AND \error_reporting(E_ALL);
        $fault__fn = function($ex = null){
            $FAULTS[\microtime(true).':'.\uniqid()] = $ex;
            $intfc = $GLOBALS['INTFC']
                ?? (empty($_SERVER['HTTP_HOST']) 
                    ? 'cli'
                    : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
                )
            ;
            switch($intfc){
                case 'cli':{
                    echo "\033[91m\n"
                        .$ex::class.": {$ex->getMessage()}\n"
                        ."File: {$ex->getFile()}\n"
                        ."Line: {$ex->getLine()}\n"
                        ."\033[31m{$ex}\033[0m\n"
                    ;
                } break;
                case 'web':{
                    \http_response_code(500);
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                    exit(<<<HTML
                        <pre style="overflow:auto; color:red;border:1px solid red;padding:5px;">{$ex}</pre>
                    HTML);
                } break;
                default:{
                    \http_response_code(500);
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                    \header('Content-Type: application/json');
                    exit(\json_encode([
                        'status' => "error",
                        'message' => $ex->getMessage(),
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                } break;
            }
        };
        \set_exception_handler(function($ex) use($fault__fn){
            $fault__fn($ex);
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
        
        global $_;
        global $_ALT;
        global $_TRACE;
        (isset($_) && \is_array($_)) OR $_ = [];
        (isset($_ALT) && \is_array($_ALT)) OR $_ALT = [];
        (isset($_TRACE) && \is_array($_TRACE)) OR $_TRACE = [];
        if(\class_exists($class = $_[\_\_::class]['@'] ?? null ?: \epx__std_origin__pax__klude_org__github::class)){
            \class_alias($class, \_\_::class);
            \_\_::_();
        } else if(\class_exists(\_\_::class)){
            \_\_::_();
        } else {
            $INCP_DIR = \dirname($_SERVER['SCRIPT_FILENAME']);
            \is_file($f = $INCP_DIR."/.config.php") AND include $f;
            \is_file($f = $INCP_DIR."/.config-{$INTFC}.php") AND include $f;
            $_ENV = \array_merge($_ENV, $_); //$_ENV holds higher preceedence here
            $_ =& $_ENV;
        }
        
        return \_\_::_()->route();
    }
    
}

