<?php namespace _;

abstract class env extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    const COM_ROOT = \_\o::class;

    public readonly object $route;
    public array $_;
    
    public static function _(){ 
        static $I; return $I ?? ($I = (__CLASS__."\\intfc\\".\_\INTFC)::_()); //to avoid hh search
    }
    
    protected function __construct(){ 
        $this->_ = $_ENV;
        $_ENV = $this;
        $this->route = (object)[];
        $this->start = new \DateTime(\date('Y-m-d H:i:s.'.\sprintf("%06d",(\_\MSTART-floor(\_\MSTART))*1000000), (int)\_\MSTART));
        $this->intfc = \_\REQ['intfc'];
        $this->intfx = \_\REQ['intfc'];
        $this->urp = \_\REQ['urp'];
        $this->rurp = \_\REQ['rurp'];
        $this->site_urp = \_\REQ['site_urp'];
        $this->rpath = \_\REQ['rpath'];
    }
    
    public function __get($n){
        return ($this->{$k = \strtolower($n)} = 
            (
                \class_exists($c = static::class.'\\'.$k)
                || \class_exists($c = __CLASS__.'\\intfc_common\\'.$k)
            ) 
            ? $c::_($this)
            : false
        ) ?: null;
    }
    
    public function offsetSet($n, $v):void { 
        throw new \Exception('Set-Accessor is not supported for class '.static::class);
    }
    public function offsetExists($n):bool { 
        return \array_key_exists($n, $this->_) ||  $this->offsetGet($n);
    }
    public function offsetUnset($n):void { 
        throw new \Exception('Unset-Accessor is not supported for class '.static::class);
    }
    public function offsetGet($n):mixed { 
        if(!\array_key_exists($n, $this->_)){
            $k = "FW_{$n}";
            $this->_[$n] = 
                (\defined($k) ? \constant($k) : null)
                ?? ((($r = \getenv($k)) !== false) ? $r : null)
                ?? $_SERVER[$k]
                ?? $_SERVER["REDIRECT_{$k}"]
                ?? $_SERVER["REDIRECT_REDIRECT_{$k}"]
                ?? null
            ;
        }
        return $this->_[$n] ?? null;
    }
    
    public function jsonSerialize():mixed {
        return $this->_;
    }
    
    public function component($n){
        if(\class_exists($class = \str_replace('/','\\', static::COM_ROOT."\\{$n}"))){
            $o = $class::_();
            return $o;
        } else {
            return false;
        }
    }
    
    private function i__auto_init($type){
        foreach($_ENV['AUTO_INITS'][\_\INTFC][$type] ?? [] as $k => $v){
            if(\is_numeric($k)){
                o()->$v;
            } else {
                $v && o()->$k;
            }
        }
    }
    
    private function i__resolve_file($path, $suffix){
        if($f = (($suffix)
            ? \stream_resolve_include_path($r[] = "{$path}/{$suffix}") 
                ?: (\stream_resolve_include_path($r[] = "{$path}{$suffix}")
            )
            : \stream_resolve_include_path($r[] = "{$path}")
        )){
            return \_\i\file::_($f);
        }
    }
    
    private function i__resolve_class_file($base_class, $path, $suffix){
        if($class = \get_parent_class($base_class)){
            do{
                $j = \str_replace('\\','/', $class).($path ? '/' : '');
                if($f = $this->i__resolve_file("{$j}{$path}",$suffix)){
                    return $f;
                }
            } while($class = \get_parent_class($class));
        }
    }

    
    private function i__resolve_sub_class($base_class, $sub_path){
        if($class = \get_parent_class($base_class)){
            do{
                if(\class_exists($c = \str_replace('/','\\', "{$class}\\{$sub_path}"))){
                    return $c;
                }
            } while($class = \get_parent_class($class));
        }
    }
    
    public function route(){
        if($dispatch = $this->auth->route()){
            return $dispatch;
        }
        $panel = \_\REQ['panel'];
        $rpath = \_\REQ['rpath'];
        $intfx = \_\REQ['intfx'];
        $com_root = static::COM_ROOT;
        $__CTLR_FILE__ = null;
        $__ENV__ = $this;
        $this->i__auto_init('PRE_ROUTE');

        if(!$rpath){
            if($f = $this->i__resolve_file("{$panel}", "-@{$intfx}.php")){
                $__CTLR_FILE__ = $f;
                $__ENV__ = $this;
                $__CONTEXT__ = function(){
                    return $this;
                };
            } else if(\class_exists($panel)){
                $com_class = $panel;
                $context_path = "console";
                $suffix = "-@{$intfx}.php";
                $context_args = [];
                if($context_class = $this->i__resolve_sub_class($com_class, $context_path)){
                    if($f = $this->i__resolve_class_file($com_class, $context_path, $suffix)){
                        $__CTLR_FILE__ = $f;
                        $__CONTEXT__ = function() use($context_class, $context_args, $com_class){
                            return $context_class::_($com_class::_(), ...$context_args);
                        };
                    }
                }
            }
        } else {
            if($f = $this->i__resolve_file("{$panel}/{$rpath}", "-@{$intfx}.php")){
                $__CTLR_FILE__ = $f;
                $__ENV__ = $this;
                $__CONTEXT__ = function(){
                    return $this;
                };
            } else if(
                \preg_match("#^(?<p>(?<a>[^@]*)(?:(?<b>@(?<c>[^/]*))(?<d>.*))?)$#", $rpath, $m)
                && \class_exists($com_class = \str_replace('/','\\', $com_path = "{$com_root}/".\trim($m['a'],'/')))
            ){
                if($m['b'] ?? null){
                    $index = \trim($m['c'],'/');
                    if($index === ''){
                        $context_path = "pool";
                        $context_args = [];
                    } else {
                        $context_path = "item";
                        $context_args = [$index];
                    }
                } else {
                    $context_path = "entity";
                    $context_args = [];
                }
                
                if($context_class = $this->i__resolve_sub_class($com_class, "{$context_path}")){
                    $path = implode('/',\array_map(fn($k) => \trim($k,'/'), [$panel, $context_path, $m['d'] ?? '']));
                    $suffix = "-@{$intfx}.php";
                    if($f = $this->i__resolve_class_file($com_class, $path, $suffix)){
                        $__CTLR_FILE__ = $f;
                        $__CONTEXT__ = function() use($context_class, $context_args, $com_class){
                            return $context_class::_($com_class::_(), ...$context_args);
                        };
                    }
                }
            }
        }
         
        if($__CTLR_FILE__ instanceof \SplFileInfo){
            $__ENV__ = $this;
            $this->i__init();
            \define('_\CTLR_URL', $this->ctlr_url = $this->base_url.$rpath);
            $this->panel = \class_exists(\_\REQ['panel'])
                ? $panel::_()
                : (static::class."\\panel")::_()
            ;            
            return (function() use($__CTLR_FILE__, $__ENV__){
                $tsp = \explode(PATH_SEPARATOR,get_include_path());
                foreach($tsp as $d){
                    \is_file($f = "{$d}/.functions.php") AND include_once $f;
                }
                foreach(\array_reverse($tsp) as $d){
                    \is_file($f = "{$d}/.module.php") AND include_once $f;
                }
                if(\is_callable($o = (include $__CTLR_FILE__))){
                    $__ENV__->controller = $o;
                    ($o)($_REQUEST->ctrl_args ?? null);
                }
            })->bindTo($__CONTEXT__());
        }        
        
        return function(){
            \http_response_code(404);
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
            exit("404 Not Found: ".$this->rurp);
        };
    }
}