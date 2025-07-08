<?php 

final class epx__250706_01_start__pax__klude_org__github {
    
    public static function _(){
        
        if(\defined('_\SIG_START')){
            return;
        }
        
        global $_;
        global $_ALT;
        global $_TRACE;
        (isset($_) && \is_array($_)) OR $_ = [];
        (isset($_ALT) && \is_array($_ALT)) OR $_ALT = [];
        (isset($_TRACE) && \is_array($_TRACE)) OR $_TRACE = [];
        
        \define('_\SIG_START', \_\MSTART);
        \define('_\CWD', \str_replace('\\','/', \getcwd()));
        \define('_\ABACA_DIR', \_\START_DIR.'/abaca');
        \define('_\SCRATCH_DIR', \dirname(\_\START_DIR).'/.local');
        \define('_\OB_OUT', \ob_get_level());
        !empty($_SERVER['HTTP_HOST']) AND \ob_start();
        \define('_\OB_TOP', \ob_get_level());
        \define('_\REGEX_CLASS_FQN', "/^(([a-zA-Z_\x80-\xff][\\\\a-zA-Z0-9_\x80-\xff]*)\\\)?([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)$/");
        \define('_\REGEX_CLASS_QN', "^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$");
        \define('_\BR', '<br>');
        \define('_\PHP_TSP_DEFAULTS', [
            'handler' => 'spl_autoload',
            'extensions' => \spl_autoload_extensions(),
            'path' =>  \get_include_path(),
        ]);

        #region DX
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
        #endregion
        #region TSP 
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
        #endregion
        #region CONFIG 
        
        \define('_\INCP_DIR', \str_replace('\\','/',\realpath(\dirname($_SERVER['SCRIPT_FILENAME']))));
        \define('_\SITE_DIR', empty($_SERVER['HTTP_HOST'])
            ? \str_replace('\\','/',\realpath($_SERVER['FX__SITE_DIR'] ?? \getcwd()))
            : \str_replace('\\','/',\realpath(\dirname($_SERVER['SCRIPT_FILENAME'])))
        );
        \define('_\INTFC', $INTFC =
            $GLOBALS['INTFC']
            ?? (empty($_SERVER['HTTP_HOST']) 
                ? 'cli'
                : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
            )
        );
        \define('_\IS_CLI', (\_\INTFC === 'cli'));
        \define('_\IS_WEB', (\_\INTFC === 'web'));
        \define('_\IS_HTTP', (\_\INTFC !== 'cli'));
        \define('_\IS_API', (!\_\IS_CLI && !\_\IS_WEB));
        \define('_\KEY', \md5($_SERVER['SCRIPT_FILENAME']));
        if(!empty($_SERVER['HTTP_HOST'])){
            \define('_\ROOT_DIR', \str_replace('\\','/', \realpath($_SERVER['DOCUMENT_ROOT'])));
            \define('_\ROOT_URL', function(){
                return (($_SERVER["REQUEST_SCHEME"] 
                    ?? ((\strtolower(($_SERVER['HTTPS'] ?? 'off') ?: 'off') === 'off') ? 'http' : 'https'))
                ).'://'.$_SERVER["HTTP_HOST"];
            });
        } else {
            if($root = (function(){
                for (
                    $i=0, $dx=\getcwd(); 
                    $dx && $i < 20 ; 
                    $i++, $dx = (\strchr($dx, DIRECTORY_SEPARATOR) != DIRECTORY_SEPARATOR) ? \dirname($dx) : null
                ){ 
                    if(\is_file($f = "{$dx}/http-root-info.json")){
                        if($root = \json_decode(\file_get_contents($f))){
                            return $root;
                        }
                    }
                }
            })()){
                \define('_\ROOT_DIR', $root->dir);
                \define('_\ROOT_URL', $root->url);
            };
        }
        \define('_\LOCAL_DIR', \_\INCP_DIR.'/.local');
        \define('_\DATA_DIR', \_\LOCAL_DIR.'/data');
        
        global $_;
        global $CFG_MODE; //empty is 'lock', 1 is 'auto', 2 is force, 3 is 'force and test';
        (isset($_) && \is_array($_)) OR $_ = [];
        (isset($GLOBALS['_TRACE']) && \is_array($GLOBALS['_TRACE'])) OR $GLOBALS['_TRACE'] = [];
        $intfc = \_\INTFC;
        $cfg_cache_t = \is_file($cfg_cache_f = \_\LOCAL_DIR."/.config-cache-{$intfc}.php") ? \filemtime($cfg_cache_f) : 0;
        if($CFG_MODE || !$cfg_cache_t){
            $my_class = static::class;
            if(!\file_exists($f = \_\START_DIR."/.lib-config.php")){
                \file_put_contents(
                    $f, 
                    \file_get_contents(
                        "https://raw.githubusercontent.com/klude-org/epx-pax/main/plugins/{$my_class}/.lib-config.php"
                    )
                );
                include $f;
            }
            $cfg_parts = [
                \_\START_DIR."/.lib-config.php" => 1,
                \_\START_DIR."/.lib-config-{$intfc}.php" => 1,
                \_\INCP_DIR."/.config.php" => 1,
                \_\INCP_DIR."/.config-{$intfc}.php" => 1,
                \_\LOCAL_DIR."/.config.php" => 1,
                \_\LOCAL_DIR."/.config-{$intfc}.php" => 1,
                \_\INCP_DIR."/index.php" => 0,
                __FILE__ => 0,
            ];
            if(!($build = ($CFG_MODE > 1))){
                foreach($cfg_parts as $k => $v){
                    if(\is_file($k) && $cfg_cache_t < \filemtime($k)){
                        $build = true;
                        break;
                    }
                }
            }
            if($build){
                (function(){
                    $_ = [];
                    $this->dl = \_\INCP_DIR.'/--epx';
                    $this->ds = \_\START_DIR;
                    $this->trace = \str_pad("# ",80,"#").PHP_EOL;
                    foreach($this->cfg_parts as $k => $v){
                        if($v && \is_file($k)){
                            include $k;
                            $this->trace .= "# Included Config: '".\str_replace('\\','/', $k)."'".PHP_EOL;
                        }
                    }
                    $this->_ = $_;
                    $this->_['LSP'] = \array_filter(\iterator_to_array((function(){
                        yield $this->dl => true;
                        $this->trace .= "# Library directory included: '{$this->dl}'".PHP_EOL;
                        foreach($this->_['LIBRARIES'] ?? [] as $dx => $en){
                            $dx = \str_replace('\\','/', $dx);
                            if($en){
                                if(\is_dir($dx)){
                                    $this->trace .= "# Library directory included: '{$dx}'".PHP_EOL;
                                    yield \str_replace('\\','/', \realpath($dx)) => true;
                                } else {
                                    $this->trace .= "# Library directory not found: '{$dx}'".PHP_EOL;
                                }
                            } else {
                                $this->trace .= "# Library directory disabled: '{$dx}'".PHP_EOL;
                            }
                        }
                        if($this->ds != $this->dl){
                            yield $this->ds => true;
                            $this->trace .= "# Library directory included: '{$this->ds}'".PHP_EOL;
                        }
                    })()));
                    $this->_['TSP']['PATH'] = \implode(PATH_SEPARATOR, $this->_['TSP']['LIST'] = \array_keys(\array_filter(\iterator_to_array((function(){
                        $modules = [];
                        foreach($this->_['MODULES'] ?? [] as $k => $v){
                            $modules[\str_replace('\\','/', $k)] = $v ? true : false;
                        }
                        foreach(\explode(PATH_SEPARATOR,\get_include_path()) as $v){
                            $modules[\str_replace('\\','/', $v)] ??= true;
                        }
                        foreach($modules as $m => $en){
                            if($en){
                                $found = false;
                                if((($m[0]??'')=='/' || ($m[1]??'')==':')){
                                    $found = \is_dir($m);
                                    $d = $m;
                                } else if(\str_starts_with($m,'epx__')) {
                                    if(\class_exists($m)){
                                        $d = \dirname((new \ReflectionClass($m))->getFileName());
                                        $found = true;
                                    }
                                } else {
                                    foreach($this->_['LSP'] as $lk => $lv){
                                        if(\file_exists($d = "{$lk}/{$m}")){
                                            $found = true;
                                            break;
                                        }
                                    }
                                }
                                if($found){
                                    yield \str_replace('\\','/', $d) => true;
                                    $this->trace .= "# Module Included '{$m}': '{$d}'".PHP_EOL; 
                                } else {
                                    $this->trace .= "# Module Failed!! '{$m}'".PHP_EOL;
                                }
                            } else {
                                $this->trace .= "# Module Disabled '{$m}'".PHP_EOL;
                            }
                        }
                    })()))));
                    $com = '';
                    foreach($_ as $k => $v){
                        if($w = $v['#'] ?? null){
                            $k = \trim($k,'\\');
                            $w = \trim($w,'\\');
                            if(\str_ends_with($w, "-#.php")){
                                if((($w[0]??'')=='/' || ($w[1]??'')==':')){
                                    $com.=  "namespace { \$_ALT[\\{$k}::class] = '{$w}'; }".PHP_EOL;
                                } else if(\str_starts_with($w,'https://')){
                                    $com.=  "namespace { \$_ALT[\\{$k}::class] = function(){ o()->alt?->load(\\{$k}::class,'{$w}'); }; }".PHP_EOL;
                                }
                            } else if(\str_ends_with($w,".zip")){
                                $com.=  "namespace { \$_ALT[\\{$k}::class] = function(){ o()->alt?->load(\\{$k}::class,'{$w}'); }; }".PHP_EOL;
                            } else if(\preg_match(\_\REGEX_CLASS_FQN, $w) && \preg_match(\_\REGEX_CLASS_FQN, $k, $m)){
                                $d = (\is_array($a = $v['#config'] ?? null)) ? \var_export($a) : '';
                                if($m[2]){
                                    $com.=  "namespace {$m[2]} { \$_ALT[\\{$k}::class] = function(){ final class {$m[3]} extends \\{$w} { {$d} } }; }".PHP_EOL;
                                } else {
                                    $com.=  "namespace { \$_ALT[\\{$k}::class] = function(){ final class {$m[3]} extends \\{$w} { {$d} } }; }".PHP_EOL;
                                }
                            } else {
                                $this->trace .= "# Component '{$k}': FAILED: Invalid Expression".PHP_EOL;
                            }
                        } else if($w = $v['@'] ?? null){
                            $w = \trim($w,'\\');
                            $com.=  "namespace { \$_ALT[\\{$k}::class] = function(){ \class_alias(\\{$w}::class, \\{$k}::class); }; }".PHP_EOL;
                        } else {
                            //* do nothing
                        }
                    }
                    $cfg = \str_replace("\n","\n  ", \var_export($this->_,true));
                    $trace = $this->trace;
                    $stamp = "# ".\date('Y-md-Hi-s').PHP_EOL;
                    $contents = <<<PHP
                    <?php namespace {
                    \$_ = {$cfg};
                    }
                    {$com}
                    namespace { return \$_; };
                    {$trace}
                    {$stamp}
                    PHP;
                    \is_dir($d = \dirname($this->cfg_cache_f)) OR \mkdir($d,0777,true);
                    \file_put_contents(
                        $this->cfg_cache_f, 
                        $contents, 
                        LOCK_EX // prevents race when testing and you have ton of simultaneous requests
                    );
                })->bindTo((object)[
                   'cfg_parts' => $cfg_parts,
                   'cfg_cache_f' => $cfg_cache_f
                ])();
            }
        }

        if($CFG_MODE === 3){ exit(); }
        $GLOBALS['_TRACE'][] = "Loading Cache Config: {$cfg_cache_f}";
        try{
            // prevents race when testing and you have ton of simultaneous requests
            $handle = fopen($cfg_cache_f, 'r');
            if (flock($handle, LOCK_SH)) {
                try {
                    include $cfg_cache_f;
                } finally {
                    \flock($handle, LOCK_UN);
                }
            } else {
                throw new \Exception("Cache error");
            }
        } finally {
            fclose($handle);
        }
        $_ENV = \array_merge($_ENV, $_); //$_ENV holds higher preceedence here
        $_ =& $_ENV;
        1 AND \set_include_path($_['TSP']['PATH']);
        0 AND \spl_autoload_extensions($_['TSP']['EXTENSIONS'] ?? null ?: '-#.php,/-#.php');
        0 AND \spl_autoload_register();
        $p = \get_include_path();
        if(isset($_ALT)){
            \spl_autoload_register(function($n) use($_ALT){ 
                if(\is_callable($f = $_ALT[$n] ?? null)){
                    ($f)();
                } else if(\is_string($f) && \is_file($f)){
                    include $f;
                }
            },true,false);
        }
        $GLOBALS['_TRACE'][] = "TSP: ".\get_include_path();
        #endregion
        #region LAUNCH
        
        return \_::_()->route();        
        
        #endregion
    }    

}

