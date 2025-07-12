<?php 
$browser_exe = $_REQUEST[1] ?? 'msedge';
$sub = (($r = \trim($_REQUEST[2] ?? '', '/')) ? "/{$r}" : "");
$url = \_\SITE_URL.$sub;
\system("start {$browser_exe} {$url}");
