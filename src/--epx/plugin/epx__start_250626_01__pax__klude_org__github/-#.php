<?php

final class epx__start_250626_01__pax__klude_org__github {
    
    public static function _() { static $i;  return $i ?: ($i = new static()); }
    
    private function __construct(){
    }
    
    public function __invoke(){
        echo __METHOD__.":".__FILE__.PHP_EOL;
        \epx__std_ui__pax__klude_org__github\web::_();
    }
    
}