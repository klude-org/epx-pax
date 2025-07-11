<?php

(new class extends \stdClass {

    public function __construct() {
        $this->hostname = $_ENV['DB_HOSTNAME'] ?? 'localhost';
        $this->database = $_ENV['DB_DATABASE'] ?? '';
        $this->char_set = $_ENV['DB_CHAR_SET'] ?? 'utf8mb4';
        $this->dir = __DIR__;
    }

    public function pdo() {
        try {
            return $this->pdo ??= new \PDO(
                "mysql:host={$this->hostname};dbname={$this->database};charset={$this->char_set}",
                $_ENV['DB_USERNAME'] ?? 'root',
                $_ENV['DB_PASSWORD'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function __invoke() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_REQUEST['--action'] ?? '';
            $filename = basename($_POST['file'] ?? '');

            if ($action === 'Backup') {
                $this->create_csv_backup();
                return;
            }

            if ($action === 'Download' && $filename && is_file("{$this->dir}/$filename")) {
                header('Content-Type: text/csv');
                header("Content-Disposition: attachment; filename=\"$filename\"");
                readfile("{$this->dir}/$filename");
                exit;
            }

            if ($action === 'Remove' && $filename && is_file("{$this->dir}/$filename")) {
                unlink("{$this->dir}/$filename");
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }

            if ($action === 'Restore' && $filename && is_file("{$this->dir}/$filename")) {
                $this->validate_uploaded_csv("{$this->dir}/$filename");
                return;
            }

            if ($_FILES['csv_file'] ?? false) {
                $this->validate_uploaded_csv($_FILES['csv_file']['tmp_name']);
                return;
            }
        }

        $this->prt();
    }

    public function create_csv_backup() {
        $pdo = $this->pdo();
        $filename = date('Y-md-Hi-s') . '-backup.csv';
        $path = "{$this->dir}/{$filename}";
        $fp = fopen($path, 'w');

        foreach ($pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN) as $table) {
            fwrite($fp, "#, {$table}\n");

            $columns = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll();
            $headers = array_column($columns, 'Field');
            fwrite($fp, '@, ' . implode(', ', $headers) . "\n");

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll();
            foreach ($rows as $row) {
                $escaped = array_map(fn($v) => is_null($v) ? '' : (str_contains($v, ',') ? "\"$v\"" : $v), $row);
                fwrite($fp, '$, ' . implode(', ', $escaped) . "\n");
            }
        }

        fclose($fp);
        header('Content-Type: text/plain');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        readfile($path);
        exit;
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

    public function prt() {
        $files = glob($this->dir . '/*-backup.csv');
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Impex</title>
            <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
            <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
            <link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </head>
        <body>
        <div class="container mt-4">
            <h4>Impex - Database CSV Backup</h4>

            <div class="mb-3">
                <form method="POST">
                    <input type="hidden" name="--action" value="Backup">
                    <button class="btn btn-success">Create Backup</button>
                </form>
            </div>

            <div class="mb-4">
                <form method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <input type="file" name="csv_file" accept=".csv" class="form-control" required>
                        <button class="btn btn-primary">Upload & Validate</button>
                    </div>
                </form>
            </div>

            <h5>Existing Backups</h5>
            <ul class="list-group">
                <?php foreach ($files as $file):
                    $basename = basename($file);
                    $title = md5_file($file);
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span title="<?= $title ?>"><?= htmlspecialchars($basename) ?></span>
                        <form class="d-flex gap-2" method="POST">
                            <input type="hidden" name="file" value="<?= htmlspecialchars($basename) ?>">
                            <div class="btn-group">
                                <input type="submit" class="btn btn-outline-primary btn-sm" name="--action" value="Restore"
                                       onclick="return confirm('This will change the database!!!\nAre you sure?')">
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><input type="submit" class="dropdown-item" name="--action" value="Download"></li>
                                    <li><input type="submit" class="dropdown-item" name="--action" value="Remove"></li>
                                </ul>
                            </div>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        </body>
        </html>
        <?php
    }

})();
