::<?php echo "\r   \r"; if(0): ?>
@echo off
C:/xampp/current/php__xdbg/php.exe "%~f0" %*
:: php "%~f0" %*
exit /b 0
<?php endif;

(new class extends \stdClass {

    public function __construct(){ }

    public function __invoke(){
        $this->create_manifest();
    }
    
    private function create_manifest(){
        $sourceDir = \dirname(__DIR__);
        $manifest_json = $sourceDir.DIRECTORY_SEPARATOR.'.manifest.json';
        $manifest_files = [];
        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($rii as $file) {
            $filePath = $file->getPathname();
            $relativePath = \str_replace('\\','/', substr($filePath, strlen($sourceDir) + 1));
            if(
                (\dirname($filePath) !== __DIR__)
                && $filePath != $manifest_json
                //&& $relativePath != '.start.bat'
            ){
                $manifest_files[$relativePath] = [ 'hash_sha256' => \hash_file('sha256', $filePath) ];
            }
        }
        \file_put_contents($manifest_json, \json_encode(['files' => $manifest_files], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function ensureDir($path): void {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

})();
