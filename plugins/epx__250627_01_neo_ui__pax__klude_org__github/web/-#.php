<?php namespace epx__250627_01_neo_ui__pax__klude_org__github;

class web extends \epx__250627_01_neo_ui__pax__klude_org__github {
    
    use \_\i\singleton__t;
    
    protected function __construct(){
        parent::__construct();
        $this->urp = $_ENV['REQ']['urp'] ?? (function(){
            $p = \strtok($_SERVER['REQUEST_URI'],'?');
            if((\php_sapi_name() == 'cli-server')){
                return $p;
            } else {
                if((\str_starts_with($p, $n = $_SERVER['SCRIPT_NAME']))){
                    return \substr($p,\strlen($n));
                } else if((($d = \dirname($n = $_SERVER['SCRIPT_NAME'])) == DIRECTORY_SEPARATOR)){
                    return $p;
                } else {
                    return \substr($p,\strlen($d));
                }
            }
        })();
        if(!\preg_match(
            "#^/"
                ."(?<full>"
                    ."(?:"
                        ."(?<facet>"
                            ."(?<panel>(?:__|--)[^/\.]*)"
                            ."(?:\.(?<role>[^/]*))?"
                        .")/?"
                    .")?"
                    ."(?<rpath>.*)"
                .")?"
            . "$#",
            $this->urp,
            $m
        )){
            \http_response_code(404);
            echo "Invalid Route '{$_ENV['REQ']['urp']}'";
            exit(1);
        }
        $this->intfc = \_\INTFC;
        $this->intfx = $_REQUEST['--intfc'] ?? '';
        $this->parsed = \array_filter($m, fn($k) => !is_numeric($k), ARRAY_FILTER_USE_KEY);
        $this->panel = \trim(\str_replace('-','_', $this->parsed['panel'] ?? null ?: ''),'/');
        $this->scheme = ($_SERVER["REQUEST_SCHEME"] 
            ?? ((\strtolower(($_SERVER['HTTPS'] ?? 'off') ?: 'off') === 'off') ? 'http' : 'https'))
        ;
        $this->host = $_SERVER["HTTP_HOST"];
        $this->method = $method = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->headers = \iterator_to_array((function(){
            foreach(\getallheaders() as $k => $v){
                yield $k => $v;
            }
        })());
        $this->agent = $agent = (function(){
        if(!\is_null($agent = $this->headers['Epx-Agent'] ?? null)){
            return $agent;
        } else if('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' )) {
            return 'xhr';
        } else {
            return 'page';
        }
        })();
        $this->is_get = $is_get = !\in_array($method, ['POST','PUT','PATCH','DELETE']);
        $this->action = $action = (\in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','PATCH','DELETE']) 
            ? $_REQUEST['--action'] ?? true
            : $_GET['--action'] ?? false
        );
        $this->is_action = $is_action = ($action || !$is_get) ? true : false;
        $this->is_view = !$is_action;
        $this->referer = ($j = $_SERVER['HTTP_REFERER'] ?? null) ? \parse_url($j) : [];
        $this->is_supply = \preg_match('#(?:-pub|-@)[/\.]#', $this->urp) ? true : false;
        $this->is_html = (\str_contains(($_SERVER['HTTP_ACCEPT'] ?? ''),'text/html'));
        $this->is_xhr = ('xmlhttprequest' == \strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' ));
        $this->is_sub = (($_SERVER['HTTP_REFERER'] ?? '') ? true : false);
        $this->urp = \strtok($_SERVER['REQUEST_URI'] ?? '','?');
        $this->site_urp = (function(){
        if((\php_sapi_name() == 'cli-server')){
            return '';
        } else {
            $p = $this->urp;
            if((\str_starts_with($p, $n = $_SERVER['SCRIPT_NAME']))){
                return \substr($p, 0, \strlen($_SERVER['SCRIPT_NAME']));
            } else if((($d = \dirname($n = $_SERVER['SCRIPT_NAME'])) == DIRECTORY_SEPARATOR)){
                return '';
            } else {
                return \substr($p, 0, \strlen($d));
            }
        }
        })();
        $this->root_url = $this->scheme.'://'.$this->host;
        $this->full_url = \rtrim($this->root_url.$this->urp,'/');
        $this->site_url = \rtrim($this->root_url.$this->site_urp,'/');
        $this->data_url = $this->site_url."/data";
        $this->panel_url = \rtrim($this->site_url."/".($this->parsed['panel'] ?? null ?: ''), '/');
        $this->base_url = \rtrim($this->site_url."/"
            .(
                ($this->parsed['panel'] ?? null ?: '')
                .'.'.($this->parsed['role'] ?? null ?: '')
            )
            , 
            '/.'
        );
        //for now let us treat lib dir as on site;
        $this->lib_url = $this->site_url.'/--epx';
    }
    
    public function route(){
        $this->request;
        $this->session;
        $this->auth;
        $compile_inits__fn = function($path_list){
            global $_;
            $intfc = $this->intfc;
            $this->start = new \DateTime(\date('Y-m-d H:i:s.'.\sprintf("%06d",(\_\MSTART-floor(\_\MSTART))*1000000), (int)\_\MSTART));
            $this->inits = [];
            $is_routed = true;
            $tsp_list = \explode(PATH_SEPARATOR, \get_include_path());
            foreach(\array_reverse($tsp_list) as $d){
                if(\is_file($f = "{$d}/.module.php")){
                    $this->inits[] = \str_replace('\\','/', $f);
                }
            }
            foreach($tsp_list as $d){
                if(\is_file($f = "{$d}/.functions-{$intfc}.php")){
                    $this->inits[] = \str_replace('\\','/', $f);
                }
                if(\is_file($f = "{$d}/.functions.php")){
                    $this->inits[] = \str_replace('\\','/', $f);
                }
            }
            if(\is_file($f = \_\LIB_DIR.'/composer/vendor/autoload.php')){
                $this->inits[] = \str_replace('\\','/', $f);
            }
            foreach($path_list as $path => $offset){
                $list = [];
                if(\is_string($path)){
                    $g = (($path)
                        ? "{".\array_reduce(\explode('/', $path), function($c, $i){
                                static $u;
                                if($i){
                                    $u .= '/'.$i;
                                    $c .= ','.$u;
                                }
                                return $c;
                            })."}"
                        : ''
                    )
                    ."/-ini";
                    foreach($tsp_list as $d){
                        foreach(\glob("{$d}{$offset}{$g}{-{$intfc},}.php", GLOB_BRACE) as $f){ 
                            $list[$f] = \substr($f, \strlen($d));
                        }
                    }
                    \asort($list);
                }
                foreach($list as $f => $v){
                    $GLOBALS['_TRACE'][] = "found init {$f}";
                    $this->inits[] = $f;
                }
            }
        };
        
        \class_exists(($this->panel ?: '__')); //forces autoload
        foreach((function(){
            if($rpath = $this->parsed['rpath'] ?? null){
                $pi = \pathinfo($rpath);
                $this->ctlr_path = (\str_contains($rpath,'/') 
                    ? ($pi['dirname'].'/')
                    : ''
                ).\strtok($pi['basename'],'.');
                $this->ctrl_args = \strtok('');
                if($this->panel){
                    yield "{$this->panel}/{$this->ctlr_path}/-@{$this->intfx}.php";
                    yield "{$this->panel}/{$this->ctlr_path}-@{$this->intfx}.php";
                    yield "__/{$this->ctlr_path}/-@{$this->intfx}{$this->panel}.php";
                    yield "__/{$this->ctlr_path}-@{$this->intfx}{$this->panel}.php";
                } else {
                    yield "__/{$this->ctlr_path}/-@{$this->intfx}.php";
                    yield "__/{$this->ctlr_path}-@{$this->intfx}.php";
                }
            } else {
                $this->ctlr_path = '';
                if($this->panel){
                    yield "{$this->panel}/-@{$this->intfx}.php";
                    yield "__/-@{$this->intfx}{$this->panel}.php";
                } else {
                    yield "__/-@{$this->intfx}.php";
                }
            }
        })() as $v){
            $GLOBALS['_TRACE'][] = "Router resolving: {$v}";
            if($file = \stream_resolve_include_path($v)){
                $this->ctlr_file = \str_replace('\\','/',$file);
                $this->inits_path = \rtrim(($this->panel ?: '__').'/'.$this->ctlr_path, '/');
                $compile_inits__fn([$this->inits_path => '']);
                $route = $this;
                
                if(($_REQUEST['--trap'] ?? null) === 'pre-dispatch'){
                    throw new \Exception('--trap=pre-dispatch');
                }
                
                return (function() use($route){
                    (function(){
                        $tsp = \explode(PATH_SEPARATOR,get_include_path());
                        foreach($tsp as $d){
                            \is_file($f = "{$d}/.functions.php") AND include_once $f;
                        }
                        foreach(\array_reverse($tsp) as $d){
                            \is_file($f = "{$d}/.module.php") AND include_once $f;
                        }
                    })->bindTo(null,null)();
                    if(\is_callable($o = (include $route->ctlr_file))){
                        $route->controller = $o;
                        ($o)($route->ctrl_args);
                    }
                })->bindTo(\_::_(),\_::class);
            }
        }
        
        return function(){
            \http_response_code(404);
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
            exit("404 Not Found: ".$_SERVER['REQUEST_URI']);
        };
    }    
    
}