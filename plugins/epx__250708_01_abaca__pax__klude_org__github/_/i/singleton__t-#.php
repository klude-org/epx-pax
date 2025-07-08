<?php namespace _\i;

trait singleton__t {
    public static function _() { 
        static $I = []; return $I[static::class] ?? ($I[static::class] = new static());
    }
}