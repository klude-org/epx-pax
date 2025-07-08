<?php namespace _\xui;

final class view {
    
    private readonly mixed $callable;
    private ?object $context;
    private object $on_default;
    
    public static function _(string|callable|\SplFileInfo  $expr = ''){
        return new static((\is_string($expr)) 
            ? (o()->file($expr,'-v.php')
                ?: function(){ throw new \Exception("View not found: '{$expr}'"); }
            )
            : $expr
        );
    }
    
    private function __construct($expr){
        if(\is_callable($expr)){
            $this->callable = $expr;
        } else if($expr){
            $__FILE__ = $expr;
            $this->callable = function ($__INSET__ = null, array $__PARAM__ = null) use($__FILE__){
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
            };
        }
    }
    
    public function context(object|null $context = null){
        if(func_num_args()){
            $this->context = $context;
            return $this;
        } else {
            return $this->context;
        }
    }    
    
    public function __get($n){
        return o()->xui->__get($n);
    }    
    
    public function __call($m,$a){
        return o()->xui->$m(...$a);
    }    
    
    public function __invoke(...$args){
        return $this->context 
            ? ($this->callable)->bindTo($this->context)(...$args)
            : ($this->callable)(...$args)
        ;
    }
    
    public function prt(...$args){
        return $this->context 
            ? ($this->callable)->bindTo($this->context)(...$args)
            : ($this->callable)(...$args)
        ;
    }
 
    public function on_default(bool|callable $on_default = false){
        if($on_default === false){
            $this->on_default = function(){ };
        } else if($on_default === true){
            $this->on_default = function(){ throw new \Exception("View not found"); };
        } else {
            $this->on_default = $on_default;
        }
        return $this;
    }
    
    public function put(...$args){ return $this->xui->put(...$args); }
    public function plugin(...$args){ return $this->xui->plugin->include(...$args); }
    public function plic__set(...$args){ return $this->xui->plic__set(...$args); }
    
}
