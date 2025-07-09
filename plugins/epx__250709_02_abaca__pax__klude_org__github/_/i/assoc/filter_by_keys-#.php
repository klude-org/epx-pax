<?php namespace _\i\assoc;

class filter_by_keys {
    
    public static function numeric($assoc){
        return \array_filter($assoc, fn($k) => \is_numeric($k), \ARRAY_FILTER_USE_KEY);
    }
    
    public static function non_numeric($assoc){
        return \array_filter($assoc, fn($k) => !\is_numeric($k), \ARRAY_FILTER_USE_KEY);
    }
}