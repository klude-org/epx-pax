<?php namespace _\env\intfc\web;

class request extends \stdClass implements \ArrayAccess, \JsonSerializable {
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # traits
    use \_\i\instance__t;
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Members
    private static $_;
    public readonly \_\env $env;
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Functions
    public function __construct($env){
        $this->env = $env;
        static::$_  = $_REQUEST;
        $_REQUEST = $this;
    }
    
    public function offsetSet($n, $v):void { 
        throw new \Exception('Set-Accessor is not supported for class '.static::class);
    }
    public function offsetExists($n):bool { 
        return isset(static::$_[$n]);
    }
    public function offsetUnset($n):void { 
        throw new \Exception('Unset-Accessor is not supported for class '.static::class);
    }
    public function offsetGet($n):mixed { 
        return static::$_[$n] ?? null;
    }
    public function jsonSerialize():mixed {
        return [ '_' => static::$_ ] + (array) $this;
    }
    
}
