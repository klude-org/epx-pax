<?php

abstract class epx__250627_01_neo_ui__pax__klude_org__github extends \stdClass {
    
    public object $vars;

    public static function _(){ 
        static $I; return $I ?? ($I = (__CLASS__."\\".\_\INTFC)::_()); 
    }
    
    protected function __construct(){ 
        $this->vars = (object)[];
    }
    
    public function __get($n){
        $k = \strtolower($n);
        return $this->$k = (static::class.'\\'.$k)::_($this);
    }
    
}