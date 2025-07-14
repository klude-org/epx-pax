<?php
(new class extends \__php_ibs\xdbg_server{
    public function __invoke(){
        if (!$this->isServerRunning()) {
            echo "Server is not running at {$this->url}\n";
            $this->start_server();
        }
        
        // Cross-platform browser launch
        echo "Opening browser at {$this->url}...\n";

        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // Windows
            exec("start \"\" \"{$this->url}\"");
        } elseif (PHP_OS === 'Darwin') {
            // macOS
            exec("open \"{$this->url}\"");
        } else {
            // Linux
            exec("xdg-open \"{$this->url}\"");
        }
    }
})();



