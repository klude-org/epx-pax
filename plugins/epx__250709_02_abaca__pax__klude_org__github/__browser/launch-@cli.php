<?php 
$browser_exe = $_REQUEST[1] ?? 'msedge';
$url = \_\SITE_URL;
\system("start {$browser_exe} {$url}");
