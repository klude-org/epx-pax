<?php namespace __php_ibs;

class std_server extends \stdClass {
    
    public function __construct(){
        // Configuration
        parent::__construct();
        $this->php_exe = $_SERVER['FX__PHP_EXEC_STD_PATH'] ?? "php";
    }
    
}