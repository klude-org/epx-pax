<?php

abstract class epx__250706_01_ui__pax__klude_org__github extends \stdClass {
       
    public readonly object $vars;

    public static function _(){ 
        static $I; return $I ?? ($I = (__CLASS__."\\".\_\INTFC)::_()); //to avoid hh search
    }
    
    protected function __construct(){ 
        $this->vars = (object)[];
    }
    
    public function __get($n){
        return ($this->{$k = \strtolower($n)} = 
            (
                \class_exists($c = static::class.'\\'.$k)
                || \class_exists($c = __CLASS__.'\\_\\'.$k)
            ) 
            ? $c::_($this)
            : false
        ) ?: null;
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
    
    public function route(){
        $panel = $this->request->panel ?? "";
        $rpath = $this->request->rpath ?? "";
        $intfx = $this->request->intfx ?? "";
        $__CTLR_FILE__ = null;
        $__UI__ = $this;
        $this->i__auto_init('PRE_ROUTE');
        
        if(
            $rpath ? (
                ($f = \stream_resolve_include_path("{$panel}/{$rpath}/-@{$intfx}.php"))
                || ($f = \stream_resolve_include_path("{$panel}/{$rpath}-@{$intfx}.php"))
            ) : (
                ($f = \stream_resolve_include_path("{$panel}/-@{$intfx}.php"))
            )
        ){
            $__CTLR_FILE__ = \_\i\file::_($f);
            $__UI__ = $this;
            $__CONTEXT__ = function(){
                return $this;
            };
        } else if(
            \class_exists(\_\com::class)
            && \_\com::_()->route($this->request, $__CTLR_FILE__, $__CONTEXT__)
        ){
            //all set
        }
        
        if(\class_exists($panel)){
            $this->PANEL = $panel::_();
        } else {
            $this->PANEL = (static::class."\\panel")::_();
        }
        
        if($__CTLR_FILE__ instanceof \SplFileInfo){
            $__UI__ = $this;
            return (function() use($__CTLR_FILE__, $__UI__){
                $tsp = \explode(PATH_SEPARATOR,get_include_path());
                foreach($tsp as $d){
                    \is_file($f = "{$d}/.functions.php") AND include_once $f;
                }
                foreach(\array_reverse($tsp) as $d){
                    \is_file($f = "{$d}/.module.php") AND include_once $f;
                }
                if(\is_callable($o = (include $__CTLR_FILE__))){
                    $__UI__->controller = $o;
                    ($o)($_REQUEST->ctrl_args ?? null);
                }
            })->bindTo($__CONTEXT__());
        }        
        
        return function(){
            \http_response_code(404);
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
            exit("404 Not Found: ".$_SERVER['REQUEST_URI']);
        };
        
    }
    
}