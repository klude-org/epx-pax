<?php namespace epx__250328_01_db__pax__klude_org__github;

class query {
    
    use \_\i\instance__t;
    
    private $SQL;
    private $DB;
    private $TBLP;
    private $SELECTS;
    private $FILTERS;
    private $JOINERS;
    private $GROUPERS;
    private $SORTERS;
    private $SPLICER;
    private $EACH__FN;
    private $PACK__FN;
    private $GETTER;
    private $PARID;
    
    private $SQL_BUILDS = [];
    const FULL = 'select,from,join,where,group,order,limit';
    const LIMITLESS = 'select,from,join,where,group';
    const FILTERLESS = 'select,from,join';
    const SELECTLESS = 'from,join,where,group';
    
    protected function __construct(\epx__250328_01_db__pax__klude_org__github $db){ 
        $this->DB = $db;
    }
    
    public function parid($id){
        $this->PARID = $id;
        return $this;
    }
    
    public function reset(){ 
        $this->SQL = null;
        $this->SELECTS = null;
        $this->FILTERS = null;
        $this->JOINERS = null;
        $this->GROUPERS = null;
        $this->SORTERS = null;
        $this->SQL_BUILDS = [];
    }
    
    public function setup(?array $options){
        foreach($options ?? [] as $k => $args){
            switch($k){
                case 'select': $this->select(...$args); break;
                case 'where': $this->where(...$args); break;
                case 'join': $this->join(...$args); break;
                case 'with': $this->with(...$args); break;
                case 'group': $this->group(...$args); break;
                case 'order': $this->order(...$args); break;
            }
        }
        return $this;
    }
    
    public function select(array|string $selects){
        if(\is_string($selects)){
            $this->SELECTS[] = $selects;
        } else {
            $this->SELECTS = \array_merge($this->SELECTS ?? [], $selects);
        }
        return $this;
    }
    
    public function from($tblp){
        if($this->TBLP){
           throw new \Exception("Cannot change PRIMARY TABLE. It is already set to '{$this->TBLP}'");
        }
        $this->TBLP = $tblp;
        return $this;
    }
    
    public function filter(...$filter){
        $this->FILTERS = \array_merge($this->FILTERS ?? [], $filter);
        $this->SQL = null;
        return $this;
    }

    public function where(...$wheres){
        foreach($wheres as $w_expr){
            if(\is_string($w_expr)){
                $this->FILTERS[] = $w_expr;
            } else if(\is_array($w_expr)){
                $count = \count($w_expr);
                if($count == 1){
                    $this->FILTERS[] = $w_expr[0];
                } else if($count == 2){
                    list($col, $with) = $w_expr;
                    $compate = '=';
                    if($x = $this->SELECTS[$col] ?? false){
                        $x = implode('`.`', \explode('.', \str_replace('`','', $x)));
                        $this->FILTERS[] = "`{$x}` {$compare} {$with}";
                    } else {
                        $col = implode('`.`', \explode('.', \str_replace('`','', $col)));
                        $this->FILTERS[] = "`{$col}` {$compare} {$with}";            
                    }
                } else if($count == 3){
                    list($col, $compare, $with) = $w_expr;
                    if($x = $this->SELECTS[$col] ?? false){
                        $x = implode('`.`', \explode('.', \str_replace('`','', $x)));
                        $this->FILTERS[] = "`{$x}` {$compare} '{$with}'";
                    } else {
                        $col = implode('`.`', \explode('.', \str_replace('`','', $col)));
                        $this->FILTERS[] = "`{$col}` {$compare} '{$with}'";
                    }
                }
            }
        }        
        return $this;
    }
    
    public function splice(string $alias, string $ta, string $ida, string $tb, string $idb ){
        $this->splice_x($alias, "LEFT JOIN `{$ta}` AS `{$alias}` ON `{$alias}`.`{$ida}` = `{$tb}`.`{$idb}`");
    }

    public function splice_x(string $alias, string $expression){
        if(empty($this->SPLICER[$alias])){
            $this->JOINERS[] = $expression;
            $this->SPLICER[$alias] = true;
        }
    }

    public function with($tblp, $as, $on){
        $this->JOINERS[] = "LEFT JOIN `{$tblp}` as `{$as}` on {$on}";
        $this->SQL = null;
        return $this;
    }

    public function join(...$joins){
        $this->JOINERS = \array_merge($this->JOINERS ?? [], $joins);
        $this->SQL = null;
        return $this;
    }
    
    public function sort_by($expr, $type = 'ASC'){
        if($type){
            if($x = $this->SELECTS[$expr] ?? false){
                $expr = $x;
            }   
            $expr = implode('`.`', \explode('.', \str_replace('`','', $expr)));
            $this->SORTERS[] = "`{$expr}` {$type}";
        } else {
            
        }
        $this->SQL = null;
        return $this;
    }    

    public function group_by(string ...$groupers){
        foreach($groupers as $g){
            if($x = $this->SELECTS[$g] ?? false){
                $g = $x;
            }   
            $g = implode('`.`', \explode('.', \str_replace('`','', $g)));
            $this->GROUPERS[] = "`{$g}`";
        }
        return $this;
    }
    
    protected function gen__sql_select(){
        yield "SELECT\n\t";
        $s = [];
        if($this->SELECTS){
            foreach($this->SELECTS ?? [] as $k => $v){
                if(\is_numeric($k)){
                    $s[] =  "{$v}";
                } else {
                    $s[] =  "{$v} as `{$k}`";
                }
            }
            yield \implode(",\n\t", $s);
        } else {
            yield '*';
        }
    }
    
    protected function gen__sql_from(){
        yield "\nFROM\n\t`{$this->TBLP}` as `T`";
    }

    protected function gen__sql_join(){
        foreach($this->JOINERS ?? [] as $v){
            yield "\n\t".$v;
        }
    }
    
    protected function gen__sql_where(){
        $filters = $this->FILTERS;
        if($filters){
            yield "\nWHERE ";
            $fresh = 1;
            foreach($filters as $filter){
                if(
                    $fresh
                    || \str_starts_with($filter, ')')
                    || \str_starts_with($filter, 'OR')
                    || \str_starts_with($filter, 'AND')
                ){
                    yield "\n\t {$filter}";
                    if(str_ends_with($filter, '(')){
                        $fresh = 1;
                        continue;
                    }
                } else {
                    yield "\n\tAND {$filter}";
                }
                $fresh = 0;
            }
        }
    }

    protected function gen__sql_group(){
        if($this->GROUPERS){
            yield "\nGROUP BY\n\t";
            yield implode(',', $this->GROUPERS);
        }
    }

    protected function gen__sql_order(){
        if($this->SORTERS){
            yield "\nORDER BY\n\t";
            yield implode(',', $this->SORTERS);
        }
    }
    
    protected function gen__sql_limit(){
        yield from [];    
    }
    
    protected function gen__sql($types){
        foreach(\explode(',',$types) as $type){
            yield from $this->{"gen__sql_{$type}"}();    
        }
    }
    
    public function sql(string $types = \epx__250328_01_db__pax__klude_org__github\query::FULL){
        return $this->SQL_BUILDS[$types] 
            ?? ($this->SQL_BUILDS[$types] = \implode('', \iterator_to_array($this->gen__sql($types), false)))
        ;
    }
    
    public function on__each(callable $fn){
        $this->EACH__FN = $fn;
        return $this;
    }
    
    public function on__pack(callable $fn){
        $this->PACK__FN = $fn;
        return $this;
    }
    
    
    private function i__package($package){
        if(\is_callable($this->PACK__FN)){
            ($this->PACK__FN)($package);
        }
        return $this;
    }

    public function i__fetch_count(){
        return $this->DB->query("SELECT COUNT(*) as `count_filtered` ".$this->sql__build(static::SELECTLESS))->fetchColumn();        
    }
    
    public function i__fetch_all($sql){
        $rows = $this->DB->query($sql)->fetchAll(\PDO::FETCH_ASSOC) ?? [];
        if(\is_callable($this->EACH__FN)){
            foreach($rows as &$row){
                ($this->EACH__FN)($row, $rows);
            }
        }
        return $rows;
    }

    public function row($id){
        return $this->i__fetch_all($this->sql(static::FILTERLESS)." WHERE `T`.`id`='{$id}' LIMIT 1")[0] ?? null;
    }
    
    public function get__count(){
        return $this->i__fetch_count();
    }
    
    public function get__first(){
        return $this->i__fetch_all($this->sql(static::LIMITLESS)," LIMIT 1")[0] ?? null;
    }

    public function get__last(){
        return $this->i__fetch_all($this->sql(static::LIMITLESS)."  ORDER BY `T`.`id` DESC LIMIT 1")[0] ?? null;
    }

    public function get__offset($start = 0){
        return $this->i__fetch_all($this->sql(static::LIMITLESS)."  LIMIT {$start},1")[0] ?? null;
    }

    public function get__all(){
        return $this->i__fetch_all($this->sql());
    }

    public function get__bunch($limit = 0){
        return $this->i__fetch_all($this->sql(static::LIMITLESS)."  LIMIT {$limit}");
    }
    
    public function get__range($start = 0, $limit = 0){
        $limit = ($limit < 0) ? 0 : $limit;
        $sql_limit = ($limit) ? "LIMIT {$start},{$limit}" : "";
        return $this->i__fetch_all($this->sql(static::LIMITLESS)."  {$sql_limit}");
    }
    
    public function get__pack($start = 0, $limit = 0, array $args = []){
        $sql = $this->SQL ?: "SELECT * FROM `{$tblp}`";
        \extract($args);
        empty($package) AND $package = (object)[];
        $count_filtered = $this->i__fetch_count($sql);
        if($count_filtered){
            $limit = ($limit < 0) ? 0 : $limit;
            $sql_limit = ($limit) ? "LIMIT {$start},{$limit}" : "";
            $pack = $this->i__fetch_all("{$sql} {$sql_limit}");
            $count = \count($pack);
            $end = $start + $count -1;
            $package = (object)[];
            $package->meta['count_filtered'] = $count_filtered;
            $package->meta['count'] = $count;
            $package->meta['start'] = $start;
            $package->meta['end'] = $end;
            $package->rows = $pack;
            $this->i__package($package);
        }  else {
            $package->meta['count_filtered'] = 0;
            $package->meta['count'] = 0;
            $package->meta['start'] = 0;
            $package->meta['end'] = 0;
            $package->meta['first'] = 0;
            $package->meta['last'] = 0;
            $package->rows = [];
        }  
        return $package;
    }
    
    public function get__page($page_no = 1, $page_sz = 10, array $args = []){
        \extract($args);
        empty($package) AND $package = (object)[];
        $limit = $page_sz = ($page_sz ?: 10);
        $page_no = $page_no ?: 1;
        $count_filtered = $this->i__fetch_count($sql);
        if($count_filtered){
            
            $limit = ($limit < 0) ? 0 : $limit;
            $pages = ($limit > 0) 
                ? (int) \ceil($count_filtered / $limit)
                : 1
            ;
            if($page_no > $pages){
                $page_no = $pages;
            } else if($page_no < 1){
                $page_no = 1;
            }
            $start = ($page_no-1) * $limit;
            $sql_limit = ($limit) ? "LIMIT {$start},{$limit}" : "";
            $pack = $this->i__fetch_all("{$sql} {$sql_limit}");
            $count = count($pack);
            $end = $start + $count - 1;
            $package->meta['pages'] = $pages;
            $package->meta['page_sz'] = $page_sz;
            $package->meta['page_no'] = $page_no;
            $package->meta['count_filtered'] = $count_filtered;
            $package->meta['count'] = $count;
            $package->meta['start'] = $start;
            $package->meta['end'] = $end;
            $package->meta['first'] = ($count > 0) ? $start + 1 : $start;
            $package->meta['last'] = ($count > 0) ? $end + 1 : $end;
            $package->rows = $pack;
            $this->i__package($package);
        }  else {
            $package->meta['pages'] = 0;
            $package->meta['page_sz'] = $page_sz;
            $package->meta['page_no'] = $page_no;
            $package->meta['count_filtered'] = 0;
            $package->meta['count'] = 0;
            $package->meta['start'] = 0;
            $package->meta['end'] = 1;
            $package->meta['first'] = 0;
            $package->meta['last'] = 0;
            $package->rows = [];
            $this->i__package($package);
        }  
        return $package;
    }
        
    
}