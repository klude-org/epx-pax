<?php

final class _ extends \stdClass {
    
    public const COM_ROOT = '_/com';
    
    public static function _() { static $i;  return $i ?: ($i = new static()); }
    
    private function __construct(){ 
        function o(string|object $n = null){ 
            static $J = [];
            static $K = [];
            if($n === null){
                static $I; return $I ?? $I = \_::_();
            } else if(\is_string($n)){
                return ($J[$n] ?? ((function($n) use(&$J,&$K){
                    if($n){
                        if($o = \_\env::_()->component($n)){
                            $J[$n] = $o;
                            $K[\spl_object_hash($o)] = $n;
                            return $o;
                        } else {
                            return false;
                        }
                    } else{
                        return \_::_();
                    }
                })($n)) ?: null);
            } else {
                return $K[\spl_object_hash($n)] ?? null;
            }
        }
    }
    
    public function __get($n){
        static $N =[];  return ($N[$k = \strtolower($n)] ?? ($N[$k] = \class_exists($c = "_\\{$k}") 
            ? $c::_()
            : (function($n){ 
                $GLOBALS['_TRACE'][] = "Node Not Found: '{$n}'";
                return false;
            })($n)
        )) ?: null;
    }    
    
    public function route(){
        if(\class_exists(\_\env::class)){
            return \_\env::_()->route();
        } else {
            return function(){ echo "Missing ENV"; };
        }
    }
    
    public static function file(string $n, string $suffix = null){
        static $WRAPPER; $WRAPPER OR $WRAPPER = \class_exists(\_\i\file::class)
            ? \_\i\file::class
            : \SplFileInfo::class
        ;
        $p = \str_replace('\\','/', $n);
        if(($p[0]??'')=='/' || ($p[1]??'')==':'){
            $f = \realpath($GLOBALS['_TRACE']['Realpath Resolve'] = $p);
        } else {
            $f = (($suffix)
                ? \stream_resolve_include_path($GLOBALS['_TRACE']['File Resolve'] = "{$p}/{$suffix}") 
                    ?: (\stream_resolve_include_path($GLOBALS['_TRACE']['File Resolve'] = "{$p}{$suffix}")
                )
                : \stream_resolve_include_path($GLOBALS['_TRACE']['File Resolve'] = "{$p}")
            );
        }
        if($f){
            $GLOBALS['_TRACE']['File Found'] = $f = \str_replace('\\','/',$f);
            return new $WRAPPER($f);
        }
    }
    
    public static function glob($p, $flags = 0){
        static $WRAPPER; $WRAPPER OR $WRAPPER = \class_exists(\_\i\file::class)
            ? \_\i\file::class
            : \SplFileInfo::class
        ;
        if(\is_string($p)){
            /* using gxp would not work on files */
            $p = \_\p($p);
            $list = [];
            foreach(\explode(PATH_SEPARATOR, \get_include_path()) as $d){
                foreach(\glob("{$d}/{$p}", $flags) as $f){
                    $list[] = new $WRAPPER($f);
                }
            }
            return $list;
        } else {
            return [];
        }
    }
    
    public static function include(string|array $file, bool|callable $on_default = false):callable {
        if($__FILE__ = \is_string($file) ? static::file($file) : static::file(...$file)){
            return (function (array $__PARAM__) use($__FILE__){
                $__PARAM__ AND \extract($__PARAM__, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p__');
                return include $__FILE__;
            })->bindTo(static::_(), static::class);
        } else {
            if(\is_callable($on_default) ){
                return $on_default;
            } else if($on_default == true){
                return function(){ };
            } else {
                if(\is_array($file)){
                    throw new \Exception("View not found: '{$file[0]}'");
                } else {
                    throw new \Exception("View not found: '{$file}'");
                }
            }
        }
    }
    
    public static function view(string $file, bool|callable $on_default = false):callable {
        if($__FILE__ = static::file($file,'-v.php')){
            return (function ($__INSET__ = null, array $__PARAM__ = null) use($__FILE__){
                if(\is_callable($__INSET__)){ 
                    $__INSET__ = \_\texate($__INSET__);
                } else if($__INSET__ instanceof \SplFileInfo) {
                    $__INSET__ = \_\texate(function() use($__INSET__){ include $__INSET__; });
                } else if(\is_array($__INSET__)) {
                    $__PARAM__ = $__INSET__;
                    $__INSET__ = $__PARAM__[0] ?? '';
                } else if(\is_scalar($__INSET__)) {
                    $__INSET__ = $__INSET__;
                }
                $__PARAM__ AND \extract($__PARAM__, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'p__');
                return include $__FILE__;
            })->bindTo(static::_(), static::class);
        } else {
            if(\is_callable($on_default) ){
                return $on_default;
            } else if($on_default == true){
                return function(){ };
            } else {
                throw new \Exception("View not found: '{$file}'");
            }
        }
    }

}