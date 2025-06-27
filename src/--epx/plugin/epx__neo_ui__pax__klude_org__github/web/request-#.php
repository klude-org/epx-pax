<?php namespace epx__neo_ui__pax__klude_org__github\web;

class request extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    private static $_;
    
    use \_\i\singleton__t;
    
    public function __construct(){
        $json = [];
        $files = [];
        switch($content_type = \strtok($_SERVER["CONTENT_TYPE"] ?? '',';')){
            case "application/json": {
                $json = (function(){
                    $input = \file_get_contents('php://input');
                    $ox = [];
                    foreach(\json_decode($input, true) as $k => $v){
                        $oy =& $ox;
                        foreach(explode('[',\str_replace("]","", $k)) as $kk){
                            ($oy[$kk] = []);
                            $oy = &$oy[$kk];
                        }
                        $oy = $v;
                    }
                    return $ox;
                })();
            } break;
            case "multipart/form-data": {
                $files = (function(){
                    $o = [];
                    foreach($_FILES as $field => $array){
                        foreach($array as $attrib => $inner){
                            if(\is_array($inner)){
                                foreach(($r__fn = function($array, $pfx = '', $ifx = '[', $sfx = ']') use(&$r__fn){
                                    foreach($array as $k  => $v){
                                        if(\is_array($v)){
                                            yield from ($r__fn)($v,"{$pfx}{$ifx}{$k}{$sfx}",$ifx,$sfx);
                                        } else {
                                            yield "{$pfx}{$ifx}{$k}{$sfx}" => $v;
                                        }
                                    }
                                })($inner,$field) as $k => $v){
                                    $o[$k][$attrib] = $v;
                                }
                            } else {
                                $o[$field][$attrib] = $inner;
                            }
                        }
                    }
                    $ox = [];
                    foreach($o as $k => $v){
                        if(!($v['name'] ?? null)){ continue; }
                        $oy =& $ox;
                        foreach(explode('[',\str_replace("]","", $k)) as $kk){
                            isset($oy[$kk]) OR $oy[$kk] = [];
                            $oy = &$oy[$kk];
                        }
                        $oy =  new class($v) extends \SplFileInfo implements \JsonSerializable {
                            private array $details;
                            public function __construct($v){
                                $this->details = $v; 
                                parent::__construct($v['tmp_name']);
                            }
                            public function info($n){
                                if($n == 'extension'){
                                    return \pathinfo($this->details['name'] ?? '', PATHINFO_EXTENSION);
                                } else {
                                    return $this->details[$n] ?? null;
                                }
                            }
                            public function jsonSerialize(): mixed {
                                return "--file::".$this->getRealPath();
                            }
                            public function f(){
                                return \_\fsp\file::_((string) $this, $this->INFO);
                            }
                            public function move_to($path){
                                \is_dir($d = \dirname($path)) OR \mkdir($d,0777,true);
                                if(\move_uploaded_file($this, $path)){
                                    return new \SplFileInfo($path);
                                } else {
                                    return false;
                                }
                            }
                        };
                    }
                    return $ox;
                })();
            } break;
            case "application/x-www-form-urlencoded": 
            default:{
                //* do nothing
            } break;
        }
        
        $_FILES = $files;
        //! warning: array_merge_recursive messes up if $_FILES and $_POST have same key
        static::$_  = \array_replace_recursive(
            $_POST, 
            $_FILES, //* $_FILES is higher priority over $_POST
            $json,
            $_GET,
        );
        $_REQUEST = $this;
    }
    
    public function offsetSet($n, $v):void { 
        throw new \Exception('Set-Accessor is not supported for class '.static::class);
    }
    public function offsetExists($n):bool { 
        return isset(static::$_[$n]);
    }
    public function offsetUnset($n):void { 
        throw new \Exception('Unset-Accessor is not supported for class '.static::class);
    }
    public function offsetGet($n):mixed { 
        return static::$_[$n] ?? null;
    }
    public function jsonSerialize():mixed {
        return [ '_' => static::$_ ] + (array) $this;
    }
    
    public function params(){
        return static::$_;
    }
}
