<?php namespace _\xui\pool\alpha;

class controller extends \stdClass  {
    
    protected $vars = [];
    
    public function __construct(){
        
    }
    
    public function __invoke() {
        if($_REQUEST->is_action){
            
        } else {
            $this->data__query();
            $this->data__gen();
            $this->export__gen();
            include '-v.php';
        }
    }
    
    public function data__gen(){ 
        $this->db = o()->db;
        $this->vars['table/params'] = $params = $_GET['--p'] ?? [];
        $pageSize = $this->vars['table/params']['page_sz'] = isset($params['page_sz']) ? intval($params['page_sz']) : 10;
        $page_no = $this->vars['table/params']['page_no'] = isset($params['page_no']) ? intval($params['page_no']) : 1; 
        $search = $this->vars['table/params']['search'] = isset($params['search']) ? trim($params['search']) : '';
        $offset = ($pageSize > 0) ? (($page_no - 1) * $pageSize) : 0;

        if(($fn = $this->vars['table/params']['on__setup'] ?? null) instanceof \closure){
            ($fn)();
        }
        
        //strange!!! this didn't work
        // $q__from = '';
        // $q__selects = '*';
        // $q__joins = '';
        // $q__wheres = '';
        // $q__orders = '';
        // $q__binds = '';
        // if(\is_array($e = $this->vars['table/query'] ?? null)){
        //     \extract($e, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'q__');
        // }
        $q__ = $this->vars['table/query'] ?? [];
        $q__from = $q__['from'] ?? '';
        $q__selects = \rtrim($q__['selects'] ?? '*',", \r\n");
        $q__joins = $q__['joins'] ?? '';
        $q__wheres = $q__['wheres'] ?? '';
        $q__orders = $q__['orders'] ?? '';
        $q__pre_exec = $q__['on__prepare'] ?? function(){ };
        $query = "SELECT COUNT(*) as `total` FROM {$q__from} {$q__joins} {$q__wheres}";
        $stmt = $this->db->prepare($query);
        ($q__pre_exec)($stmt);
        $stmt->execute();
        if($total_records = $stmt->fetchColumn()){
            $total_pages = ceil($total_records / $pageSize);
            $q__limits = ($pageSize > 0) ? "
                LIMIT :__limit__ OFFSET :__offset__
            " : "";
            $query = "SELECT {$q__selects} FROM {$q__from} {$q__joins} {$q__wheres} {$q__orders} {$q__limits}";
            0 AND \file_put_contents(__DIR__.'/.local-dump.sql', $query);
            $stmt = $this->db->prepare($query);
            ($q__pre_exec)($stmt);
            if($pageSize > 0){
                $stmt->bindValue(':__limit__', $pageSize, \PDO::PARAM_INT);
                $stmt->bindValue(':__offset__', $offset, \PDO::PARAM_INT);
            }
            $stmt->execute();
            // Fetch all orders with related customer and vehicle data
            if($rows = $stmt->fetchAll(\PDO::FETCH_ASSOC)){
                $count = count($rows);
                $start = $offset;
                $end = $start + $count -1;
                $first = ($count > 0) ? $start + 1 : $start;
                $last = ($count > 0) ? $end + 1 : $end;
                $i = 1;
                foreach($rows as &$o){
                    $o['#'] = $i++;
                }
                if(($fn = $this->vars['table/result']['on__adjust'] ?? null) instanceof \closure){
                    ($fn)($rows);
                }
                $this->vars['table/result']['meta'] = [
                    'page_no' => $page_no,
                    'page_sz' => $pageSize,
                    'total_records' => $total_records,
                    'total_pages' => $total_pages,
                    'count' => $count,
                    'start' => $start,
                    'end' => $end,
                    'first' => $first,
                    'last' => $last,
                ];
                $this->vars['table/result']['rows'] = $rows;
            }
        } else {
            $this->vars['table/result']['meta'] = [
                'page_no' => 1,
                'page_sz' => 10,
                'total_records' => 0,
                'total_pages' => 0,
                'count' => 0,
                'start' => 0,
                'end' => 0,
                'first' => 0,
                'last' => 0,
            ];
            $this->vars['table/result']['rows'] = [];
        }        
    }
    
    public function export__gen(){
        if($this->vars['table/params']['export'] ?? null){
            if(($fn = $this->vars['table/result']['on__export'] ?? null) instanceof \closure){
                
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                
                $rows = ($fn)($this->vars['table/result']['rows'] ?? []);
                
                // CSV file creation
                $filename = "order_data_" . date('Ymd') . ".csv";
                header("Content-Disposition: attachment; filename=\"$filename\"");
                header("Content-Type: text/csv");
        
                // Output CSV headers
                $output = fopen('php://output', 'w');
                foreach($rows as $row){
                    fputcsv($output, $row);
                }
                fclose($output);
                exit();
            }
        }
    }    
    
    public function paginator_entries__prt(){
        $page_sz = $this->vars['table/result']['meta']['page_sz'] ?? 10;
        $options = $this->vars['table/params']['page_size_options'] ?? [5 => '5',10 => '10', 50 => '50', 100 => '100', -1 => 'All'];
        foreach($options as $k => $v) {
            $selected = ($page_sz == $k) ? 'selected' : '';
            echo "<option value=\"{$k}\" {$selected}>{$v}</option>";
        }
    }
    
    public function paginator_stats__prt(){
        $result = $this->vars['table/result']['meta'] ?? ($this->vars['table/result']['meta'] = [
            'page_no' => 1,
            'page_sz' => 10,
            'total_records' => 0,
            'total_pages' => 0,
            'count' => 0,
            'start' => 0,
            'end' => 0,
            'first' => 0,
            'last' => 0,
        ]);
        $count = $result['count'];
        if($count <= 0){
            $n = "No items to show";
        } else if($count == 1){
            $n = "{$count} item [ {$result['first']} of {$result['total_records']} ]";
        } else {
            $n = "{$count} items [ {$result['first']} ~ {$result['last']} of {$result['total_records']} ]";
        }
        echo $n;        
    }
    
    public function paginator_nav__prt(){ 
        
        $page_no = $this->vars['table/result']['meta']['page_no'] ?? 1;
        $total_pages = $this->vars['table/result']['meta']['total_pages'] ?? 1;
        $page_sz = $this->vars['table/result']['meta']['page_sz'] ?? 10;
        if($page_sz > 0){ 
            ?>
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <!-- First Button -->
                <li class="page-item <?= $page_no == 1 ? 'disabled' : 'xui-pointer' ?>">
                    <a class="page-link" onclick="xui__table_set_page(<?=1?>)" aria-label="First">
                        <span aria-hidden="true"><i class="bi bi-chevron-bar-left"></i></span>
                    </a>
                </li>
                <!-- Previous Button -->
                <li class="page-item <?= $page_no == 1 ? 'disabled' : 'xui-pointer' ?>">
                    <a class="page-link" onclick="xui__table_set_page(<?= $page_no - 1 ?>)" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item xui-pointer <?= $i == $page_no ? 'active' : '' ?>">
                        <a class="page-link" onclick="xui__table_set_page(<?=$i?>)"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next Button -->
                <li class="page-item <?= $page_no == $total_pages ? 'disabled' : 'xui-pointer' ?>">
                    <a class="page-link" onclick="xui__table_set_page(<?= $page_no + 1 ?>)" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <!-- Last Button -->
                <li class="page-item <?= $page_no >= $total_pages ? 'disabled' : 'xui-pointer' ?>">
                    <a class="page-link" onclick="xui__table_set_page(<?=$total_pages?>)" aria-label="Last">
                        <span aria-hidden="true"><i class="bi bi-chevron-bar-right"></i></span>
                    </a>
                </li>
            </ul>
            <?php
        }
    }
    
}