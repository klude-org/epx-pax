<?php namespace epx__250631_01_db__pax__klude_org__github;
    
class table implements \ArrayAccess {

    protected readonly string $TBLP;
    protected $A§;
    public function __construct($owner, $tblp){
        $this->TBLP = $tblp;
        \epx__250631_01_db__pax__klude_org__github::owner_of($this, $owner);
    }

    public function T§(){
        return $this->TBLP;
    }
    public function offsetSet($n, $v):void { 
        \epx__250631_01_db__pax__klude_org__github::owner_of($this)->table__row__set($this->TBLP, $n, $v);
    }
    public function offsetExists($n):bool { 
        return \epx__250631_01_db__pax__klude_org__github::owner_of($this)->table__row__isset($this->TBLP, $n);
    }
    public function offsetUnset($n):void {
        \epx__250631_01_db__pax__klude_org__github::owner_of($this)->table__row__isset($this->TBLP, $n); 
    }
    public function offsetGet($n):mixed { 
        return \epx__250631_01_db__pax__klude_org__github::owner_of($this)->table__row__get($this->TBLP, $n);
    }
    public function __call($name, $args){
        if(\method_exists($o = \epx__250631_01_db__pax__klude_org__github::owner_of($this), $m = "table__{$name}")){
            return $o->$m($this->TBLP,...$args);
        }
        throw new \Exception("Method not found ".static::class."::{$name}");
    }

    public function get(\closure $fn = null){
        $getter = new \epx__250631_01_db__pax__klude_org__github\get($this);
        $query_builder = $getter->query_builder();
        $query_builder->from($this->TBLP);
        if($fn){
            ($fn)($query_builder, $this);
        }
        return \epx__250631_01_db__pax__klude_org__github\get::new_($query);
    }
    
    public function query(\closure $fn = null){
        $q = \epx__250631_01_db__pax__klude_org__github\query::_(\epx__250631_01_db__pax__klude_org__github::owner_of($this));
        $q->from($this->TBLP);
        if($fn){
            ($fn)($q, $this);
        }
        return $q;
    }
    
    public function get__first(array $options = null){
        return \epx__250631_01_db__pax__klude_org__github\query::_(\epx__250631_01_db__pax__klude_org__github::owner_of($this))
            ->from($this->TBLP)
            ->setup($options)
            ->get__first()
        ;   
    }

    public function get__end(array $options = null){
        return \epx__250631_01_db__pax__klude_org__github\query::_(\epx__250631_01_db__pax__klude_org__github::owner_of($this))
            ->from($this->TBLP)
            ->setup($options)
            ->get__end()
        ;   
    }

    public function get__all(array $options = null){
        return \epx__250631_01_db__pax__klude_org__github\query::_(\epx__250631_01_db__pax__klude_org__github::owner_of($this))
            ->from($this->TBLP)
            ->setup($options)
            ->get__all()
        ;   
    }
    
}