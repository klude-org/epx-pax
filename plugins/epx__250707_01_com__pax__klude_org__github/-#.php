<?php

class epx__250707_01_com__pax__klude_org__github {
    
    use \_\i\singleton__t;
    
    protected function __construct(){
        \class_alias(__CLASS__.'\\component', \_\i\component::class);
        \class_alias(__CLASS__.'\\component\\nodes\\model', \_\i\component\nodes\model::class);
    }
    
    public function component($n){
        if(\class_exists($class = \str_replace('/','\\', static::class."\\{$n}"))){
            $o = $class::_();
            return $o;
        } else {
            return false;
        }
    }
    
    public function route($request, &$__CTLR_FILE__, &$__CONTEXT__){
        $panel = $request->panel ?? "";
        $rpath = $request->rpath ?? "";
        $intfx = $request->intfx ?? "";
        $com_root = static::class;
        if(
            \preg_match("#^(?<p>(?<a>[^@]*)(?:(?<b>@(?<c>[^/]*))(?<d>.*))?)$#", $rpath, $m)
            && \class_exists($com_class = \str_replace('/','\\', $com_path = "{$com_root}/".\trim($m['a'],'/')))
        ){
            if($m['b'] ?? null){
                $index = \trim($m['c'],'/');
                if($index === ''){
                    $context_path = "access/pool";
                    $context_args = [];
                } else {
                    $context_path = "access/item";
                    $context_args = [$index];
                }
            } else {
                $context_path = "access/entity";
                $context_args = [];
            }
            $context_class = null;
            $class = $com_class;
            do{
                if(\class_exists($c = \str_replace('/','\\', "{$class}\\{$context_path}"))){
                    $context_class = $c;
                    break;
                }
            } while($class = \get_parent_class($class));
            
            if($context_class){
                $path = implode('/',\array_map(fn($k) => \trim($k,'/'), [$context_path, $panel, $m['d'] ?? '']));
                $class = $com_class;
                $suffix = "-@{$intfx}.php";
                do{
                    $j = \str_replace('\\','/', $class).($path ? '/' : '');
                    if($f = (($suffix)
                        ? \stream_resolve_include_path($r[] = "{$j}{$path}/{$suffix}") 
                            ?: (\stream_resolve_include_path($r[] = "{$j}{$path}{$suffix}")
                        )
                        : \stream_resolve_include_path($r[] = "{$j}{$path}")
                    )){
                        $__CTLR_FILE__ = \_\i\file::_($f);
                        $__CONTEXT__ = function() use($context_class, $context_args, $com_class){
                            return $context_class::_($com_class::_(), ...$context_args);
                        };
                        break;
                    } else {
                    }
                } while($class = \get_parent_class($class));
            }
        }
        return ($__CTLR_FILE__) ? true : false;
    }    
    
}