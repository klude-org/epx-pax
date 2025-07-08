<?php namespace epx__250707_01_com__pax__klude_org__github\component\access;

class item {
    
    use \_\i\instance__t;
    
    public readonly object $COM;
    public readonly string $INDEX;
    
    protected function __construct($component, $index){
        $this->COM = $component;
        $this->INDEX = $index;
    }
    
    public function record(array $set = null){
        if(func_num_args()){
            $this->COM->model[$this->INDEX] = $set;
        } else {
            return $this->COM->model[$this->INDEX];
        }
    }
    
}