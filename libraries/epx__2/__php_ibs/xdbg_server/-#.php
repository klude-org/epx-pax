<?php namespace __php_ibs;

class xdbg_server extends server {
    
    public function __construct(){
        // Configuration
        parent::__construct();
        $this->php_exe = $_SERVER['FX__PHP_EXEC_XDBG_PATH'] ?? "C:/xampp/current/php__xdbg/php.exe";
    }
    
}