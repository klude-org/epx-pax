
in php. please can you write me up a utitlity for database tables (data backup as csv)
1. All tables in 1 CSV file in the following format

    #, customer__tbl
    @, id, first_name, last_name, address, phone, mobile
    $, 1, Jon, Doe, "2540 Imp St, Mora BA 1110", 040123451, 990234591
    $, 2, Jane, Doe, "2540 Imp St, Mora BA 1110", 0403456201, 990201991
    $, 3, Puck, Duick, "2540 Imp St, Mora BA 1110", 0401034561, 990256551
    #, orders__tbl
    @, id, order_code, customer_id, address, phone, mobile
    $, 1, O-001, 1, "2540 Imp St, Mora BA 1110", 040123451, 990234591
    $, 2, O-002, 3, "2540 Imp St, Mora BA 1110", 0403456201, 990201991
    $, 3, O-003, 4, "2540 Imp St, Mora BA 1110", 0401034561, 990256551


2. Write it to the file with format Y-md-Hi-s-backup_name.csv

3. If I drop a csv on to the page it must check the format - first 2 columns on every line - [symbol, id|integer]

I have provided a sample below

<?php

(new class extends \stdClass {

    public function __construct(){
        $this->hostname = $_ENV['DB_HOSTNAME'] ?? 'localhost';
        $this->database = $_ENV['DB_DATABASE'] ?? '';
        $this->char_set = $_ENV['DB_CHAR_SET'] ?? 'utf8mb4';
        $this->dir = __DIR__;
    }
    
    public function pdo(){
        try {
            return $this->pdo ?? $this->pdo = new \PDO(
                "mysql:host={$hostname};dbname={$database};charset={$char_set}", 
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
    
    public function __invoke(){
        if($action = $_REQUEST['--action']){
            
        } else {
            $this->prt();
        }
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
            <script>xui = {};</script>
            
            <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
            <script src="https://code.jquery.com/jquery-3.2.1.js" crossorigin="anonymous"></script>
            <!-- Bootstrap Icons -->
            <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
            <!-- Bootstrap minified CSS -->
            <link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
            <!-- Bootstrap minified JavaScript -->
            <script src="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
            
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
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="do__backup()">Backup</button>
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
                            <li class="list-group-item">
                                <span title="c4ff78a64e7c5c53452fcefd9d11caf9">2025-0711-0106-27-backup</span>
                                <form class="float-end" action="" method="POST">
                                    <input hidden name="--csrf" value="dadce63a7e85f3924fcea3b96874585f">
                                    <input type="hidden" name="file" value="db-backup.json">
                                    <div class="float-end">
                                        <div class="btn-group">
                                            <input type="submit" class="btn btn-outline-primary btn-sm" name="--action" value="Restore" onclick="return confirm('This will change the database!!!\nAre you sure?')) ? true : event.preventDefault()">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Download"></li>
                                                <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Remove"></li>
                                            </ul>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <li class="list-group-item">
                                <span title="c4ff78a64e7c5c53452fcefd9d11caf9">2025-0711-0246-27-backup</span>
                                <form class="float-end" action="" method="POST">
                                    <input hidden name="--csrf" value="dadce63a7e85f3924fcea3b96874585f">
                                    <input type="hidden" name="file" value="db-backup-2025-0711-0246-27.json">
                                    <div class="float-end">
                                        <div class="btn-group">
                                            <input type="submit" class="btn btn-outline-primary btn-sm" name="--action" value="Restore" onclick="return confirm('This will change the database!!!\nAre you sure?')) ? true : event.preventDefault()">
                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Download"></li>
                                                <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Remove"></li>
                                            </ul>
                                        </div>
                                    </div>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </body>
        </html>        
    <?php 
    }
    
    
})();


