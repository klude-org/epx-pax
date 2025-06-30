<?php

final class epx__std_config__pax__klude_org__github extends \stdClass {
    
    public static function _() { static $i;  return $i ?: ($i = new static()); }
    
    private function __construct(){
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
        
        global $_;
        global $CFG_MODE; //empty is 'lock', 1 is 'auto', 2 is force, 3 is 'force and test';
        (isset($_) && \is_array($_)) OR $_ = [];
        (isset($GLOBALS['_TRACE']) && \is_array($GLOBALS['_TRACE'])) OR $GLOBALS['_TRACE'] = [];
        $intfc = \_\INTFC;
        $this->cfg_cache_t = \is_file($this->cfg_cache_f = \_\LOCAL_DIR."/.config-cache-{$intfc}.php") ? \filemtime($this->cfg_cache_f) : 0;
        if($CFG_MODE || !$this->cfg_cache_t){
            $this->ds = \_\START_DIR;
            $this->dl = \_\INCP_DIR.'/--epx';
            $this->cfg_parts = [
                \_\START_DIR."/.lib-config.php" => 1,
                \_\START_DIR."/.lib-config-{$intfc}.php" => 1,
                \_\INCP_DIR."/.config.php" => 1,
                \_\INCP_DIR."/.config-{$intfc}.php" => 1,
                \_\LOCAL_DIR."/.config.php" => 1,
                \_\LOCAL_DIR."/.config-{$intfc}.php" => 1,
                \_\INCP_DIR."/index.php" => 0,
            ];
            if(!($build = ($CFG_MODE > 1))){
                foreach($this->cfg_parts as $k => $v){
                    if(\is_file($k) && $this->cfg_cache_t < \filemtime($k)){
                        $build = true;
                        break;
                    }
                }
            }
            if($build){
                (function(){
                    $_ = [];
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
                        $this->trace .= "# Library directory included: '".\str_replace('\\','/', $this->dl)."'".PHP_EOL;
                        foreach($this->_['LIBRARIES'] ?? [] as $dx => $en){
                            if($en){
                                if(\is_dir($dx)){
                                    $this->trace .= "# Library directory included: '".\str_replace('\\','/', $dx)."'".PHP_EOL;
                                    yield \str_replace('\\','/', \realpath($dx)) => true;
                                } else {
                                    $this->trace .= "# Library directory not found: '".\str_replace('\\','/', $dx)."'".PHP_EOL;
                                }
                            } else {
                                $this->trace .= "# Library directory disabled: '".\str_replace('\\','/', $dx)."'".PHP_EOL;
                            }
                        }
                        if($this->ds != $this->dl){
                            yield $this->ds => true;
                            $this->trace .= "# Library directory included: '".\str_replace('\\','/', $this->ds)."'".PHP_EOL;
                        }
                    })()));
                    $this->_['TSP']['PATH'] = \implode(PATH_SEPARATOR, $this->_['TSP']['LIST'] = \array_keys(\array_filter(\iterator_to_array((function(){
                        foreach($this->_['MODULES'] ?? [] as $m => $en){
                            foreach($this->_['LSP'] as $lk => $lv){
                                if($lv){
                                    if(\file_exists($d = "{$lk}/{$m}")){
                                        $this->trace .= "# Module '{$m}': Included {$d}".PHP_EOL; 
                                        yield \str_replace('\\','/', $d) => true;
                                        break;
                                    } else {
                                        $this->trace .= "# Module '{$m}': FAILED: '{$lk}/{$m}'".PHP_EOL;
                                    }
                                } else {
                                    $this->trace .= "# Module '{$m}': Disabled: '{$lk}/{$m}'".PHP_EOL;
                                }
                            }
                        }
                        yield \_\ABACA_DIR => true;
                        yield \_\PLUGIN_DIR => true;
                        yield \_\VND_DIR => true;
                        foreach(\explode(PATH_SEPARATOR,\get_include_path()) as $v){
                            yield \str_replace('\\','/', $v) => true;
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
                })();
            }
        }

        if($CFG_MODE === 2){ exit(); }
        $GLOBALS['_TRACE'][] = "Loading Cache Config: {$this->cfg_cache_f}";
        try{
            // prevents race when testing and you have ton of simultaneous requests
            $handle = fopen($this->cfg_cache_f, 'r');
            if (flock($handle, LOCK_SH)) {
                try {
                    include $this->cfg_cache_f;
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
    }

}