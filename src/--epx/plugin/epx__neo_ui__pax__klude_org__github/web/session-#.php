<?php namespace epx__neo_ui__pax__klude_org__github\web;

class session implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\singleton__t;
    
    public function __construct(){
        if(\session_status() == PHP_SESSION_NONE) {
            //* if the primary starter did the session it would have managed the auth
            //* this part will be scipped
            \session_name(\_\KEY); 
            \session_start();
            if(($_ENV['AUTH']['EN'] ?? true)){
                if(($_SESSION['--AUTH']['en'] ?? false) !== true){
                    if(!($_SESSION['--AUTH']['login_in_progress'] ?? false)){
                        $_SESSION['--AUTH'] = [];
                        $_SESSION['--AUTH']['login_in_progress'] = 1;
                        \header("Location: ". \strtok($_SERVER['REQUEST_URI'],'?'));
                        exit();
                    }
                }
                if(
                    isset($_GET['--logout'])
                    || isset($_GET['--signout'])
                ){
                    $_SESSION['--AUTH'] = [];
                    \header("Location: ". \strtok($_SERVER['REQUEST_URI'],'?'));
                    exit();
                }
            }
        }
        \define('_\SESSION_PATH', \_\KEY.'/'.\session_id());
        isset($_SESSION['--CSRF']) OR $_SESSION['--CSRF'] = \md5(uniqid('csrf-'));
        \is_array($_SESSION['--AUTH'] ?? null) OR $_SESSION['--AUTH'] = [];
        
        \define('_\CSRF', $_SESSION['--CSRF']);
        \define('_\FLASH', $_SESSION['--FLASH'] ?? []);
        
        $_SESSION['--FLASH'] = [];
        if($_ENV['SESSION']['CSRF']['EN'] ?? true){
            $token = $_REQUEST['--csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if(
                \in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','PATCH','DELETE'])
                && ($token) != ($_SESSION['--CSRF'] ?? null)
            ){
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                \http_response_code(406);
                exit('406: Not Acceptable');
            }
        }
        ($sess_cfg = $_SESSION['_'] ?? []) 
            AND $_ = \array_replace($_, $sess_cfg ?? [])
        ;
    }
    
    public function offsetSet($n, $v):void { 
        $_SESSION[$n] = $v;
    }
    public function offsetExists($n):bool { 
        return isset($_SESSION[$n]);
    }
    public function offsetUnset($n):void { 
        unset($_SESSION[$n]);
    }
    public function &offsetGet($n):mixed { 
        return $_SESSION[$n];
    }
    public function jsonSerialize():mixed {
        return [ '_' => static::$_ ] + (array) $this;
    }
    
}
