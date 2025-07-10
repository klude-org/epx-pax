<?php namespace _\env\com;

trait context_item_node__t {
    
    use node__t {
        __construct as __construct_base;
    }
    public readonly string $INDEX;
    protected function __construct($component, $index){
        $this->__construct_base($component);
        $this->INDEX = $index;
    }
    private static function si__subpath(){
        static::$I__SUB_PATH = \str_replace('\\','/', \_\REQ['panel'].\strrchr(static::class,'\\')."/");
    }

}