<?php namespace epx__250707_01_com__pax__klude_org__github\component;

class model implements \ArrayAccess {

    use _\__t;
    
    private string $TBLP;
    
    public function tblp(){
        return $this->TBLP ?? $this->TBLP = $this->COM->config('tblp') ?? $this->COM->name();
    }
    
    public function offsetSet($n, $v):void { 
        o()->db[$this->tblp()][$n] = $v;
    }
    public function offsetExists($n):bool { 
        return isset(o()->db[$this->tblp()][$n]);
    }
    public function offsetUnset($n):void {
        unset(o()->db[$this->tblp()][$n]); 
    }
    public function offsetGet($n):mixed { 
        return o()->db[$this->tblp()][$n];
    }
    
    public function __call($name, $args){
        return o()->db[$this->tblp()]->$name(...$args);
    }
    
}