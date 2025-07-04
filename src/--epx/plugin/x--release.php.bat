::<?php echo "\r   \r"; if(0): ?>
@echo off
php "%~f0" %*
exit /b 0
<?php endif;

(new class extends \stdClass {

    public function __construct(){
        $this->PLUGINS_DIR = __DIR__ . "/../../../plugins";
        $this->ARCHIVE_DIR = __DIR__ . "/../../../plugins-archive/.local";
        $this->SOURCE_DIR = __DIR__;
    }

    public function __invoke(){
        $this->ensureDir($this->PLUGINS_DIR);
        $this->ensureDir($this->ARCHIVE_DIR);
        $dirs = array_filter(glob($this->SOURCE_DIR . '/*'), 'is_dir');

        foreach ($dirs as $dirPath) {
            $dirName = basename($dirPath);
            $zipFile = "{$this->PLUGINS_DIR}/{$dirName}.zip";

            // Always recreate the base zip
            $this->createZip($dirPath, $zipFile, $dirName);

            $hash = hash_file("sha256", $zipFile);
            $pattern = "{$this->ARCHIVE_DIR}/{$dirName}-*-{$hash}.zip";

            // Only make hash-named copy if not already exists
            if (empty(glob($pattern))) {
                $timestamp = date("Y-md-Hi-s");
                $hashFile = "{$this->ARCHIVE_DIR}/{$dirName}-{$timestamp}-{$hash}.zip";
                copy($zipFile, $hashFile);
                echo "Copied: {$hashFile}\n";
            } else {
                echo "Skipped copy (already exists for SHA): $dirName\n";
            }
        }
    }

    private function createZip(string $sourceDir, string $zipFile, string $topLevelName): void {
        if (file_exists($zipFile)) {
            unlink($zipFile);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
            throw new RuntimeException("Cannot create zip file: $zipFile");
        }

        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($rii as $file) {
            $filePath = $file->getPathname();
            $relativePath = substr($filePath, strlen($sourceDir) + 1);
            $zipPath = $topLevelName . '/' . $relativePath;
            $zip->addFile($filePath, $zipPath);
        }

        $zip->close();
    }

    private function ensureDir($path): void {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

})();
