<?php namespace _;

class xui {
    
    use \_\i\singleton__t;
    use \_\i\extensible\c\getter__t;
    
    private $I__PLICS = [];
    
    public function view(string|callable|\SplFileInfo $path = ''){
        return \_\xui\view::_($path)->context(\is_callable($path) ? null : $this);
    }
    
    public function plic__set($k, $v){
        //* plic__set processes information immediately
        isset($this->I__PLICS[$k]) OR $this->I__PLICS[$k] = '';
        $this->I__PLICS[$k] .= (\is_string($v)) ? $v : \_\texate($v);
    }
    
    public function put($k){
        echo $this->I__PLICS[$k] ?? '';
    }
    
}