<?php

abstract class epx__std_ui__pax__klude_org__github {
    
    public static function _() { static $i;  return $i ?: ($i = new static()); }
    
    private function __construct(){
        
    }
    
    public function route(){
        return function(){
            echo __METHOD__.PHP_EOL;
        };
    }
    
}