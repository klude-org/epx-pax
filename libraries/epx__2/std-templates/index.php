<?php 
1 AND $_['START_EN']['cfg_mode'] = 1;
if(
    \is_file($f = __DIR__."/--epx/.start.php")
    || \is_file($f = $_SERVER['DOCUMENT_ROOT']."/--epx/.start.php")
){
    (include $f)();
} else {
    http_response_code(500); exit('Invalid START');
}