<?php namespace _\env\com\db_model;

class item {
    
    use \_\env\com\context_item_node__t;
    
    public function record(array $set = null){
        if(func_num_args()){
            $this->COM->model[$this->INDEX] = $set;
        } else {
            return $this->COM->model[$this->INDEX];
        }
    }
    
}