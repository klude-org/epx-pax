<?php namespace _\env\com;

trait component__t {
    
    use \_\i\singleton__t;
    
    const NODE_BASE = 'nodes';

    private $I__NODES = [];
    
    public function name(){
        return o($this);
    }

    public function config($key){
        return o()->env[static::class][$key] ?? null;
    }
    
    public function __get($n){
        return ($this->I__NODES[$k = \strtolower($n)] ?? ($this->I__NODES[$k] = ($node = $this->node($k)) 
            ? $node
            : (function($n){ 
                $GLOBALS['_TRACE'][] = "Node Not Found: '{$n}'";
                return false;
            })($n)
        )) ?: null;
    }
    
    public static function nested_class($path){
        $class = static::class;
        do{
            if(\class_exists($c = "{$class}\\{$path}")){
                return $c;
            }
        } while($class = \get_parent_class($class));
    }
    
    public function node($path,...$args){
        return \_\i\type::_(static::class)?->PARENT?->nest()?->type_hh("{$path}")?->instantiate($this,...$args);
    }
    
    public function file(string $path, string $suffix = null){
        $class = \get_parent_class(static::class);
        do{
            $j = \str_replace('\\','/', $class).($path ? '/' : '');
            if($f = (($suffix)
                ? \stream_resolve_include_path($r[] = "{$j}{$path}/{$suffix}") 
                    ?: (\stream_resolve_include_path($r[] = "{$j}{$path}{$suffix}")
                )
                : \stream_resolve_include_path($r[] = "{$j}{$path}")
            )){
                break;
            } else {
            }
        } while($class = \get_parent_class($class));
        foreach($r ?? [] as $rv){ $GLOBALS['_TRACE'][] = "Searched: {$rv}"; }
        $f AND $GLOBALS['_TRACE'][] = "Found: {$f}";
        return $f ? \_\i\file::_(\str_replace('\\','/', ($f))) : null;
    }
    
    public function glob(string $path, int $flags = 0){
        $class = \get_parent_class(static::class);
        $list = [];
        do{
            $j = \str_replace('\\','/', $class).($path ? '/' : '');
            foreach(\explode(PATH_SEPARATOR, \get_include_path()) as $d){
                $list = \array_merge($list, \glob($x[] = "{$d}/{$j}{$path}", $flags));
            }
        } while($class = \get_parent_class($class));
        return $list;
    }
    
    public function view(string $path = '') {
        $file = $this->file($path,'-v.php');
        return o()->xui->view($file, $this);
    }
    
}