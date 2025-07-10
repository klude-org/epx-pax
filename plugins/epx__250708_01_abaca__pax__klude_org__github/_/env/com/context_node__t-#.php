<?php namespace _\env\com;

trait context_node__t {
    
    use node__t;
    private static function si__subpath(){
        static::$I__SUB_PATH = \str_replace('\\','/', \_\REQ['panel'].\strrchr(static::class,'\\')."/");
    }
    
}