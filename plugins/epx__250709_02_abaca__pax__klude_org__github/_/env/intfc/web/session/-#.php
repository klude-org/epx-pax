<?php namespace _\env\intfc\web;

class session implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\singleton__t;
    
    public function __construct(){
    }
    
    public function offsetSet($n, $v):void { 
        $_SESSION[$n] = $v;
    }
    public function offsetExists($n):bool { 
        return isset($_SESSION[$n]);
    }
    public function offsetUnset($n):void { 
        unset($_SESSION[$n]);
    }
    public function &offsetGet($n):mixed { 
        return $_SESSION[$n];
    }
    public function jsonSerialize():mixed {
        return [ '_' => static::$_ ] + (array) $this;
    }
    
}
