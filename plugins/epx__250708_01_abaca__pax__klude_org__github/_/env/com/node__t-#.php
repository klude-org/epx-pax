<?php namespace _\env\com;

trait node__t {
    
    use \_\i\instance__t;
    public readonly object $COM;
    private static string $I__SUB_PATH;
    protected function __construct($component){
        $this->COM = $component;
        empty(static::$I__SUB_PATH) AND static::si__subpath();
        $this->i__construct();
    }
    
    private static function si__subpath(){
        static::$I__SUB_PATH = substr(static::class, \strrpos(static::class, '\\') + 1);
    }
    
    private function i__construct(){ }    
    
    public function file(string $path,...$args){
        return $this->COM->file(static::$I__SUB_PATH."/{$path}",...$args);
    }
    
    public function glob(string $path,...$args){
        return $this->COM->glob(static::$I__SUB_PATH."/{$path}",...$args);
    }
    
    public function view(string $path = null,...$args){
        return $this->COM->view(static::$I__SUB_PATH.($path ? "/{$path}" : ''),...$args);
    }

}