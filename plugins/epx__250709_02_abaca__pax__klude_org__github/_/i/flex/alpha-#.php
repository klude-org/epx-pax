<?php namespace _\i\flex;

class alpha extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\instance__t;
    protected $A§ = []; //ALT+0167
    protected $F§ = [];
    
    public function offsetSet($n, $v):void { 
        if(\is_null($n)){
            $this->A§[] = $v;    
        } else {
            $this->A§[$n] = $v;    
        }
    }
    public function offsetExists($n):bool { 
        return \array_key_exists($n, $this->A§);
    }
    public function offsetUnset($n):void { 
        unset($this->A§[$n]);
    }
    public function offsetGet($n):mixed { 
        return $this->A§[$n] ?? null;
    }

    public function __set($n, $v){
        if($v instanceof \closure){
            $this->F§[$n] = $v->bindTo($this, static::class);    
        } else {
            $this->A§[$n] = $v;    
        }
    }
    
    public function __isset($n){
        return \array_key_exists($n, $this->A§);
    }
    
    public function __unset($n){
        unset($this->A§[$n]);
    }
    
    public function __get($n){
        return $this->A§[$n] ?? null;
    }    
    
    public function __call($method, $args){
        return ($this->F§[$method])(...$args);
    }
 
    public function jsonSerialize():mixed {
        return $this->A§;
    }
    
}