<?php

final class epx__250631_01_db__pax__klude_org__github implements \ArrayAccess {
    
    public readonly string $database;
    public readonly string $hostname;
    public readonly string $char_set;
    public readonly string $dir;
    private $pdo;
    
    private $i__tables;
    private $i__queries = [];
    private $i__error_messages = [];
    private $i__logs = [];
    
    public static function _(int $index = 0){
        static $I = []; return $I[$index] ?? ($I[$index] = new static($index));
    }
    
    private function __construct(int $index = 0){
        $this->hostname = $_ENV['DB_HOSTNAME'] ?? 'localhost';
        $this->database = $_ENV['DB_DATABASE'] ?? '';
        $this->char_set = $_ENV['DB_CHAR_SET'] ?? 'utf8mb4';
        $this->dir = \_\p(\_\DATA_DIR."/db/{$index}");
    }
    
    public static function owner_of(object $me, object|bool|int $owner = null){
        static $c = []; 
        if(\is_null($owner) || $owner === 0){
            return $c[\spl_object_id($me)] ?? null;
        } else if(\is_int($owner)){
            return ($i = $c[\spl_object_id($me)] ?? null) ? \_\owner_of($i, --$owner) : null;
        } else if(\is_bool($owner)){
            if($owner == false){
                unset($c[\spl_object_id($me)]);
            } else {
                //* do nothing
            }
        } else {
            return $c[\spl_object_id($me)] = $owner;
        }
    }
    
    public function pdo(){
        if(!$this->pdo){
            if($_ENV['DB_AUTOCREATE']){
                try {
                    $this->pdo = new \PDO(
                        "mysql:host={$this->hostname}",
                        $_ENV['DB_USERNAME'] ?? 'root',
                        $_ENV['DB_PASSWORD'] ?? '',
                        [
                            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                            \PDO::ATTR_EMULATE_PREPARES   => false,
                        ]
                    );
                    
                    if(!$this->db__exists()){
                        $this->db__create();
                    }
                    
                    $this->exec("USE `{$this->database}`");
                    
                } catch (\PDOException $e) {
                    throw new \PDOException($e->getMessage(), (int)$e->getCode());
                }            
            } else {
                try {
                    $this->pdo = new \PDO(
                        "mysql:host={$this->hostname};dbname={$this->database};charset={$this->char_set}", 
                        $_ENV['DB_USERNAME'] ?? 'root',
                        $_ENV['DB_PASSWORD'] ?? '',
                        [
                            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                            \PDO::ATTR_EMULATE_PREPARES   => false,
                        ]
                    );
                } catch (\PDOException $e) {
                    throw new \PDOException($e->getMessage(), (int)$e->getCode());
                }                
            }
        }
        return $this->pdo;
    }
    public function last_error_message(){
        return end($this->i__error_messages);
    }
    public function connect(){
        try{
            if($this->pdo()){
                return true;
            }
        } catch (\PDOException $ex){
            $this->i__error_messages[] = $ex->getMessage();
        }
        return false;
    }
    public function __call($name, $args){
        if(\method_exists($this->pdo ?? $this->pdo(),$name)){
            return $this->pdo->$name(...$args);
        } else {
            throw new \Exception("Method Not Found '{$name}'");
        }
    }
    
    private function log($contents){
        static $f; 
        static $p;
        if(!$f){
            if(!\is_file($f = \_\DATA_DIR.'/.local-sql/'.date('Y-md').'-log.php')){
                \is_dir($d = \dirname($f)) OR \mkdir($d,0777,true);
            }
            $p = \str_pad('',80, '#');
        } 
        
        \file_put_contents($f, $p.PHP_EOL.$contents.PHP_EOL.PHP_EOL, FILE_APPEND | LOCK_EX);
    }    
    
    public function query(string|callable $query = null,...$args){
        if(\is_string($query)){
            try{
                return $this->pdo()->query($this->i__queries[] = $query, ...$args);
            } catch (\Throwable $ex) {
                $this->log("Error on Query: ".\date("Y-md-Hi-s").":{$ex->getMessage()}\n".$query);
                throw new \epx__250631_01_db__pax__klude_org__github\fault\pdo_query('Encountered an exception on query', 0, $ex, [
                    'previous' => 1,
                    'sql' => $query
                ]);
            }
        } else if(\is_callable($query) || \is_null($query)){
            $q = \epx__250631_01_db__pax__klude_org__github\query::_($this);
            if($query){
                ($query)($q, $this);
            }
            return $q;
        }
    }
    
    public function write($sql, $data) {
        //! credits: https://stackoverflow.com/a/7716896/10753162
        $this->i__queries[] = $query = \preg_replace_callback('/:([0-9a-z_]+)/i', function($m) use(&$data){
            $v = $data[$m[1]];
            if ($v === null) {
                return "NULL";
            }
            if (!is_numeric($v)) {
                $v = str_replace("'", "''", $v);
            }
            return "'". $v ."'";
        }, $sql);
        try{
            return $this->pdo()->prepare($sql)->execute($data);
        } catch (\Throwable $ex) {
        	$this->log("Error on Write: ".\date("Y-md-Hi-s").":{$ex->getMessage()}\n".$query);            
            throw new \epx__250631_01_db__pax__klude_org__github\fault\pdo_query('Encountered an exception on query', 0, $ex, [
                'previous' => 1,
                'sql' => $query
            ]);
        }
    }
    public function prepare(string $query, array $options = []) {
        try{
            return $this->pdo()->prepare($this->i__queries[] = $query, $options);
        } catch (\Throwable $ex) {
            $this->log("Error on Prepare: ".\date("Y-md-Hi-s").":{$ex->getMessage()}\n".$query);
            throw new \epx__250631_01_db__pax__klude_org__github\fault\pdo_query('Encountered an exception on query', 0, $ex, [
                'previous' => 1,
                'sql' => $query
            ]);
        }
    }
    public function exec(string $query): int|false {
        try{
            return $this->pdo()->exec($this->i__queries[] = $query);    
        } catch (\Throwable $ex) {
            $this->log("Error on Exec: ".\date("Y-md-Hi-s").":{$ex->getMessage()}\n".$query);
            throw new \epx__250631_01_db__pax__klude_org__github\fault\pdo_query('Encountered an exception on exec', 0, $ex, [
                'previous' => 1,
                'sql' => $query
            ]);
        }
    }    
    public function execute($sql, $data = null){
        try {
            $sql_debug = $_ENV['DB_DEBUG'] ?? '';
            if($data){
                $sql_debug>=5 AND $this->log[] = ['db_query' => ['sql'=>$sql, 'data'=>$data]];
                if($sql_debug>=5){
                    $this->i__queries = static::data_into_sql($sql, $data);
                }
                if(0){
                    echo "<pre>", json_encode([$sql, $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),"</pre>";
                }
                return $this->pdo()->prepare($sql)->execute($data);
            } else {
                $sql_debug>=5 AND $this->log[] = ['db_query' => ['sql'=>$sql, 'data'=>$data]];
                if($sql_debug>=5){
                    $this->i__queries = $sql;
                }
                // todo: investigate and solve
                // todo:- exec doesn't throw an exception if there are multiple statements... just fails
                $r = $this->pdo()->exec($sql);
                return $r;
            }
        } catch (\PDOException $ex) {
            $this->log("Error on Execute: ".\date("Y-md-Hi-s").":{$ex->getMessage()}\n".$query);
            throw new \epx__250631_01_db__pax__klude_org__github\fault\pdo_query('Encountered an exception on exec', 0, $ex, [
                'previous' => 1,
                'sql' => $sql
            ]);
        }
    }
    
    public static function data_into_sql($sql, &$data){
        //! credits: https://stackoverflow.com/a/7716896/10753162
        return preg_replace_callback('/:([0-9a-z_]+)/i', function($m) use(&$data){
            $v = $data[$m[1]];
            if ($v === null) {
            return "NULL";
            }
            if (!is_numeric($v)) {
            $v = str_replace("'", "''", $v);
            }
            return "'". $v ."'";
        }, $sql);
    }
    
    public static function create_insert_query($table_name, $data){
        $keys = array_keys($data);
        $l1 = '`'.implode('`, `', $keys).'`';
        $l2 = ":".implode(', :', $keys);
        $sql = "INSERT INTO `{$table_name}` ({$l1}) VALUES ({$l2})";
        return static::data_into_sql($sql,$data);
    }
    
    public static function create_update_query($table_name, $data){
        $p1 = implode(', ', array_map(function($k){ return "`{$k}`=:{$k}"; },$keys));
        $sql = "UPDATE `{$path}` SET {$p1} WHERE `id`='{$id}'";
        return static::data_into_sql($sql, $data);
    }
    
    public static function create_multi_insert_query($table_name, $rows){
        $vals=[];
        foreach($rows as $row){
            if(!$vals){
                $keys = array_keys($row);
                $l1 = '`'.implode('`, `', $keys).'`';
                $l2 = ":".implode(', :', $keys);
                $l3 = "({$l2})";
            }
            $data = [];
            foreach($keys as $k){
                $data[$k] = $row[$k] ?? null;
            }
            $vals[] = static::data_into_sql($l3,$data);
        }
        $sql = "INSERT INTO `{$table_name}` ({$l1}) VALUES \n".implode(",\n",$vals).';';
        return $sql;
    }
    
    #endregion
    # ##################################################################################################################
    #region extra
    
    public function id_is_new($id){
        return $id === 0 || $id === '+' || $id < 0;
    }

    #endregion
    # ##################################################################################################################
    #region array access
    
    public function offsetSet($n, $v):void { 
        throw new \Exception("Not Supported");
    }
    public function offsetExists($n):bool { 
        return $this->table__exists($n);
    }
    public function offsetUnset($n):void {
        $this->table__delete($n);
    }
    public function offsetGet($n):mixed {
        return $this->i__tables[$n]['node'] ?? (
            $this->i__tables[$n]['node'] = new \epx__250631_01_db__pax__klude_org__github\table($this, $n)
        );
    }
    
    #endregion
    # ##################################################################################################################
    #region commmon
    
    public function list_tables(){
        return $this->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    #endregion
    # ##################################################################################################################
    #region table
    
    public function table__exists($tblp){
        if(isset($this->i__tables[$tblp])){
            return !empty($this->i__tables[$tblp]);
        } else {
            return !empty($this->i__tables[$tblp] = ($this->query("SHOW TABLES LIKE '{$tblp}'")->fetchColumn() 
                ? ['' => []]
                : []
            ));
        }
    }
    
    public function table__idk($tblp, string $set = null){
        if(\is_null($set)){
            return $this->i__tables[$tblp]['idk'] 
                ?? $this->query("SHOW KEYS FROM `{$tblp}` WHERE Key_name = 'PRIMARY'")
                ->fetch()['Column_name']
            ;
        } else {
            return $this->i__tables[$tblp]['idk'] = $set;
        }
    }

    public function table__statistics($tblp){
        return $this->query("SHOW TABLE STATUS FROM {$this->database__i} LIKE '{$tblp}'")->fetchAll();
    }
    
    public function table__keys($tblp){
        return $this->query("SHOW KEYS FROM {$tblp}")->fetchAll();
    }

    public function table__fields($tblp){
        return $this->i__tables[$tblp]['fields'] ?? (
            $this->i__tables[$tblp]['fields'] = (function($tblp){
                if($this->table__exists($tblp)){
                    $fields = $this->query("DESCRIBE `{$tblp}`")->fetchAll(\PDO::FETCH_COLUMN);
                    return $this->i__tables[$tblp]['fields'] = \array_flip($fields);
                } else {
                    return null;
                }
            })($tblp)
        );
    }
    
    public function table__has_field($tblp, $field){
        return !empty($this->table__fields($tblp)[$field]);
    }
    
    public function table__last_insert_id($tblp){
        return $this->i__tables[$tblp]['last_insert_id'] ?? null;
    }
    
    public function table__column_attribs($tblp,...$attrib_names){
        $a = (($attrib_names[0] ?? '*') == '*')? '*' : '`'.implode('`,`',$attrib_names).'`';
        return $this->query("SELECT {$a} FROM information_schema.columns"
            ." WHERE `table_schema`='{$this->database__i}' AND `table_name`='{$tblp}' ORDER BY `ORDINAL_POSITION` ASC");
    }
    
    public function table__columns($tblp){
        return $this->i__tables[$tblp]['columns'] ?? (
            $this->i__tables[$tblp]['columns'] = (function($tblp){
                if($this->table__exists($tblp)){
                    $columns = [];
                    foreach($this->query("SHOW COLUMNS from `{$tblp}`")
                        ->fetchAll(\PDO::FETCH_OBJ) 
                        as $column
                    ){
                        $columns[$column->Field] = $column;
                    };
                    return $this->i__tables[$tblp]['columns'] = $columns;
                }
            })($tblp)
        );
    }

    public function table__delete($tblp){
        $this->exec("DROP TABLE IF EXISTS `{$tblp}`");
    }
    
    public function table__dir(string $tblp, string $sub = null){
        return "{$this->data_dir}\\{$tblp}".($sub ? "\\{$sub}" : "" );
    }    
    
    #endregion
    # ##################################################################################################################
    #region query
    
    #endregion
    # ##################################################################################################################
    #region bulk

    public function table__get__all($tblp){
        return $this->query("SELECT * FROM `{$tblp}`")->fetchAll();
    }
    
    public function table__get__first($tblp){
        return $this->query("SELECT * FROM `{$tblp}` LIMIT 1")->fetchAll();
    }

    public function table__get__last($tblp){
        return $this->query("SELECT * FROM `{$tblp}` ORDER BY `T`.`id` DESC LIMIT 1")->fetchAll();
    }

    public function table__get__offset($tblp, $start){
        return $this->query("SELECT * FROM `{$tblp}` LIMIT {$start},1")->fetchAll();
    }
    
    public function table__pack__get($tblp, array $args = []){
        $sql = "SELECT * FROM `{$tblp}`";
        \extract($args);
        $package OR $package = (object)[];
        $count_filtered = \count($this->query($sql)->fetchAll() ?? []);
        if($count_filtered){
            $limit = ($limit < 0) ? 0 : $limit;
            $sql_limit = ($limit) ? "LIMIT {$start},{$limit}" : "";
            $pack = $this->query("{$sql} {$sql_limit}")->fetchAll(\PDO::FETCH_OBJ);
            $count = count($pack);
            $end = $start + $count -1;
            $package = (object)[];
            $package->meta['count_all'] = $count_all;
            $package->meta['count_filtered'] = $count_filtered;
            $package->meta['count'] = $count;
            $package->meta['start'] = $start;
            $package->meta['end'] = $end;
            $package->rows = $pack;
        }  else {
            $package->meta['count_all'] = 0;
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
    
    public function table__page__get($tblp, array $args = []){
        $page_sz = 10;
        $page_no = 1;
        $sql = "SELECT * FROM `{$tblp}` LIMIT 1,10";
        \extract($args);
        $package = (object)[];
        $tblp = $this->tblp();
        $limit = $page_sz = ($page_sz ?: 10);
        $page_no = $page_no ?: 1;
        $sql = $this->sql();
        $count_filtered = $this->query("SELECT COUNT(*) as `count_filtered` ".strstr($sql,'FROM'))->fetchColumn();
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
            $pack = $this->query("{$sql} {$sql_limit}")->fetchAll(\PDO::FETCH_CLASS, $class);
            $count = count($pack);
            $end = $start + $count - 1;
            $package->meta['pages'] = $pages;
            $package->meta['page_sz'] = $page_sz;
            $package->meta['page_no'] = $page_no;
            $package->meta['count_all'] = $count_all;
            $package->meta['count_filtered'] = $count_filtered;
            $package->meta['count'] = $count;
            $package->meta['start'] = $start;
            $package->meta['end'] = $end;
            $package->meta['first'] = ($count > 0) ? $start + 1 : $start;
            $package->meta['last'] = ($count > 0) ? $end + 1 : $end;
            $package->rows = $pack;
        }  else {
            $package->meta['pages'] = 0;
            $package->meta['page_sz'] = $page_sz;
            $package->meta['page_no'] = $page_no;
            $package->meta['count_all'] = 0;
            $package->meta['count_filtered'] = 0;
            $package->meta['count'] = 0;
            $package->meta['start'] = 0;
            $package->meta['end'] = 1;
            $package->meta['first'] = 0;
            $package->meta['last'] = 0;
            $package->rows = [];
        }  
        return $package;
    }

    
    #endregion
    # ##################################################################################################################
    #region rows

    public function table__row__set($tblp, $id, array $values, $insert = null){
        $db = $this;
        //* zero or negative id means new
        if(\_\is_empty($id) || (\is_numeric($id) && $id <= 0) || $id == '+'){
            $id = 0;
            $insert = true;
            unset($values['id']);
        }
        
        $data = [];
        $files = [];
        $fsdob = [];
        
        $fields = $this->table__fields($tblp);
        foreach($values as $k => $v){
            if($k[0] === '/'){
                $files[$k] = $v;
            } if(isset($fields[$k])){
                $data[$k] = $v;
            } else {
                $fsdob[$k] = $v;
            }
            $this->cells__i[$tblp][$id][$k] = $v;
        }
        
        if(\is_null($insert)){
            $insert = (
                $this->id_is_new($id) 
                || !($this->query("SELECT `{$this->table__idk($tblp)}` FROM `{$tblp}` WHERE `{$this->table__idk($tblp)}`='{$id}'")
                    ->fetch(\PDO::FETCH_OBJ)
                )
            );
        }
        
        if($insert){
            if($id){
                $data['id'] = $id;
            } else {
                unset($data['id']);
            }
            if($data){
                $keys = array_keys($data);
                $l1 = '`'.implode('`, `',$keys).'`';
                $l2 = ":".implode(', :',$keys);
                $sql = "INSERT INTO `{$tblp}` ({$l1}) VALUES ({$l2})";
            } else {
                $sql = "INSERT INTO `{$tblp}` () VALUES();";
            }
            if($this->execute($sql, $data)){
                $id = $this->i__tables[$tblp]['last_insert_id'] = $this->lastInsertId();
            }
        } else {
            unset($data['id']);
            if($data){
                $keys = \array_keys($data);
                $p1 = '`'.\implode('`=?, `',$keys).'`=?';
                $sql = "UPDATE `{$tblp}` SET {$p1} WHERE `{$this->table__idk($tblp)}`='{$id}'";
                $this->execute($sql, \array_values($data));
            }
        }
        
        if($files){
            foreach($files as $k => $v){
                $this->table__file__set($tblp, $id, $k, $v);
            }
        }
        return $id;
    }
    public function table__row__isset($tblp, $id){
        return ($this->query("SELECT `{$this->table__idk($tblp)}` FROM `{$tblp}` WHERE `{$this->table__idk($tblp)}`='{$id}'")
            ->fetch(\PDO::FETCH_OBJ)
        ) || \is_dir($this->dir."/{$tblp}/({$id})");
    }
    
    public function table__row__unset($tblp, $id){
        $this->exec("DELETE FROM `{$tblp}` WHERE `id`='{$id}'");
        $drec = $this->dir."/{$tblp}/({$id})";
        $this->fs__delete($drec);
    }
    
    public function table__row__get($tblp, $id, $select = '*'){
        if(0){
            $data = ($this->query("SELECT {$select} FROM `{$tblp}` WHERE `{$this->table__idk($tblp)}`='{$id}' LIMIT 1")
                ->fetchAll(\PDO::FETCH_CLASS, $this->i__tables[$tblp]['row_type'] ?? \stdClass::class)[0] ?? null
            );
            $drec = $this->dir."/{$tblp}/({$id})";
            foreach(\glob("{$drec}/*", GLOB_BRACE) as $f){
                if(!\is_dir($f)){
                    $pi = pathinfo($f);
                    $data->{"/{$pi['filename']}"} = \_\f($f);
                }
            }
        } else {
            return $this->query("SELECT {$select} FROM `{$tblp}` WHERE `{$this->table__idk($tblp)}`='{$id}' LIMIT 1")
                ->fetch()
            ;
        }
        return $data;
    }
    
    public function table__row__post($path, $id, $values){
        $this->table__row__set($path, $id, $values, true);
    }

    public function table__row__put($path, $id, $values){
        $this->table__row__set($path, $id, $values, false);
    }
    
    public function table__row__patch($path, $id, $values){
        $this->table__row__set($path, $id, $values, false);
    }

    public function table__row__delete($path, $id){
        $this->table__row__set($path, $id, ['__sts__' => -1],false);
    }

    public function table__row__purge($path, $id){
        $this->table__row__unset($path, $id);
    }
    
    #endregion
    # ##################################################################################################################
    #region cell
    
    protected $cells__i = [];
    
    public function table__cell__set($tblp, $id, $k, $v){
        $this->table__row__set($tblp, $id, [$k => $v]);
    }
    
    public function table__cell__isset($tblp, $id, $k){
        if($k[0] === '/'){
            return $this->table__file__isset($tblp, $id, $k);
        } else if($this->table__has_field($tblp, $k)) {
            return true;
        } else {
            return false; //* TODO FDOB LOGIC
        }
    }
    
    public function table__cell__unset($tblp, $id, $k){
        if($k[0] === '/'){
            $this->table__file__unset($tblp, $id, $k);
        } else if($this->table__has_field($tblp, $k)) {
            //* do nothing
        } else {
            //TOD FDOB LOGIC
        }
    }
    
    public function table__cell__get($tblp, $id, $k){
        if($k){
            return (($k[0] == '/')
                ? $this->table__file__get($tblp, $id, $k)
                : $this->query("SELECT `{$k}` FROM `{$tblp}` WHERE `{$this->table__idk($tblp)}`='{$id}' LIMIT 1")->fetchColumn()
            );
        }
    }
    
    #endregion
    # ##################################################################################################################
    #region bulk
    
    public function table__bulk__set($tblp, array $list, array $args = []){
        if(!$this->table__exists($tblp)){
            return null;
        }
        foreach($list as $record){
            $data = (array) $record;
            $this->table__row__set($tblp, $data['id'] ?? null, $data);
        }
    }
    
    public function table__bulk__get($tblp, array $args = []){
        \extract($args);
        return $this->query("SELECT * FROM `{$tblp}`")->fetchAll(\PDO::FETCH_OBJ);
    }    
    
    #endregion
    # ##################################################################################################################
    #region file
    
    public function table__file__set($tblp, $id, $field, $value){
        $field = \ltrim($field,'/');
        $file_k = "{$this->dir}/{$tblp}/({$id})/{$field}";
        $pi_k = \pathinfo($file_k);
        $fnp_k = "{$pi_k['dirname']}/{$pi_k['filename']}";
        $extn_k = $pi_k['extension'] ?? '*';
        if($value instanceof \SplFileInfo){
            if(\file_exists($value)){
                if(\is_uploaded_file($value)){
                    $extn_v = pathinfo($value->details->name, PATHINFO_EXTENSION);
                } else {
                    $extn_v = $value->getExtension();
                }
                if($extn_k === '*'){
                    foreach(\glob("{$fnp_k}{,.*}",GLOB_BRACE) as $f){
                        \unlink($f);
                    }
                }
                $filedest = (!$extn_v) ? $fnp_k :"{$fnp_k}.{$extn_v}";
                \is_dir($dir = \dirname($filedest)) OR \mkdir($dir,0777,true);
                \is_uploaded_file($value) ? \move_uploaded_file($value, $filedest) : \copy($value, $filedest);
                return \_\f($filedest);
            } else {
                return null;
                //* todo - report error!
            }
        } else if(empty($value)) {
            if($extn_k === '*'){
                foreach(\glob("{$fnp_k}{,.*}",GLOB_BRACE) as $f){
                    \unlink($f);
                }
            } else {
                \unlink("{$fnp_k}.{$extn_k}");
            }
        } else {
            switch($extn_k){
                default:{
                    \is_scalar($value) 
                        ? \file_put_contents($file_k, $value)
                        : \file_put_contents($file_k, \json_encode($value))
                    ;
                    return \_\f($file_k);
                } break;
            }
        }
    }
    
    public function table__file__isset($tblp, $id, $field){
        $field = \ltrim($field,'/');
        if($extn = pathinfo($field, PATHINFO_EXTENSION)){
            return !empty(\glob("{$this->dir}/{$tblp}/({$id})/{$field}"));
        } else {
            return !empty(\glob("{$this->dir}/{$tblp}/({$id})/{$field}.*"));
        }
    }
    
    public function table__file__unset($tblp, $id, $field){
        $field = \ltrim($field,'/');
        if($extn = pathinfo($field, PATHINFO_EXTENSION)){
            if($f = \glob("{$this->dir}/{$tblp}/({$id})/{$field}")[0] ?? null){
                unlink($f);
            }
        } else {
            foreach(\glob("{$this->dir}/{$tblp}/({$id})/{$field}.*") as $f){
                unlink($f);
            }
        }
    }
    
    public function table__file__get($tblp, $id, $field){
        $field = \ltrim($field,'/');
        if(\strpos($field,'*') !== false){
            $flist = [];
            foreach(\glob("{$this->dir}/{$tblp}/({$id})/{$field}") as $f){
                $flist[] = \_\f($f);
            }
            return $flist;
        } else if($extn = pathinfo($field, PATHINFO_EXTENSION)){
            if($f = \glob("{$this->dir}/{$tblp}/({$id})/{$field}")[0] ?? null){
                return \_\f($f);
            }
        } else {
            if($f = \glob("{$this->dir}/{$tblp}/({$id})/{$field}.*")[0] ?? null){
                return \_\f($f);
            }
        }
    }
    
    public function table__fsd($tblp){
        return $this->i__tables[$tblp]['fsd'] ?? (
            $this->i__tables[$tblp]['fsd'] = \epx__250631_01_db__pax__klude_org__github\fsd::new_($this, "{$this->dir}/{$tblp}")
        );
    }
    
    public function fs__collect_all($drec, &$files){
        foreach(new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($drec, \RecursiveDirectoryIterator::SKIP_DOTS)
            , \RecursiveIteratorIterator::CHILD_FIRST
        ) as $f) {
            if(!$f->isDir()){
                $pi = \pathinfo($f);
                $files[\substr("{$pi['dirname']}/{$pi['filename']}", strlen($drec) + 1)] 
                    = \_\f($f)
                ;
            }
        }
    }

    public function fs__delete(string $tblp) {
        if($tblp){
            $g = \glob($tblp, (strpos($tblp,'{') === false) ? 0 : GLOB_BRACE);
            foreach($g as $p){
                if(is_dir($p)){
                    foreach(new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($p, \RecursiveDirectoryIterator::SKIP_DOTS)
                        , \RecursiveIteratorIterator::CHILD_FIRST
                    ) as $f) {
                        if ($f->isDir()){
                            rmdir($f->getRealPath());
                        } else {
                            unlink($f->getRealPath());
                        }
                    }
                    rmdir($p);
                } if(file_exists($p)) {
                    unlink($p);
                }
            }
        }
    }
    
    #endregion
    # ##################################################################################################################
    #region schema
    
    public function table__mount($tblp, $schema_sql = false, $data = []){
        if($this->table__exists($tblp)){
            $this->execute("TRUNCATE TABLE `{$tblp}`");
            //$this->execute("ALTER TABLE `{$tblp}` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0");
            $this->table__bulk__set($tblp, $data ?: []);
        } else if($schema_sql){
            $this->execute($schema_sql);
            $this->table__bulk__set($tblp, $data ?: []);
        }
    }
    
    public function table__unmount($tblp, $schema_sql = false, $data = []){
        
    }
    
    public function table__schema_current($tblp){
        if($this->table__exists($tblp)){
            return $this->db__compile_schema($tblp);
        }
    }
    
    public function table__schema_sql_file($tblp){
        return \_\f("{$tblp}/schema.sql-php");
    }
    
    public function table__schema_sql($tblp):string {
        if($f = $this->table__schema_sql_file($tblp)){
            $TBLP = $tblp;
            return include $f;
        }
    }
    
    public function table__install($tblp, $sql = null){
        $sql OR $sql = $this->table__schema_sql($tblp);
        if($sql){
            if(!$this->table__exists($tblp)){
                $this->execute($sql);
            }
        }
    }
    
    public function table__uninstall($tblp){
        if($this->table__exists($tblp)){
            try{
                $this->query('SET foreign_key_checks = 0');
                $this->exec("DROP TABLE IF EXISTS `{$tblp}`");
            } finally {
                $this->query('SET foreign_key_checks = 1');
            }
        }
    }
    
    #endregion
    # ##################################################################################################################
    #region db

    private function db__list_tables(){
        return $this->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    private function db__clear(){
        try{
            $this->query('SET foreign_key_checks = 0');
            foreach($this->db__list_tables() as $tblp){
                $this->exec("DROP TABLE IF EXISTS `{$tblp}`");
            }
        } finally {
            $this->query('SET foreign_key_checks = 1');
        }
    }

    public function db__compile_schema(...$tblp_list){
        $table = [];
        foreach($tblp_list as $tblp){
            $table_create =   $this->query("SHOW CREATE TABLE `$tblp`")->fetch(\PDO::FETCH_ASSOC);
            $table[$tblp]['fields'] = [];
            $table[$tblp]['constraint'] = [];
            $table[$tblp]['keys'] = [];
            $table[$tblp]['ainc'] = '';
            $table[$tblp]['last'] = '';
            $table[$tblp]['first'] = '';
            foreach(\explode("\n", $table_create['Create Table']) as $v){
                //echo "--".$v.'<br>';
                $x = \trim($v);
                if(\str_starts_with($x, 'CREATE TABLE')){
                    $table[$tblp]['first'] = $x;
                } else if(\str_starts_with($x, '`')){
                    if(\str_contains($x,'AUTO_INCREMENT')){
                        $table[$tblp]['fields'][] = str_replace(' AUTO_INCREMENT','',\rtrim($x," \t,"));
                        $table[$tblp]['ainc'] = "MODIFY ".\rtrim($x," \t,").', AUTO_INCREMENT=0'; 
                    } else {
                        $table[$tblp]['fields'][] = \rtrim($x," \t,");
                    }
                } else if(\str_starts_with($x, ')')){
                    $table[$tblp]['last'] = \preg_replace("#AUTO_INCREMENT=\d+ #",'',$x);
                } else if(\str_starts_with($x, 'CONSTRAINT')){
                    $table[$tblp]['constraint'][] = "ADD ".\rtrim($x," \t,");
                } else {
                    $table[$tblp]['keys'][] = "ADD ".\rtrim($x," \t,");
                }
            }
        }
        $schema_1 = '';
        $schema_2 = '';
        $schema_3 = '';
        $schema_4 = '';
        foreach($table as $tblp => $v){
            $schema_1.= "\n\n{$v['first']}\n  ";
            $schema_1.= \implode(",\n  ", $v['fields']);
            $schema_1.= "\n{$v['last']};\n";
            if($v['keys']){
                $schema_2.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_2.= "  ".\implode(",\n  ", $v['keys']);
                $schema_2.="\n;";
            }
            if($v['ainc']){
                $schema_3.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_3.= "  {$v['ainc']}";
                $schema_3.="\n;";
            }
            if($v['constraint']){
                $schema_4.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_4.= "  ".\implode(",\n  ", $v['constraint']);
                $schema_4.="\n;";
            }
        }
        return \trim("{$schema_1}\n\n{$schema_2}\n\n{$schema_3}\n\n{$schema_4}");
    }

    private function db__stash_file_path($id = '', $extn = 'json'){
        return \_\DATA_DIR."/--stash-db/{$id}.{$extn}";
    }
    
    private function db__stash_file_remove($id){
        if(\is_file($f = $this->db__stash_file_path($id))){
            \unlink($f);
        }
    }
    
    private function db__stash_download(){
        if(\is_file($f = $this->db__stash_file_path($id))){
            \_\x()->download($f);
        }
    }
    
    private function db__stash(array $options = []){
        if($tblp_list = $this->db__list_tables()){
            $clean = true;
            $dated_backup = true;
            \extract($options);
            $data = [];
            $schema = $this->db__compile_schema($tblp_list);
            foreach($tblp_list as $tblp){
                $data[$tblp] = $this->query("SELECT * FROM `{$tblp}`")->fetchAll();    
            }
            $date = \date("Y-md-Hi-s");
            $file = $this->db__stash_file_path($date);
            $file_latest = $this->db__stash_file_path();
            $db['schema'] = $schema;
            $db['data'] = $data;
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            $x = json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            \file_put_contents($file_latest, $x);
            if($dated_backup){
                \file_put_contents($file, $x);
            }
            $schema_file = $this->db__stash_file_path('schema','sql');
            \file_put_contents($schema_file, $schema);
        }
    }
    
    private function db__load($id){
        if(\is_file($f = $this->db__stash_file_path($id))){
            $this->db__clear();
            try{
                $this->query('SET foreign_key_checks = 0');
                $db = json_decode(\file_get_contents($file), true);
                $schema = $db['schema'];
                echo $schema;
                $this->exec($schema);
                foreach($db['data'] as $tblp => $data){
                    foreach($data as $record){
                        if($record){
                            $keys = array_keys($record);
                            $l1 = "`".implode('`, `',$keys)."`";
                            $l2 = ":".implode(', :',$keys);
                            $sql = "INSERT INTO `{$tblp}` ({$l1}) VALUES ({$l2})";
                        } else {
                            $sql = "INSERT INTO `{$tblp}` () VALUES();";
                        }
                        $this->prepare($sql)->execute($record);
                    }
                }
            } finally {
                $this->query('SET foreign_key_checks = 1');
            }
        }
    }

    private function db__mount($id){
        if(\is_file($f = $this->db__stash_file_path($id))){
            if($this->db__list_tables() && $f != \_\DATA_DIR."/db-backup.json"){
                $this->db__stash(['dated_backup' => false]);
            }
            $this->db__load($f);
        }
    }
    
    private function db__unmount(){
        if(\is_file($f = $this->db__stash_file_path($id))){
            if($this->db__list_tables() && $f != \_\DATA_DIR."/db-backup.json"){
                $this->db__stash(['dated_backup' => false]);
            }
            $this->db__clear($f);
        }
    }
    
    public function table_schema__get(string $table) {
        $schema = [
            'columns' => [],
            'primary_keys' => [],
            'foreign_keys' => []
        ];
    
        // Get columns
        $stmt = $this->query("DESCRIBE $table");
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $columnName = $row['Field'];
            $columnType = $row['Type'];
            $isNull = $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
            $default = $row['Default'] !== null ? "DEFAULT '{$row['Default']}'" : '';
            $extra = $row['Extra'];
            $attributes = "$columnName $columnType $isNull $default $extra";
            $schema['columns'][$columnName] = $attributes;
        }
    
        // Get primary keys
        $stmt = $this->query("SHOW INDEX FROM $table WHERE Key_name = 'PRIMARY'");
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $schema['primary_keys'][] = $row['Column_name'];
        }
    
        // Get foreign keys
        $stmt = $this->query("
            SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = '$table' AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $constraint = $row['CONSTRAINT_NAME'];
            $details = "FOREIGN KEY ({$row['COLUMN_NAME']}) REFERENCES {$row['REFERENCED_TABLE_NAME']}({$row['REFERENCED_COLUMN_NAME']})";
            $schema['foreign_keys'][$constraint] = $details;
        }
    
        return $schema;
    }
    
    public function db__use($dbname){
        if($this->db__exists($dbname)){
            $this->exec("USE `{$dbname}`");
            $this->database = $dbname;
        }
    }
    
    public function db__exists($dbname = null){
        if(!$dbname){
            $dbname = $this->database;
        }
        $stmt = $this->query("SHOW DATABASES LIKE '{$dbname}'");
        return $stmt->fetch() ? true : false;
    }
    
    public function db__create(){
        // Create the database if it does not exist
        $this->exec("CREATE DATABASE IF NOT EXISTS `{$this->database}`");
        $this->i__logs[] = "Database '{$this->database}' created or already exists.<br>";
    }
    
    public function db__drop(){
        try {
            // Drop the database
            $this->exec("DROP DATABASE IF EXISTS `{$this->database}`");
        
            $this->i__logs[] = "Database '{$this->database}' successfully dropped.";
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        
    }
    
    public function generateAlterScript($originalTable, $newTable) {
        $alterScript = '';
    
        try {
            // Fetch the schema for the original table
            $originalSchema = $this->table_schema__get($this, $originalTable);
            $newSchema = $this->table_schema__get($this, $newTable);
    
            // Add columns to original table
            foreach ($newSchema['columns'] as $column => $attributes) {
                if (!isset($originalSchema['columns'][$column])) {
                    $alterScript .= "ALTER TABLE $originalTable ADD COLUMN $attributes;\n";
                } elseif ($originalSchema['columns'][$column] !== $attributes) {
                    $alterScript .= "ALTER TABLE $originalTable CHANGE COLUMN $column $attributes;\n";
                }
            }
    
            // Drop columns from original table
            foreach ($originalSchema['columns'] as $column => $attributes) {
                if (!isset($newSchema['columns'][$column])) {
                    $alterScript .= "ALTER TABLE $originalTable DROP COLUMN $column;\n";
                }
            }
    
            // Add primary keys
            $originalPrimaryKeys = $originalSchema['primary_keys'];
            $newPrimaryKeys = $newSchema['primary_keys'];
    
            if ($originalPrimaryKeys !== $newPrimaryKeys) {
                $alterScript .= "ALTER TABLE $originalTable DROP PRIMARY KEY;\n";
                $alterScript .= "ALTER TABLE $originalTable ADD PRIMARY KEY (" . implode(", ", $newPrimaryKeys) . ");\n";
            }
    
            // Add or modify foreign keys
            $originalForeignKeys = $originalSchema['foreign_keys'];
            $newForeignKeys = $newSchema['foreign_keys'];
    
            foreach ($newForeignKeys as $constraint => $details) {
                if (!isset($originalForeignKeys[$constraint])) {
                    $alterScript .= "ALTER TABLE $originalTable ADD CONSTRAINT $constraint $details;\n";
                }
            }
    
            foreach ($originalForeignKeys as $constraint => $details) {
                if (!isset($newForeignKeys[$constraint])) {
                    $alterScript .= "ALTER TABLE $originalTable DROP FOREIGN KEY $constraint;\n";
                }
            }
    
            return $alterScript;
    
        } catch (\PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }    
    #endregion
    # ##################################################################################################################
    #region get
    
    public function get($expr){
        if(\is_string($expr)){
            return \epx__250631_01_db__pax__klude_org__github\get::new_($this, $expr);
        } else if(\is_callable($expr)){
            $query = \epx__250631_01_db__pax__klude_org__github\query::new_($this);
            if($expr){
                ($expr)($query, $this);
            }
            return \epx__250631_01_db__pax__klude_org__github\get::new_($this, $query->sql());
        } else if($expr instanceof \epx__250631_01_db__pax__klude_org__github\query){
            return \epx__250631_01_db__pax__klude_org__github\get::new_($this, $expr->sql());
        } else {
            throw new \Exception("Invalid argument \$query");
        }
    }
    
    public function query_builder(){
        return \epx__250631_01_db__pax__klude_org__github\query::new_($this);
    }
    
    
    public function db__get($expr){
        if(\is_string($expr)){
            return \_\data\db\get::new_($this, $expr);
        } else if(\is_callable($expr)){
            $query = \_\data\db\query::new_($this);
            if($expr){
                ($expr)($query, $this);
            }
            return \_\data\db\get::new_($this, $query->sql());
        } else if($expr instanceof \_\data\db\query){
            return \_\data\db\get::new_($this, $expr->sql());
        } else {
            throw new \Exception("Invalid argument \$query");
        }
    }    
    
    
}