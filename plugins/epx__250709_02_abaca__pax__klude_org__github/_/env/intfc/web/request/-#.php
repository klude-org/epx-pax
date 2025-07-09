<?php namespace _\env\intfc\web;

class request extends \stdClass implements \ArrayAccess, \JsonSerializable {
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # traits
    use \_\i\instance__t;
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Members
    private static $_;
    public readonly \_\env $env;
    # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    # Functions
    public function __construct($env){
        $this->env = $env;
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
        $this->is_mine = !$j || \str_starts_with($j, $this->env->root_url);
        $this->is_supply = \preg_match('#(?:-pub|-@)[/\.]#', $this->env->rurp) ? true : false;
        $this->is_html = (\str_contains(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html'));
        $this->is_xhr = ('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' ));
        $this->is_sub = (($_SERVER['HTTP_REFERER'] ?? '') ? true : false);
        # ----------------------------------------------------------------------
        static::$_  = $_REQUEST;
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
