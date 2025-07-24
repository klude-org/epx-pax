<?php 
namespace {
    \defined('_\MSTART') OR \define('_\MSTART', \microtime(true));
    \define('_\START_FILE', \str_replace('\\','/', __FILE__));
    \define('_\START_DIR', \dirname(\_\START_FILE));
    \define('_\START_OB', \ob_get_level());
}
namespace {(function(){
    1 AND \ini_set('display_errors', 0);
    1 AND \ini_set('display_startup_errors', 1);
    1 AND \ini_set('error_reporting', E_ALL);
    0 AND \error_reporting(E_ALL);
    $fault__fn = function($ex = null){
        $FAULTS[\microtime(true).':'.\uniqid()] = $ex;
        $intfc = (\defined('_\INTFC') ? \_\INTFC : null)
            ?? $GLOBALS['INTFC']
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
                exit(1);
            } break;
            case 'web':{
                \http_response_code(500);
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                echo <<<HTML
                    <pre style="overflow:auto; color:red;border:1px solid red;padding:5px;">{$ex}</pre>
                HTML;
                exit(1);
            } break;
            default:{
                \http_response_code(500);
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', -1);
                \header('Content-Type: application/json');
                echo \json_encode([
                    'status' => "error",
                    'message' => $ex->getMessage(),
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                exit(1);
            } break;
        }
    };
    \set_exception_handler(function($ex) use($fault__fn){
        $fault__fn($ex);
    });
    \set_error_handler(function($severity, $message, $file, $line) use($fault__fn){
        throw new \ErrorException(
            $message, 
            0,
            $severity, 
            $file, 
            $line
        );
    });
    \register_shutdown_function(function() use($fault__fn){
        if(\class_exists(\_\dx::class, false)){
            // do nothing - assuming if dx exists it will have handled this!
        } else {
            if(\defined('_\SIG_ABORT') && \_\SIG_ABORT < 0){
                exit();
            }
            if(\defined('_\SIG_END')){
                $GLOBALS['_TRACE'][] = "Invalid SIG_END setting or Duplicate call to Root Finalizer";
                exit();
            } else {
                \define('_\SIG_END', \microtime(true));
            };
            \register_shutdown_function(function() use($fault__fn){
                try{
                    if($error = \error_get_last()){ 
                        \error_clear_last();
                        throw new \ErrorException(
                            $error['message'], 
                            0,
                            $error["type"], 
                            $error["file"], 
                            $error["line"]
                        );
                    } 
                } catch(\Throwable $ex) {
                    $fault__fn($ex);
                }
            });
        }
    });
})();}    
namespace { (new class extends \stdClass { 
# ######################################################################################################################
#region CTOR
public function __construct(){ 
    $this->env = (object)[];
    $this->vars = [];
    $this->i__boot_start();
} 
#endregion
# ######################################################################################################################
#region INVOKE
public function __invoke(){
    $this->i__prt();
}
#endregion
# ######################################################################################################################
#region boot/start
private function i__boot_start(){
    \set_include_path(
        \_\START_DIR.PATH_SEPARATOR
        .(\is_dir($d = \dirname(\_\START_DIR,2).'/plugins') ? $d.PATH_SEPARATOR : '')
        .\get_include_path()
    );
    global $_;
    global $_ALT;
    global $_TRACE;
    (isset($_) && \is_array($_)) OR $_ = [];
    (isset($_ALT) && \is_array($_ALT)) OR $_ALT = [];
    (isset($_TRACE) && \is_array($_TRACE)) OR $_TRACE = [];
    \define('_\SIG_START', \_\MSTART); //always master_start!
    \define('_\CWD', \str_replace('\\','/', \getcwd()));
    \define('_\ABACA_DIR', \_\START_DIR.'/abaca');
    \define('_\SCRATCH_DIR', \dirname(\_\START_DIR).'/.local');
    \define('_\OB_OUT', \ob_get_level());
    !empty($_SERVER['HTTP_HOST']) AND \ob_start();
    \define('_\OB_TOP', \ob_get_level());
    \define('_\REGEX_CLASS_FQN', "/^(([a-zA-Z_\x80-\xff][\\\\a-zA-Z0-9_\x80-\xff]*)\\\)?([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)$/");
    \define('_\REGEX_CLASS_QN', "^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$");
    \define('_\BR', '<br>');
    \define('_\PHP_TSP_DEFAULTS', [
        'handler' => 'spl_autoload',
        'extensions' => \spl_autoload_extensions(),
        'path' =>  \get_include_path(),
    ]);
    \define('_\START_EN', \is_array($a = $_['START_EN'] ?? null) ? $a : []);
}
#endregion
# ######################################################################################################################
#region GEN
public function file($f){
    return \stream_resolve_include_path($f);
}
#endregion
# ######################################################################################################################
#region NAV
public function nav_tree(){
    return [];
}
public function sidebar(){
    return $this->sidebar ?? $this->sidebar = (function(){ 
        if($file = $this->file('sidebar-$.json')){
            return \json_decode(\file_get_contents($file));
        } else {
            return (object)['nav' => $this->nav_tree()];
        }
    })();
}
#endregion
# ######################################################################################################################
#region PRT
public function i__prt(){ 
    include 'epx__studio/-v.php'; 
}})(); }
