<?php

(new class extends \__php_ibs\std_server {
    public function __invoke(){
        $this->start_server();
    }
})();

