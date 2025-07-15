<?php 
  
if(\defined('_\SIG_START')){
    return;
}
global $_;
global $_ALT;
global $_TRACE;
(isset($_) && \is_array($_)) OR $_ = [];
(isset($_ALT) && \is_array($_ALT)) OR $_ALT = [];
(isset($_TRACE) && \is_array($_TRACE)) OR $_TRACE = [];

\define('_\SIG_START', \_\MSTART); //always master_start!
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
\define('_\START_EN', \is_array($a = $_['START_EN'] ?? null) ? $a : []);

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
    throw new \ErrorException(
        $message, 
        0,
        $severity, 
        $file, 
        $line
    );
});
\define('_\DISABLE_ORIGIN_EXIT_HANDLER', true);
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
#region PLUGINS 
1 AND \set_include_path(
    (\is_dir($d = \_\ABACA_DIR) ? $d.PATH_SEPARATOR : '')
    .\_\START_DIR.PATH_SEPARATOR
    .(\is_dir($d = \_\START_DIR.'/sandbox') ? $d.PATH_SEPARATOR : '')
    .(\is_dir($d = \_\START_DIR.'/.local-plugins') ? $d.PATH_SEPARATOR : '')
    .\get_include_path()
);
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
#region BOOTSTRAP 
\define('_\INCP_DIR', \str_replace('\\','/',\realpath(\dirname($_SERVER['SCRIPT_FILENAME']))));
\define('_\SITE_DIR', empty($_SERVER['HTTP_HOST'])
    ? \str_replace('\\','/',\realpath($_SERVER['FX__SITE_DIR'] ?? \getcwd()))
    : \str_replace('\\','/',\realpath(\dirname($_SERVER['SCRIPT_FILENAME'])))
);
\define('_\LOCAL_DIR', \_\INCP_DIR.'/.local');
\define('_\DATA_DIR', \_\LOCAL_DIR.'/data');

\define('_\INTFC', $INTFC =
    $GLOBALS['INTFC']
    ?? (empty($_SERVER['HTTP_HOST']) 
        ? 'cli'
        : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
    )
);
\define('_\INTFX', (\_\INTFC == 'web') ? '' : \_\INTFC);
\define('_\IS_CLI', (\_\INTFC === 'cli'));
\define('_\IS_WEB', (\_\INTFC === 'web'));
\define('_\IS_HTTP', (\_\INTFC !== 'cli'));
\define('_\IS_API', (!\_\IS_CLI && !\_\IS_WEB));
\define('_\KEY', \md5($_SERVER['SCRIPT_FILENAME']));
if(!empty($_SERVER['HTTP_HOST'])){
    \define('_\ROOT_DIR', \str_replace('\\','/', \realpath($_SERVER['DOCUMENT_ROOT'])));
    \define('_\ROOT_URL', (function(){
        return (($_SERVER["REQUEST_SCHEME"] 
            ?? ((\strtolower(($_SERVER['HTTPS'] ?? 'off') ?: 'off') === 'off') ? 'http' : 'https'))
        ).'://'.$_SERVER["HTTP_HOST"];
    })());
    if(\is_file($f = \_\ROOT_DIR."/.http-root-info.php")){
        include $f;
    }
} else {
    for (
        $i=0, $dx=\getcwd(); 
        $dx && $i < 20 ; 
        $i++, $dx = (\strchr($dx, DIRECTORY_SEPARATOR) != DIRECTORY_SEPARATOR) ? \dirname($dx) : null
    ){ 
        if(\is_file($f = "{$dx}/.http-root-info.php")){
            include $f;
            break;
        }
    }
    \defined('_\ROOT_DIR') OR \define('_\ROOT_DIR', \_\SITE_DIR);
    \defined('_\ROOT_URL') OR \define('_\ROOT_URL', false);
}
\define('_\URP', \strtok($_SERVER['REQUEST_URI'] ?? '','?'));
\define('_\RURP',  (function(){
    if(empty($_SERVER['HTTP_HOST'])){
        if(!\str_starts_with(($s = $_SERVER['argv'][1] ?? ''),'-')){
            return '/'.\ltrim($s,'/');
        }
    } else {
        $p = \rtrim(\strtok($_SERVER['REQUEST_URI'],'?'),'/');
        if((\php_sapi_name() == 'cli-server')){
            return $p;
        } else {
            if((\str_starts_with($p, $n = $_SERVER['SCRIPT_NAME']))){
                return \substr($p,\strlen($n));
            } else if((($d = \dirname($n = $_SERVER['SCRIPT_NAME'])) == DIRECTORY_SEPARATOR)){
                return $p;
            } else {
                return \substr($p,\strlen($d));
            }
        }
    }
})() ?: '/');
\define('_\SITE_URP', (function(){
    if(empty($_SERVER['HTTP_HOST'])){
        if(\_\ROOT_URL){
            if(\str_starts_with(\_\SITE_DIR,\_\ROOT_DIR)){
                return \substr(\_\SITE_DIR, \strlen(\_\ROOT_DIR));
            } else {
                return false;
            }
        }
    } else if((\php_sapi_name() == 'cli-server')){
        return '';
    } else {
        $p = \strtok($_SERVER['REQUEST_URI'],'?');
        if((\str_starts_with($p, $n = $_SERVER['SCRIPT_NAME']))){
            return \substr($p, 0, \strlen($_SERVER['SCRIPT_NAME']));
        } else if((($d = \dirname($n = $_SERVER['SCRIPT_NAME'])) == DIRECTORY_SEPARATOR)){
            return '';
        } else {
            return \substr($p, 0, \strlen($d));
        }
    }
})());
\define('_\SITE_URL', \_\ROOT_URL  ? \rtrim(\_\ROOT_URL.\_\SITE_URP,'/') : false);
if(!\preg_match(
    "#^/"
        ."(?<full>"
            ."(?:"
                ."(?<facet>"
                    ."(?<portal>(?:__|--)[^/\.]*)"
                    ."(?:\.(?<role>[^/]*))?"
                .")/?"
            .")?"
            ."(?<rpath>.*)"
        .")?"
    . "$#",
    \_\RURP,
    $m
)){
    \http_response_code(404);
    exit("404: Not Found: Invalid request path format");
}
\define('_\REQ', \array_replace(
    [
        'intfc' => \_\INTFC,
        'intfx' => \_\INTFX,
        'urp' => \_\URP, // urp
        'rurp' => \_\RURP, //routable urp
        'site_urp' => \_\SITE_URP,
    ], 
    ($parsed = \array_filter($m, fn($k) => !is_numeric($k), ARRAY_FILTER_USE_KEY)),
    [
        'rpath' => \trim($parsed['rpath'], '/'),
        'panel' => \trim(\str_replace('-','_', $parsed['portal'] ?? null ?: '__'),'/'),
    ]
));
#endregion
#region SESSION
(\_\START_EN['session'] ?? true) AND (function(){
    if(empty($_SERVER['HTTP_HOST'])){
        
    } else {
        if(\session_status() == PHP_SESSION_NONE) {
            //* if the primary starter did the session it would have managed the auth
            //* this part will be scipped
            \session_name(\_\KEY); 
            \session_start();
        }
        \define('_\SESSION_PATH', \_\KEY.'/'.\session_id());
        if(\_\START_EN['auth'] ?? true){
            if(($_SESSION['--AUTH']['en'] ?? false) !== true){
                if(!($_SESSION['--AUTH']['login_in_progress'] ?? false)){
                    $_SESSION['--AUTH'] = [];
                    $_SESSION['--AUTH']['login_in_progress'] = 1;
                    \header("Location: ". \strtok($_SERVER['REQUEST_URI'],'?'));
                    exit();
                }
            }
            if(
                isset($_GET['--logout'])
                || isset($_GET['--signout'])
            ){
                $_SESSION['--AUTH'] = [];
                \header("Location: ". \strtok($_SERVER['REQUEST_URI'],'?'));
                exit();
            }
            \is_array($_SESSION['--AUTH'] ?? null) OR $_SESSION['--AUTH'] = [];
        }
        isset($_SESSION['--CSRF']) OR $_SESSION['--CSRF'] = \md5(uniqid('csrf-'));
        \define('_\CSRF', $_SESSION['--CSRF']);
        \define('_\FLASH', $_SESSION['--FLASH'] ?? []);
        $_SESSION['--FLASH'] = [];
        if(\_\START_EN['csrf_protect'] ?? true){
            if($_ENV['SESSION']['CSRF']['EN'] ?? true){
                $token = $_REQUEST['--csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
                if(
                    \in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','PATCH','DELETE'])
                    && ($token) != ($_SESSION['--CSRF'] ?? null)
                ){
                    while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                    \http_response_code(406);
                    exit('406: Not Acceptable');
                }
            }
        }
    }
})();
#endregion
#region REQUEST
(\_\START_EN['request'] ?? true) AND $_REQUEST = (empty($_SERVER['HTTP_HOST']) 
    ? function(){
        $parsed = [];
        $key = null;
        $args = \array_slice($argv = $_SERVER['argv'] ?? [], 1);
        foreach ($args as $arg) {
            if ($key !== null) {
                $parsed[$key] = $arg;
                $key = null;
            } else if(\str_starts_with($arg, '-')){
                if(\str_ends_with($arg, ':')){
                    $key = \substr($arg,0,-1);
                } else if(\str_contains($arg,':')) {
                    [$k, $v] = \explode(':', $arg);
                    $parsed[$k] = $v;
                } else {
                    $parsed[$arg] = true;
                }
            } else {
                $parsed[] = $arg;
            }
        }
        if ($key !== null) {
            $parsed[$key] = true;
        }
        $parsed[0] ??= '/';
        return $parsed;
    }
    : function(){
        global $_;
        $json = [];
        $files = [];
        switch($content_type = \strtok($_SERVER["CONTENT_TYPE"] ?? '',';')){
            case "application/json": {
                $json = (function(){
                    $input = \file_get_contents('php://input');
                    $ox = [];
                    foreach(\json_decode($input, true) as $k => $v){
                        $oy =& $ox;
                        foreach(explode('[',\str_replace("]","", $k)) as $kk){
                            ($oy[$kk] = []);
                            $oy = &$oy[$kk];
                        }
                        $oy = $v;
                    }
                    return $ox;
                })();
            } break;
            case "multipart/form-data": {
                $files = (function(){
                    $o = [];
                    foreach($_FILES as $field => $array){
                        foreach($array as $attrib => $inner){
                            if(\is_array($inner)){
                                foreach(($r__fn = function($array, $pfx = '', $ifx = '[', $sfx = ']') use(&$r__fn){
                                    foreach($array as $k  => $v){
                                        if(\is_array($v)){
                                            yield from ($r__fn)($v,"{$pfx}{$ifx}{$k}{$sfx}",$ifx,$sfx);
                                        } else {
                                            yield "{$pfx}{$ifx}{$k}{$sfx}" => $v;
                                        }
                                    }
                                })($inner,$field) as $k => $v){
                                    $o[$k][$attrib] = $v;
                                }
                            } else {
                                $o[$field][$attrib] = $inner;
                            }
                        }
                    }
                    $ox = [];
                    foreach($o as $k => $v){
                        if(!($v['name'] ?? null)){ continue; }
                        $oy =& $ox;
                        foreach(explode('[',\str_replace("]","", $k)) as $kk){
                            isset($oy[$kk]) OR $oy[$kk] = [];
                            $oy = &$oy[$kk];
                        }
                        $oy =  new class($v) extends \SplFileInfo implements \JsonSerializable {
                            public readonly array $info;
                            public function __construct($v){
                                $this->info = $v; 
                                parent::__construct($v['tmp_name']);
                            }
                            public function info($n){
                                if($n == 'extension'){
                                    return \pathinfo($this->details['name'] ?? '', PATHINFO_EXTENSION);
                                } else {
                                    return $this->details[$n] ?? null;
                                }
                            }
                            public function jsonSerialize(): mixed {
                                return "--file::".$this->getRealPath();
                            }
                            public function f(){
                                return \_\i\file::_((string) $this, $this->INFO);
                            }
                            public function move_to($path){
                                \is_dir($d = \dirname($path)) OR \mkdir($d,0777,true);
                                if(\move_uploaded_file($this, $path)){
                                    return new \SplFileInfo($path);
                                } else {
                                    return false;
                                }
                            }
                        };
                    }
                    return $ox;
                })();
            } break;
            case "application/x-www-form-urlencoded": 
            default:{
                //* do nothing
            } break;
        }
        
        // $_FILES = $files;
        //! warning: array_merge_recursive messes up if $_FILES and $_POST have same key
        return \array_replace_recursive(
            $_POST, 
            $_FILES, //* $_FILES is higher priority over $_POST
            $json,
            $_GET,
        );
    }
)();
#endregion
#region CONFIG
global $_;
$CFG_MODE = (\_\START_EN['cfg_mode'] ?? 1); //empty is 'lock', 1 is 'auto', 2 is force, 3 is 'force and test';
(isset($_) && \is_array($_)) OR $_ = [];
(isset($GLOBALS['_TRACE']) && \is_array($GLOBALS['_TRACE'])) OR $GLOBALS['_TRACE'] = [];
$intfc = \_\INTFC;
$cfg_cache_t = \is_file($cfg_cache_f = \_\LOCAL_DIR."/.config-cache-{$intfc}.php") ? \filemtime($cfg_cache_f) : 0;
if($CFG_MODE || !$cfg_cache_t){
    $my_class = \basename(__DIR__);
    if(!\file_exists($f = \_\START_DIR."/.lib-config.php")){
        if(\is_file($fsource = __DIR__."/.lib-config.php")){
            \copy($fsource, $f);
        }
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
//$_ENV holds higher preceedence here
$_ENV = \array_merge(
    $_ENV, 
    ((\_\START_EN['session_config'] ?? true) ? ($_SESSION['_'] ?? []) : []),
    $_,
); 
$_ =& $_ENV;
//* Default is 'Australia/Adelaide' because thats where epx-php was invented.
1 AND !\is_null($v = $_ENV['PHP']['timezone'] ?? 'Australia/Adelaide') AND \date_default_timezone_set($v);
1 AND !\is_null($v = $_ENV['PHP']['timelimit'] ?? null) AND \set_time_limit($v);
1 AND !\is_null($v = $_ENV['PHP']['display_errors'] ?? null) AND \ini_set('display_errors', $v);
1 AND !\is_null($v = $_ENV['PHP']['display_startup_errors'] ?? null) AND \ini_set('display_startup_errors', $v);
1 AND !\is_null($v = $_ENV['PHP']['error_reporting'] ?? null) AND \ini_set('error_reporting', $v);
0 AND \error_reporting(E_ALL);
1 AND \defined('_\DBG') OR \define('_\DBG', (int) (
    ($_ENV['DBG']['REQUEST_TRIGGER_EN'] ?? null) ? $_REQUEST['--debug'] : null)
    ?? ($_ENV['DBG']['EN'] ?? null)
    ?? 0
);
1 AND \defined('_\DBG_') OR \define('_\DBG_',[
    0 => \_\DBG >= 0,
    1 => \_\DBG >= 1,
    2 => \_\DBG >= 2,
    3 => \_\DBG >= 3,
    4 => \_\DBG >= 4,
    5 => \_\DBG >= 5,
    6 => \_\DBG >= 6,
    7 => \_\DBG >= 7,
    8 => \_\DBG >= 8,
    9 => \_\DBG >= 9,
]);
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


