<?php 
########################################################################################################################
#region
    /* 
                                               EPX-PAX-START
    PROVIDER : KLUDE PTY LTD
    PACKAGE  : EPX-PAX
    AUTHOR   : BRIAN PINTO
    RELEASED : 2025-07-04
    
    Copyright (c) 2017-2023 Klude Pty Ltd. https://klude.com.au

    The MIT License

    Permission is hereby granted, free of charge, to any person obtaining
    a copy of this software and associated documentation files (the
    "Software"), to deal in the Software without restriction, including
    without limitation the rights to use, copy, modify, merge, publish,
    distribute, sublicense, and/or sell copies of the Software, and to
    permit persons to whom the Software is furnished to do so, subject to
    the following conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
    LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
    OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
    WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
    
    */
#endregion
# ######################################################################################################################
# i'd like to be a tree - pilu (._.) // please keep this line in all versions - BP
# ######################################################################################################################
#region START
namespace _ { 
    
    \defined('_\PSTART') OR \define('_\PSTART', \microtime(true));
    1 AND \set_include_path(
        __DIR__.PATH_SEPARATOR
        .__DIR__.'/.local-abaca'.PATH_SEPARATOR
        .\get_include_path()
    );
    1 AND \spl_autoload_extensions("-#.php,/-#.php");
    1 AND \spl_autoload_register();
    global $START;
    $START ??= 'epx__start_0__pax__klude_org__github';
    if(!\class_exists($START)){
        (function(){

            0 AND \ini_set('display_errors', 0);
            0 AND \ini_set('display_startup_errors', 1);
            0 AND \ini_set('error_reporting', E_ALL);
            0 AND \error_reporting(E_ALL);
            $fault__fn = function($ex = null){
                $FAULTS[\microtime(true).':'.\uniqid()] = $ex;
                $intfc = $GLOBALS['INTFC']
                    ?? (empty($_SERVER['HTTP_HOST']) 
                        ? 'cli'
                        : $_SERVER['HTTP_X_REQUEST_INTERFACE'] ?? 'web'
                    )
                ;
                switch($intfc){
                    case 'cli':{
                        echo "\033[91m\n"
                            .$ex::class.": {$ex->getMessage()}\n"
                            ."File: {$ex->getFile()}\n"
                            ."Line: {$ex->getLine()}\n"
                            ."\033[31m{$ex}\033[0m\n"
                        ;
                    } break;
                    case 'web':{
                        \http_response_code(500);
                        while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                        exit(<<<HTML
                            <pre style="overflow:auto; color:red;border:1px solid red;padding:5px;">{$ex}</pre>
                        HTML);
                    } break;
                    default:{
                        \http_response_code(500);
                        while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                        \header('Content-Type: application/json');
                        exit(\json_encode([
                            'status' => "error",
                            'message' => $ex->getMessage(),
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    } break;
                }
            };
            \set_exception_handler(function($ex) use($fault__fn){
                $fault__fn($ex);
            });
            \set_error_handler(function($severity, $message, $file, $line) use($fault__fn){
                try{
                    throw new \ErrorException(
                        $message, 
                        0,
                        $severity, 
                        $file, 
                        $line
                    );
                } catch(\Throwable $ex) {
                    if(!\defined('_\SIG_END')){
                        $fault__fn($ex);
                    } else {
                        throw $ex;
                    }
                }
            });
            
            global $START;
            if(!\preg_match(
                "#^(?<w_plugin>epx__(?<w_partno>.+)__(?<w_repo>.+)__(?<w_owner>.+)__(?<w_domain>[^/]+))#",
                $START,
                $m
            )){
                throw new \Exception("Failed: Invalid START:'{$START}'");
            }
            \extract($mx = \array_filter($m, fn($k) => !is_numeric($k), \ARRAY_FILTER_USE_KEY));
            $w_owner = \str_replace('_','-',$w_owner);
            $w_repo = "epx-".\str_replace('_','-',$w_repo);
            $u_path = "{$w_plugin}.zip";
            $url = "https://raw.githubusercontent.com/{$w_owner}/{$w_repo}/main/plugins/{$u_path}";
            $zip_path = __DIR__."/.local-https-".\str_replace('/','~', \substr(\strtok($url,'?'),8));
            if(!($contents = \file_get_contents($url))){
                throw new \Exception("Failed: Couldn't Download START:'{$START}'");
            }
            \file_put_contents($zip_path, $contents);
            if(!\is_file($zip_path)){
                throw new \Exception("Failed: Couldn't Save Zip for START:'{$START}'");
            } 
            if (!(($zip = new \ZipArchive)->open($zip_path) === true)) {
                throw new \Exception("Failed: Start Plugin is corrupted");
            }
            try {
                $df = function($d) use (&$df) {
                    array_map(
                        function($f) use ($d, &$df) { $p = "$d/$f"; is_dir($p) ? $df($p) : unlink($p);}, 
                        (\is_dir($d) ? array_diff(scandir($d), ['.','..']) : [])
                    );
                    \is_dir($d) AND rmdir($d);
                };
                $df($extractTo = __DIR__."/{$START}");
                $extracted = false;
                $sub_offset = $START;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $fileName = $zip->getNameIndex($i);
                    if (\str_starts_with($fileName, $sub_offset)) {
                        $target_path = $extractTo.substr($fileName, strlen($sub_offset));
                        if (str_ends_with($fileName, '/')) {
                            is_dir($target_path) OR @mkdir($target_path, 0777, true);
                        } else {
                            is_dir($d = dirname($target_path)) OR @mkdir($d, 0777, true);
                            file_put_contents($target_path, $zip->getFromIndex($i));
                        }
                        $extracted = true;
                    }
                }
            } finally {
                $zip->close();
                \unlink($zip_path);
            }
            if(!\class_exists($START)){
                throw new \Exception("Failed: Couldn't Find START:'{$START}'");
            } 
        })();
    }
    return $START::_();
}