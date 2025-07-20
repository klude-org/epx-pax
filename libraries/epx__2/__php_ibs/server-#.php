<?php namespace __php_ibs;

abstract class server extends \stdClass
{
    protected string $php_exe;
    protected string $host;
    protected int $port;
    protected string $docRoot;
    protected string $lockFile;
    protected string $url;

    protected function __construct()
    {
        $this->php_exe = 'php';
        $this->host = $_SERVER['FX__IBS_HOST'] ?? 'localhost';
        $this->port = (int)($_SERVER['FX__IBS_PORT'] ?? 8000);
        $this->docRoot = \_\SITE_DIR;
        $this->lockFile = \_\LOCAL_DIR . '/php_ibs.lock';
        $this->url = "http://{$this->host}:{$this->port}";
    }

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

    public function start_server(): void
    {
        if ($this->isServerRunning()) {
            echo "PHP server already running at {$this->url}\n";
            return;
        }

        echo "Starting PHP server at {$this->url}...\n";

        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            $cmd = "start \"PHP Dev Server\" /min cmd /c \"{$this->php_exe} -S {$this->host}:{$this->port} -t \"{$this->docRoot}\"\"";
            pclose(popen("cmd /c $cmd", "r"));
            // Delay slightly to let the server start and register in tasklist
            sleep(1);
            $pid = $this->detectPhpPidWindows();
        } else {
            $cmd = "{$this->php_exe} -S {$this->host}:{$this->port} -t \"{$this->docRoot}\" > /dev/null 2>&1 & echo $!";
            $pid = (int)shell_exec($cmd);
        }

        $lock = [
            'pid' => $pid,
            'host' => $this->host,
            'port' => $this->port,
            'root' => $this->docRoot,
            'exe' => $this->php_exe,
            'class' => static::class,
            'timestamp' => time(),
        ];
        file_put_contents($this->lockFile, json_encode($lock, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "Server launched. Lock file created: {$this->lockFile}\n";
    }

    public function stop_server(): void
    {
        $lock = file_exists($this->lockFile) ? json_decode(file_get_contents($this->lockFile), true) : null;
        $pid = $lock['pid'] ?? null;

        if ($pid && $this->killProcessByPid($pid)) {
            echo "Killed PHP server using PID: $pid\n";
        } elseif ($this->isServerRunning()) {
            echo "PID not available or stale. Attempting to stop by port...\n";
            $this->stopPhpServerByPort();
        } else {
            echo "Server is not running on {$this->host}:{$this->port}\n";
        }

        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
            echo "Lock file removed.\n";
        }
    }

    protected function killProcessByPid(int $pid): bool
    {
        if ($pid <= 0) return false;

        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            exec("tasklist /FI \"PID eq $pid\" /FI \"IMAGENAME eq php.exe\"", $out);
            if (count($out) > 1) {
                exec("taskkill /PID $pid /F");
                return true;
            }
        } else {
            exec("kill -9 $pid", $out, $code);
            return $code === 0;
        }

        return false;
    }

    protected function stopPhpServerByPort(): bool
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            exec("netstat -ano | findstr :{$this->port}", $lines);
            foreach ($lines as $line) {
                if (preg_match('/\s+LISTENING\s+(\d+)/', $line, $m)) {
                    $pid = (int)$m[1];
                    exec("tasklist /FI \"PID eq $pid\" /FI \"IMAGENAME eq php.exe\"", $out);
                    if (count($out) > 1) {
                        exec("taskkill /PID $pid /F");
                        echo "Killed PHP process (PID: $pid)\n";
                        return true;
                    }
                }
            }
        } else {
            exec("lsof -i :{$this->port} -sTCP:LISTEN -t", $pids);
            foreach ($pids as $pid) {
                exec("kill -9 $pid");
                echo "Killed PHP process (PID: $pid)\n";
            }
            return !empty($pids);
        }

        echo "No matching PHP server found on port {$this->port}.\n";
        return false;
    }

    protected function detectPhpPidWindows(): ?int
    {
        exec("netstat -ano | findstr :{$this->port}", $lines);
        foreach ($lines as $line) {
            if (preg_match('/\s+LISTENING\s+(\d+)/', $line, $m)) {
                return (int)$m[1];
            }
        }
        return null;
    }
}
