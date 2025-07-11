<?php

(new class extends \stdClass {

    public function __construct(){
        $this->hostname = $_ENV['DB_HOSTNAME'] ?? 'localhost';
        $this->database = $_ENV['DB_DATABASE'] ?? '';
        $this->char_set = $_ENV['DB_CHAR_SET'] ?? 'utf8mb4';
        $this->dir = \_\DATA_DIR.'/db-backup';
    }
    
    public function pdo(){
        try {
            return $this->pdo ?? $this->pdo = new \PDO(
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
    
    public function respond__json($array){
        header('Content-Type: application/json');
        echo json_encode($array);
        exit();
    }
    
    public function __invoke(){
        if($action = $_REQUEST['--action']){
            switch($action){
                case 'backup': {
                    if($filename = $this->do__backup()){
                        $this->respond__json(['ok' => 1, 'file' => $filename]);
                    } else {
                        $this->respond__json(['ok' => 0, 'file' => $filename]);
                    }
                } break; 
                case 'restore':{
                    if($this->do__restore()){
                        $this->respond__json(['ok' => 1]);
                    } else {
                        $this->respond__json(['ok' => 0]);
                    }
                } break;
                case 'remove': {
                    if($this->do__remove()){
                        $this->respond__json(['ok' => 1]);
                    } else {
                        $this->respond__json(['ok' => 0]);
                    }
                } break;
                case 'download': {
                    $this->do__download();
                } break;
                case 'upload': {
                    $this->do__upload();
                } break;
            }
        } else {
            $this->prt();
        }
    }
    
    public function do__download(){
        if(!$filename = $_REQUEST['filename'] ?? null){
            return;
        }             
        
        if(!\is_file($filepath = "{$this->dir}/{$filename}")){
            return;
        }
        
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
        exit;        
    }
    
    
    public function do__remove(){
        if(!$filename = $_REQUEST['filename'] ?? null){
            return;
        }             
        
        if(!\is_file($filepath = "{$this->dir}/{$filename}")){
            return;
        }
        
        unlink($filepath);
        return true;
    }
    
    public function do__upload(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_FILES['csv_file']['tmp_name'])) {
                $uploaded = $_FILES['csv_file'];
                $timestamp = date('Y-md-Hi-s');
                $path = "{$this->dir}/uploaded-[{$timestamp}]{$uploaded['name']}";
                \is_dir($d = \dirname($path)) or \mkdir($d, 0777, true);
                \move_uploaded_file($uploaded['tmp_name'],$path);
                //$this->validate_uploaded_csv();
            }
        }        
    }
    
    
    public function do__backup(): string {
        $pdo = $this->pdo();
        $filename = date('Y-md-Hi-s') . '-backup.csv';
        $path = "{$this->dir}/{$filename}";
        \is_dir($d = \dirname($path)) or \mkdir($d, 0777, true);
        $fp = fopen($path, 'w');

        foreach ($pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN) as $table) {
            fwrite($fp, "#, {$table}\n");
            $columns = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll();
            $headers = array_column($columns, 'Field');
            fwrite($fp, '@, ' . implode(', ', $headers) . "\n");

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll();
            foreach ($rows as $row) {
                $escaped = array_map(function ($v) {
                    if (is_null($v)) return '';
                    $v = (string)$v;
                    return str_contains($v, ',') || str_contains($v, '"') ? '"' . str_replace('"', '""', $v) . '"' : $v;
                }, $row);
                fwrite($fp, '$, ' . implode(', ', $escaped) . "\n");
            }
        }

        fclose($fp);
        return $filename;
    }
    
    
    public function do__restore(){
        \is_file($this->tracefile = "{$this->dir}/sql-trace.txt") AND unlink($this->tracefile);
        
        if(!$filename = $_REQUEST['filename'] ?? null){
            return;
        }             
        
        if(!\is_file($filepath = "{$this->dir}/{$filename}")){
            return;
        }

        
        $pdo = $this->pdo();

        $currentTable = null;
        $currentColumns = [];
        $tableTypes = [];

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        try {
            $fp = fopen($filepath, 'r');
            if (!$fp) {
                throw new \RuntimeException("Unable to open: {$filepath}");
            }

            while (($line = fgets($fp)) !== false) {
                $line = trim($line);
                if ($line === '') continue;

                $parts = str_getcsv($line);

                switch ($parts[0]) {
                    case '#':{
                        // New table
                        $currentTable = trim($parts[1] ?? '');
                        $pdo->exec("TRUNCATE TABLE `{$currentTable}`");
                        $pdo->exec("ALTER TABLE `{$currentTable}` AUTO_INCREMENT = 1");
                        $currentColumns = [];
                        $tableTypes[$currentTable] = [];

                        // Fetch column types
                        $stmt = $pdo->query("DESCRIBE `{$currentTable}`");
                        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
                            $colName = $col['Field'];
                            $type = strtolower($col['Type']);
                            $tableTypes[$currentTable][$colName] = $type;
                        }
                        \file_put_contents($this->tracefile, \json_encode($currentTable).PHP_EOL, FILE_APPEND | LOCK_EX);
                    } break;

                    case '@': {
                        $currentColumns = array_map('trim', array_slice($parts, 1));
                        \file_put_contents($this->tracefile, \json_encode($currentColumns).PHP_EOL, FILE_APPEND | LOCK_EX);
                    } break;

                    case '$':{
                        if ($currentTable && !empty($currentColumns)){
                            
                            $row = array_map('trim', array_slice($parts, 1));
                            $assoc = [];
                            foreach($currentColumns as $k => $colname){
                                if($colname){
                                    $assoc[$colname] = $row[$k];
                                }
                            }

                            // Type-aware casting
                            foreach ($assoc as $key => &$value) {
                                $type = $tableTypes[$currentTable][$key] ?? 'varchar';

                                if ($value === '') {
                                    if (preg_match('/^(int|float|double|decimal|date|datetime|timestamp|year)/', $type)) {
                                        $value = null;
                                    }
                                } elseif (preg_match('/^int/', $type)) {
                                    $value = (int) $value;
                                } elseif (preg_match('/^(float|double|decimal)/', $type)) {
                                    $value = (float) $value;
                                }
                                // else: leave strings/dates as-is
                            }
                            unset($value);

                            // Handle ID logic
                            if (isset($assoc['id']) && ($assoc['id'] === '0' || str_contains((string)$assoc['id'], '+'))) {
                                unset($assoc['id']);
                            }

                            $columns = array_keys($assoc);
                            $placeholders = array_map(fn($col) => ":$col", $columns);

                            $string = "INSERT INTO `{$currentTable}` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $placeholders) . ")";
                            \file_put_contents($this->tracefile, $string.PHP_EOL, FILE_APPEND | LOCK_EX);
                            $stmt = $pdo->prepare($string);
                            

                            $stmt->execute($assoc);
                        } else {
                            //ignore;
                        }
                    } break;
                }
            }

            fclose($fp);
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (\Throwable $e) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            throw $e;
        }
        
        return true;
    }
    
    
    public function validate_uploaded_csv($filepath) {
        $fp = fopen($filepath, 'r');
        $lineNo = 0;
        $errors = [];

        while (($line = fgets($fp)) !== false) {
            $lineNo++;
            $line = trim($line);
            if ($line === '') continue;

            $parts = str_getcsv($line);
            $symbol = $parts[0] ?? '';
            $id = $parts[1] ?? '';

            if (!in_array($symbol, ['#', '@', '$'], true)) {
                $errors[] = "Line {$lineNo}: Invalid symbol '{$symbol}'";
            } elseif ($symbol === '$' && !is_numeric($id)) {
                $errors[] = "Line {$lineNo}: ID '{$id}' is not numeric.";
            }
        }

        fclose($fp);

        if (empty($errors)) {
            echo "<div class='alert alert-success'>CSV passed validation.</div>";
        } else {
            echo "<div class='alert alert-danger'><strong>Validation Errors:</strong><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul></div>";
        }

        echo '<div><a href="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">Back</a></div>';
    }    
    
    
    
    public function prt(){
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Impex</title>
            <script>
                console.log("%c".padEnd(120, "#"),'color: magenta');
                const X_CSRF = "<?=\_\CSRF?>";
                if (typeof xui === 'undefined') {
                    xui = {
                        datasource: {}
                    };
                }

                if (typeof xui.TRACE === 'undefined') {
                    xui.TRACE = 6;
                    xui.TRACE_1 = ((xui.TRACE ?? 0) >= 1);
                    xui.TRACE_2 = ((xui.TRACE ?? 0) >= 2);
                    xui.TRACE_3 = ((xui.TRACE ?? 0) >= 3);
                    xui.TRACE_4 = ((xui.TRACE ?? 0) >= 4);
                    xui.TRACE_5 = ((xui.TRACE ?? 0) >= 5);
                    xui.TRACE_6 = ((xui.TRACE ?? 0) >= 6);
                    xui.TRACE_7 = ((xui.TRACE ?? 0) >= 7);
                    xui.TRACE_8 = ((xui.TRACE ?? 0) >= 8);
                    xui.TRACE_9 = ((xui.TRACE ?? 0) >= 9);
                    (xui.TRACE_7) && console.log({
                        xui
                    });
                }
            </script>

            
            <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
            <script src="https://code.jquery.com/jquery-3.2.1.js" crossorigin="anonymous"></script>

            <!-- jQuery UI -->
            <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>

            <!-- Latest compiled and minified CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <!-- Latest compiled and minified JavaScript -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
            
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        </head>
        <body>
        <div class="d-flex flex-column flex-nowrap" style="max-height:100vh; height:100vh;">
            <div class="flex-fill overflow-autoscrollable-shadow-on-sticky-top" style="height: 1px">
                <!-- OPEN _/xui/table/alpha::instance  -->
                <div class="d-flex flex-column flex-nowrap h-100">
                    <div class="flex-shrink-1">
                        <div class="d-flex justify-content-between pt-2 pb-2 bg-light border-bottom">
                            <div class="align-self-start me-2">
                            </div>
                            <div class="flex-fill">
                                <span class="h4">Impex</span>
                            </div>
                            <div class="align-self-end">
                                <div class="d-flex justify-content-between">
                                    <div class="btn-group me-1">
                                        <form id="upload-form">
                                            <input type="file" id="upload-input" name="csv_file" accept=".csv" style="display:none">
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="document.getElementById('upload-input').click()">Upload CSV</button>
                                        </form>
                                    </div>
                                    <div class="btn-group me-1">
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="do__action('backup')">Backup</button>
                                    </div>
                                    <form class="d-inline-block x-table-params-form">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm x-table-params-field" name="--p[search]" placeholder="Search" value="" aria-label="Search" aria-describedby="6870672dd3727">
                                            <button class="btn btn-sm btn-outline-secondary" type="submit" id="6870672dd3727"><i class="bi bi-search"></i></button>
                                            <a class="btn btn-sm btn-outline-danger" href="https://fw.local/web-github/klude-org/epx-pax/sandbox-04/--managecustomer">&times;</a>
                                        </div>
                                    </form>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default" onclick="window.location.reload()"><span class="bi bi-bootstrap-reboot"></span></button>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid" id="id-list">
                        <ul class="list-group mt-3">
                            <?php 
                                $list = [];
                                foreach(\glob($this->dir."/*.csv") as $f){
                                    $list[$f] = \filemtime($f);
                                }
                                \arsort($list);
                                if(\is_file($latest = $this->dir."/backup.csv")){
                                    \array_unshift($list, $latest);
                                }
                                foreach($list as $f => $filetime): ?>
                                <li class="list-group-item">
                                    <span title="<?=$hash = \hash_file("md5",$f)?>"><?=\basename($f)?> (<?=$hash?> )</span>
                                    <form class="float-end" action="" method="POST">
                                        <input hidden name="--csrf" value="<?=\_\CSRF?>">
                                        <input type="hidden" name="file" value="<?=\basename($f)?>">
                                        <div class="float-end">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="return confirm('This will change the database!!!\nAre you sure?') ? do__file_action('restore','<?=\basename($f)?>') : event.preventDefault()">Restore</button>
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><button type="button" class="dropdown-item" onclick="do__file_action('remove','<?=\basename($f)?>')">Remove</button></li>
                                                    <li><button type="button" class="dropdown-item" onclick="do__download('<?=\basename($f)?>')">Download</button></li>
                                                </ul>
                                            </div>                                    
                                        </div>
                                    </form>
                                </li>
                            <?php endforeach ?>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        
        <script>
        document.getElementById('upload-input').addEventListener('change', async function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('csv_file', file);

            try {
                const response = await fetch('?--action=upload', {
                    method: 'POST',
                    headers: {
                        // 'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Csrf-Token': X_CSRF
                    },
                    body: formData
                });
                if (!response.ok) {
                    alert('Upload failed.');
                    return;
                }

                const result = await response.text(); // or .json() depending on server response
                alert('Upload complete.');
                window.location.reload();

            } catch (err) {
                alert("Error: " + err.message);
            }
        });
        </script>
        
        <script>
        function do__action(action) {
            fetch(`?--action=${action}`)
            .then(response => response.json())
            .then(response => {
                window.location.reload();
            });
        }

        function do__file_action(action,file) {
            fetch(`?--action=${action}&filename=${file}`)
            .then(response => response.json())
            .then(response => {
                window.location.reload();
            });
        }
        
        async function do__download(file){
            const response = await fetch(`?--action=download&filename=${file}`);

            if (!response.ok) {
                alert("Download failed.");
                return;
            }

            const blob = await response.blob();
            const filename = response.headers.get('Content-Disposition')?.match(/filename="(.+?)"/)?.[1] || 'backup.csv';

            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            document.body.appendChild(a);
            a.click();
        }
        
        </script>        
        </body>
        </html>        
    <?php 
    }
    
    
})();


