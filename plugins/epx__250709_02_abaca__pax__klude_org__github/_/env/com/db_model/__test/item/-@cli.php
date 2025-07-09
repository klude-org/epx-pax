<?php 

echo __FILE__.PHP_EOL;
echo \json_encode([$this, $this->record()], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
