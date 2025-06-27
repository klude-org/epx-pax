<?php namespace epx__std_ui__pax__klude_org__github;

final class web {
    
    public static function _() { static $i;  return $i ?: ($i = new static()); }
    
    private function __construct(){
    }
    
    public function __invoke(){
        echo __METHOD__.":".__FILE__.PHP_EOL;
    }
    
}