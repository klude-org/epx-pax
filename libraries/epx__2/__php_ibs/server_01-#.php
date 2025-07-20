<?php namespace __php_ibs;

abstract class server extends \stdClass {
    
    protected function __construct(){
        // Configuration
        $this->php_exe = "php";
        $this->host = $_SERVER['FX__IBS_HOST'] ?? 'localhost';
        $this->port = $_SERVER['FX__IBS_PORT'] ?? 8000;
        $this->docRoot = \_\SITE_DIR;
        $this->lockFile = \_\LOCAL_DIR.'/php_ibs.lock';
        $this->url = "http://{$this->host}:{$this->port}";
    }
    
    // Check if server is already running by opening the port
    public function isServerRunning(): bool {
        $fp = null;
        try{
            $fp = @fsockopen($this->host, $this->port, $errno, $errstr, 1);
            return $fp ? true : false;
        } catch (\Throwable $ex){
            return false;
        } finally {
            $fp AND fclose($fp);
        }
    }
    
    public function start_server(){
        // If server is running, do nothing
        if ($this->isServerRunning()) {
            echo "PHP server already running at {$this->url}\n";
            return;
        }

        // Start server and write lock file
        echo "Starting PHP server at {$this->url}...\n";

        // Build the command to run PHP -S in the background
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // Windows
            $cmd = "start \"PHP Dev Server\" /min php -S {$this->host}:{$this->port} -t \"{$this->docRoot}\"";
            pclose(popen("cmd /c $cmd", "r"));
        } else {
            // Unix/macOS
            $cmd = "php -S {$this->host}:{$this->port} -t \"{$this->docRoot}\" > /dev/null 2>&1 &";
            exec($cmd);
        }

        // Write a simple lock file
        file_put_contents($this->lockFile, \json_encode([
            'host' => $this->host,
            'port' => $this->port,
            'root' => $this->docRoot,
            'exe' => $this->php_exe,
            'class' => static::class,
            'timestamp' => time(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "Server launched. Lock file created: {$this->lockFile}\n";
    }
    
    public function stop_server(){
        // 1. Check if running
        if (!$this->isServerRunning()) {
            echo "PHP server is not running on {$this->host}:{$this->port}\n";
        } else {
            echo "PHP server is running. Attempting to stop...\n";
            $this->stopPhpServer();
        }

        // 2. Remove lock file if it exists
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
            echo "Lock file removed.\n";
        } else {
            echo "No lock file found.\n";
        }        
    }
    
    // Try to find and kill the server
    public function stopPhpServer(): bool {
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // Windows — try to kill matching php.exe with port
            $find = "netstat -ano | findstr :{$this->port}";
            exec("cmd /c \"$find\"", $lines);

            $pids = [];
            foreach ($lines as $line) {
                if (preg_match('/\s+LISTENING\s+(\d+)/', $line, $m)) {
                    $pids[] = $m[1];
                }
            }

            foreach ($pids as $pid) {
                // Confirm it's a php.exe
                exec("tasklist /FI \"PID eq $pid\" /FI \"IMAGENAME eq php.exe\"", $out);
                if (count($out) > 1) {
                    exec("taskkill /PID $pid /F");
                    echo "Killed PHP process (PID: $pid)\n";
                    return true;
                }
            }

            echo "No matching PHP server found to kill.\n";
            return false;
        } else {
            // Unix/macOS — kill php -S on port
            $cmd = "lsof -i :{$this->port} -sTCP:LISTEN -t";
            exec($cmd, $pids);

            if (!empty($pids)) {
                foreach ($pids as $pid) {
                    exec("kill -9 $pid");
                    echo "Killed PHP process (PID: $pid)\n";
                }
                return true;
            } else {
                echo "No PHP process found on port {$this->port}.\n";
                return false;
            }
        }
    }
    
}