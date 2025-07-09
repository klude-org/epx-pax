<?php namespace _\i\flex;

class functions {
    
    use \_\i\instance__t;
    
    private $functions = [];
    private readonly object $host;
    private readonly string $host_class;
    
    public function __construct($host){
        $this->host = $host;
        $this->host_class = get_class($host);
    } 
    
    public function __set($n, \closure $v){
        $this->functions[$n] = $v->bindTo($this->host, $this->host_class);
    }
    
    public function __isset($n){
        return isset($this->functions[$n]);
    }
    
    public function __unset($n){
        unset($this->functions[$n]);
    }
    
    public function __get($n){
        return $this->functions[$n] ?? null;
    }
}