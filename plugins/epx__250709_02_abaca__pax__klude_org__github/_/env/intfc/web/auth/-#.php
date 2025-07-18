<?php namespace _\env\intfc\web;

class auth extends \stdClass implements \ArrayAccess, \JsonSerializable {
    
    use \_\i\singleton__t;
    use \_\i\my\data__t;
    
    public function __construct(){
        
        $this->fn = (object)[];
        $this->fn->route_not_found = function(){
            \http_response_code(404);
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
            exit("404 Not Found: ".$_SERVER['REQUEST_URI']);  
        };
        $this->fn->invalid_request = function(){
            \http_response_code(404);
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
            exit("404 Not Found: ".$_SERVER['REQUEST_URI']);  
        };
        $this->dispatch__ffn = function(\closure $f){
            ($f)();
            exit(0);
        };
        $this->abort__ffn = function(int $code, string $message){
            return function() use($code, $message){
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
                \http_response_code($code);
                exit($message);
            };
        };
        $this->redirect__ffn = function($goto){
            return function() use($goto){
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
                \header("Location: ".$goto);
                exit();
            };
        };
        
        if(!$this->needs_authentication()){
            $this->role = \_\REQ['role'] ?? null ?: $this['default_role'];
            $this->portal = \_\REQ['portal'] ?? null ?: '';
            $this->role_data = $this->my_data("roles/{$this->role}");
            $this->portals = $this->role_data['portals'] ?? [];
        }
    }
    
    public function offsetSet($n, $v):void { 
        $_SESSION['--AUTH'][$n] = $v;
    }
    public function offsetExists($n):bool { 
        return isset($_SESSION['--AUTH'][$n]);
    }
    public function offsetUnset($n):void { 
        unset($_SESSION['--AUTH'][$n]);
    }
    public function &offsetGet($n):mixed { 
        return $_SESSION['--AUTH'][$n];
    }
    public function jsonSerialize():mixed {
        return [ '_' => $_SESSION['--AUTH'] ?? null ] + (array) $this;
    }

    public function needs_authentication(){
        return \_\REQ['portal'] && $this['login_in_progress'];
    }
    
    public function needs_permission(){
        return \_\REQ['portal'] && (!$this['login_in_progress']) && $this['en'];
    }
    
    public function get_view_file($path){
        return \_::file($f[] = "_/xui/auth/{$path}", '-v.php')
            ?: \_::file($f[] = static::class."/{$path}", '-v.php') 
        ;
    }
    
    public function get_view($path){
        if($f = $this->get_view_file($path)){
            return function() use($f){
                while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
                include $f;
            };
        }
    }
    
    public function route(){
        return $this->route__authentication() 
            ?: $this->route__option()
            ?: $this->route__permissions()
        ;
    }
    
    public function route__authentication(){
        if($this->needs_authentication()){
            if($action = $_REQUEST['--action'] ?? null){
                if($action == 'login'){
                    global $_;
                    if($username = $_POST['username'] ?? false){
                        $password = $_POST['password'] ?? '';
                        if(
                            \class_exists($c = \_\user::class)
                            && method_exists($c, '_')
                        ){
                            $user = (array) $c::_($username);
                        } else {
                            $users = $_['USERS'] ?? [
                                'admin' => [
                                    'name' => 'Admin',
                                    'password' => '`pass',
                                    'default_role' => 'admin',
                                    'roles' => ['*'],
                                ],
                            ]; 
                            $user = $users[$username] ?? [];
                        }
                        if($user){
                            $pass = (($p = ($user['password'] ?? null) ?: "`")[0] == "`") 
                                ? \md5(\substr($p, 1))
                                : $p
                            ;
                            $streq__fn = function ($a, $b) {
                                //credits: https://blog.ircmaxell.com/2014/11/its-all-about-time.html
                                $ret = false;
                                if (($aL = \strlen($a)) == ($bL = \strlen($b))) {
                                    $r = 0;
                                    for ($i = 0; $i < $aL; $i++) {
                                        $r |= (\ord($a[$i]) ^ \ord($b[$i]));
                                    }
                                    $ret = ($r === 0);
                                }
                                return $ret;
                            };
                            if((empty($pass) && !$password) || ($streq__fn)($pass, \md5($password ?? ''))){
                                $_SESSION['--AUTH'] = [
                                    'en' => true,
                                    'username' => $username,
                                    'name' => $user['name'] ?? $username ?? 'No Name',
                                    'roles' => $user['roles'] ?? [],
                                    'default_role' => $user['default_role'] ?? 'user',
                                ];
                                $_SESSION['--FLASH']['toasts'][] = 'You have Logged in Successfully';
                            } else {
                                $_SESSION['--FLASH']['toasts'][] =  'Invalid login credentials';
                            }
                        } else {
                            $_SESSION['--FLASH']['toasts'][] = 'Invalid login credentials';    
                        }
                    } else {
                        $_SESSION['--FLASH']['toasts'][] = 'Invalid login credentials';
                    } 
                } else {
                    $_SESSION['--FLASH']['toasts'][] = 'Invalid login action';
                }
                return ($this->redirect__ffn)(\strtok($_SERVER['REQUEST_URI'],'?'));
            } else if($view = $this->get_view('login')) {
                return $view;
            } else {
                return ($this->abort__ffn)(503, '503: Not Supported: Login interface is not implemented');
            }        
        }
    }

    public function route__permissions(){
        if($this->needs_permission()){
            
            $role = $this->role;
            $portal = $this->portal;
            $rurp = \_\REQ['rurp'];
            $portals = $this->portals;
            
            if(
                (($portals[0] ?? '') === '*')
                || \in_array($portal, $portals)
            ){
                //* all ok;
            } else {
                return ($this->abort__ffn)(403, '403: Not Allowed (A)');
            }
            
            if(($role_data['permits'][0] ?? null) == '*'){
                return;
            }
            
            foreach($role_data['permits'] ?? [] as $k => $v){
                if(\fnmatch($k, $rurp)){
                    return;
                }
            }
                
            foreach($role_data['exclusions'] ?? [] as $k => $v){
                if(\fnmatch($k, $rurp)){
                    return ($this->abort__ffn)(403, '403: Not Allowed (C)');
                }
            }

            if($_['is_supply'] ?? null){
                return ($this->abort__ffn)(403, '403: Not Allowed (D)');
            }
            
        }
    }
    
    public function route__option(){
        if($auth_option = ($_GET['--auth'] ?? null)){
            if($view = $this->get_view($auth_option)){
                return $view;
            } else {
                return ($this->abort__ffn)(503, "503: Not Available: '{$auth_option}'' interface is not supported");
            }
        }
    }
    
    public function get_portal_select_options(){
        $role = \_\REQ['role'];
        $portals = $this->portals;
        $select_options[''] = [
            'value' => '', 
            'label'=> 'Default',
            'is_selected' => !\_\REQ['portal'],
            'href' => \_\SITE_URL
        ];
        if(\in_array('*', $portals)){
            foreach(\_::glob('__*', GLOB_ONLYDIR) as $f){
                if($j = \substr(\basename($f),2)){
                    $k = '--'.($n = \ltrim($j,'-_'));
                    $name = \ucwords(\str_replace('_',' ',$n));
                    $select_options[$k] = [
                        'value' => $k, 
                        'label'=> $name,
                        'is_selected' => (\_\REQ['portal'] == $k),
                        'href' => \_\SITE_URL.($k 
                            ? "/{$k}".($role ? ".{$role}" : '') 
                            : "" //($role ? "/--.{$role}" : '') 
                        ),
                    ];
                }
            }
        } else {
            foreach($portals as $j){
                $k = '--'.($n = \ltrim($j,'-_'));
                $name = \ucwords(\str_replace(['-','_'],[' ',' '],$n));
                $select_options[$k] = [
                    'value' => $k, 
                    'label'=> $name,
                    'is_selected' => (\_\REQ['portal'] == $k),
                    'href' => \_\SITE_URL.($k 
                        ? "/{$k}".($role ? ".{$role}" : '') 
                        : "" //($role ? "/--.{$role}" : '') 
                    ),
                ];

            }
        }
        return $select_options;
    }
    
    public function collect_roles(){
        $roles = [];
        foreach(\_::glob(static::class.'/roles/*-$.php') as $f){
            $roles[\substr(\basename($f),0,-6)] = include $f;
        }
        foreach(\_::glob(\_\DATA_DIR.'/'.static::class.'/roles/*-$.php') as $f){
            $roles[\substr(\basename($f),0,-6)] = include $f;
        }
        return $roles;
    }
    
    public function get_role_select_options(){
        $facet_portal_url = \_\SITE_URL.'/'.\_\REQ['portal'];
        $roles = $this['roles'] ?? [];
        $select_options[''] = [
            'value' => '', 
            'label'=> 'Default',
            'is_selected' => !\_\REQ['role'],
            'href' => $facet_portal_url,
        ];
        if(\in_array('*', $roles)){
            foreach($this->collect_roles() as $k => $role){
                if($name = $role['name'] ?? false){
                    $select_options[$k] = [
                        'value' => $k, 
                        'label'=> $name,
                        'is_selected' => (\_\REQ['role'] == $k),
                        'href' => $facet_portal_url.($k ? ".{$k}" : '')
                    ];
                }
            }
        } else {
            foreach($roles as $k){
                if($role_data = $this->my_data("roles/{$k}")){
                    if($name = $role_data['name'] ?? false){
                        $select_options[$k] = [
                            'value' => $k, 
                            'label'=> $name,
                            'is_selected' => (\_\REQ['role'] == $k),
                            'href' => $facet_portal_url.($k ? ".{$k}" : '')
                        ];
                    }
                }
            }
        }
        return $select_options;        
    }
            
}
