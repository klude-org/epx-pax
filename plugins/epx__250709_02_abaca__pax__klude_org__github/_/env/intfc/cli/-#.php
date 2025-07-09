<?php namespace _\env\intfc;

class cli extends \_\env {
    
    use \_\i\instance__t;
    
    protected function __construct(){
        parent::__construct();
    }
    
    protected function i__init(){
        
    }
    
    public function route(){
        return parent::route();    
    }
    
}