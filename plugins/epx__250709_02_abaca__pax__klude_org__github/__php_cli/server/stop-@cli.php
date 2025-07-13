<?php
(new class extends \__php_cli\xdbg_server{
    public function __invoke(){
        $this->stop_server();
    }
})();
