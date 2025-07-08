<?php namespace epx__250706_01_ui__pax__klude_org__github\web;

class request extends \stdClass implements \ArrayAccess, \JsonSerializable {
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # traits
    use \_\i\instance__t;
    use \epx__250706_01_ui__pax__klude_org__github\request__t;
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Members
    private static $_;
    public readonly \epx__250706_01_ui__pax__klude_org__github $ui;
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Functions
    public function __construct($ui){
        $this->ui = $ui;
        $this->i__construct_request__t();
        # ----------------------------------------------------------------------
        $this->scheme = ($_SERVER["REQUEST_SCHEME"] ?? ((\strtolower(($_SERVER['HTTPS'] ?? 'off') ?: 'off') === 'off') ? 'http' : 'https'));
        $this->host = $_SERVER["HTTP_HOST"];
        $this->root_url = $this->scheme.'://'.$this->host;
        $this->full_url = \rtrim($this->root_url.$this->urp,'/');
        $this->site_url = \rtrim($this->root_url.$this->site_urp,'/');
        $this->lib_url = $this->site_url."/--epx";
        $this->data_url = $this->site_url."/data";
        $this->data_asset_url = $this->site_url."/data/-asset";
        $this->theme_asset_url = $this->site_url."/--epx/theme/-asset";
        $this->asset_url = $this->site_url."/-asset";
        $this->base_url = \rtrim($this->site_url."/"
            .(
                ($this->parsed['portal'] ?? null ?: '')
                .'.'.($this->parsed['role'] ?? null ?: '')
            )
            , 
            '/.'
        );
        # ----------------------------------------------------------------------
        $this->method = $method = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->headers = \iterator_to_array((function(){
            foreach(\getallheaders() as $k => $v){
                yield $k => $v;
            }
        })());
        $this->agent = $agent = (function(){
            if(!\is_null($agent = $this->headers['Epx-Agent'] ?? null)){
                return $agent;
            } else if('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' )) {
                return 'xhr';
            } else {
                return 'page';
            }
        })();
        # ----------------------------------------------------------------------
        $this->is_get = $is_get = !\in_array($method, ['POST','PUT','PATCH','DELETE']);
        $this->action = $action = $_REQUEST['--action'] ?? null;
        $this->is_action = $is_action = ($action || !$is_get) ? true : false;
        $this->is_view = !$is_action;
        $this->referer = ($j = $_SERVER['HTTP_REFERER'] ?? null) ? \parse_url($j) : [];
        $this->is_top = (($x = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? ($j ? 'document' : null)) === 'document');
        $this->is_mine = !$j || \str_starts_with($j, $this->root_url);
        $this->is_supply = \preg_match('#(?:-pub|-@)[/\.]#', $this->rurp) ? true : false;
        $this->is_html = (\str_contains(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html'));
        $this->is_xhr = ('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' ));
        $this->is_sub = (($_SERVER['HTTP_REFERER'] ?? '') ? true : false);        
        # ----------------------------------------------------------------------
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
                                return \_\i\file::_((string) $this, $this->INFO);
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
    
    

    
}
