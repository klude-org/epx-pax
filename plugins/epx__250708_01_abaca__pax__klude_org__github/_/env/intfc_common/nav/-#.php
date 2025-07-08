<?php namespace _;

class nav extends \stdClass {
       
    use \_\i\singleton__t;
    
    protected function __construct(){ 
        $this->start = new \DateTime(\date('Y-m-d H:i:s.'.\sprintf("%06d",(\_\MSTART-floor(\_\MSTART))*1000000), (int)\_\MSTART));
        $this->intfc = \_\INTFC;
        $this->intfx = (\_\INTFC == 'web') ? '' : \_\INTFC;
        $this->urp = \strtok($_SERVER['REQUEST_URI'] ?? '','?');
        $this->rurp = (function(){
            if('cli' == \_\INTFC){
                if(!\str_starts_with(($s = $_SERVER['argv'][1] ?? ''),'-')){
                    return '/'.\ltrim($s,'/');
                }
            } else {
                $p = \rtrim(\strtok($_SERVER['REQUEST_URI'],'?'),'/');
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
            }
        })() ?: '/';
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
        
        if(!\preg_match(
            "#^/"
                ."(?<full>"
                    ."(?:"
                        ."(?<facet>"
                            ."(?<portal>(?:__|--)[^/\.]*)"
                            ."(?:\.(?<role>[^/]*))?"
                        .")/?"
                    .")?"
                    ."(?<rpath>.*)"
                .")?"
            . "$#",
            $this->rurp,
            $m
        )){
            throw new \Exception("404: Not Found: Invalid request path format");
        }
        $this->parsed = \array_filter($m, fn($k) => !is_numeric($k), ARRAY_FILTER_USE_KEY);
        $this->panel = \trim(\str_replace('-','_', $this->parsed['portal'] ?? null ?: '__'),'/');
        $this->rpath = \trim($this->parsed['rpath'], '/');        
    }
    
    private function i__auto_init($type){
        foreach($_ENV['AUTO_INITS'][\_\INTFC][$type] ?? [] as $k => $v){
            if(\is_numeric($k)){
                o()->$v;
            } else {
                $v && o()->$k;
            }
        }
    }
    
    public function route(){
        $panel = $this->panel ?? "";
        $rpath = $this->rpath ?? "";
        $intfx = $this->intfx ?? "";
        $__CTLR_FILE__ = null;
        $this->i__auto_init('PRE_ROUTE');
        
        if(
            $rpath ? (
                ($f = \stream_resolve_include_path("{$panel}/{$rpath}/-@{$intfx}.php"))
                || ($f = \stream_resolve_include_path("{$panel}/{$rpath}-@{$intfx}.php"))
            ) : (
                ($f = \stream_resolve_include_path("{$panel}/-@{$intfx}.php"))
            )
        ){
            $__CTLR_FILE__ = \_\i\file::_($f);
            $__CONTEXT__ = function(){
                return $this;
            };
        } else if(
            \class_exists(\_\com::class)
            && \_\com::_()->route($this, $__CTLR_FILE__, $__CONTEXT__)
        ){
            //all set
        }
        
        if($__CTLR_FILE__ instanceof \SplFileInfo){
            o()->env;
            $__NAV__ = $this;
            return (function() use($__CTLR_FILE__, $__NAV__){
                $tsp = \explode(PATH_SEPARATOR,get_include_path());
                foreach($tsp as $d){
                    \is_file($f = "{$d}/.functions.php") AND include_once $f;
                }
                foreach(\array_reverse($tsp) as $d){
                    \is_file($f = "{$d}/.module.php") AND include_once $f;
                }
                if(\is_callable($o = (include $__CTLR_FILE__))){
                    $__NAV__->controller = $o;
                    ($o)($_REQUEST->ctrl_args ?? null);
                }
            })->bindTo($__CONTEXT__());
        }        
        
        return function(){
            \http_response_code(404);
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
            exit("404 Not Found: ".$_SERVER['REQUEST_URI']);
        };
    }
    
    
    public static function site_tree(){
    
    }

    public static function panel_tree(){
        $x = [];
        foreach(\_::glob('__*') as $panel){
            $r = (($b = \basename($panel)) == '__')
                ? ''
                : (\str_replace('_','-', $b).'/')
            ;
            $x = \array_merge_recursive($x, [ \rtrim(($r ?: '@'),'/') => \_\fs::tree_glob($panel, "{*-@.php,*-@.html}", function($name, $type) use($panel, $r){
                return match ($type){
                    'val' => \_\u(':'.$r.\_\p(\_\i\path::relative(\rtrim(\_\i\path::trim_extension($name),'@'), $panel))),
                    'key' => \rtrim(\_\i\path::trim_extension($name),'@') ?: '',
                    'dir' => $name,
                };
            })]);
        }
        return $x;
    }
    
    public static function controller_tree($panel_path = ''){
        return static::route_tree($panel_path, "{*-@.php,*-@.html}");
    }
    
    public static function route_tree($panel_path = '', $extension = null, \closure $mapper__fn = null){
        $extension = $extension ?: "{*-@.php,*-@.html}";
        $x = [];
        foreach(\_::glob(\rtrim("__/{$panel_path}",'/')) as $panel){
            $x = \array_merge_recursive($x, \_\fs::tree_glob($panel, $extension, function($name, $type) use($panel, $mapper__fn){
                return
                    ($mapper__fn) 
                    ? ($mapper__fn)($name, $type, $panel)
                    : match ($type){
                        'val' => \_\p(\_\i\path::relative(\rtrim(\_\i\path::trim_extension($name),'#@'), $panel)),
                        'key' => \_\i\path::trim_extension($name) ?: '',
                        'dir' => $name,
                    }
                ;
            }));
        }
        return $x;
    }
    
    
   
    
    public static function controller_tree__build_nav(&$nav = null, $panel_path = ''){
        if(\is_null($nav)){
            $nav = [];
        }
        $panel_path = $panel_path ? (\trim($panel_path,'/').'/') : '';
        return ($fn = function(&$nav, $g, $m = '') use(&$fn, $panel_path){
            foreach($g as $k => $v){
                $label = \trim($k, '#@') ?: '@';
                $code = \trim("{$m}/{$k}",'/#@');
                if(!$code){
                    // This will eliminate the Default controller of the root which is usually
                    // The Nav Portal UI
                } else if(\is_array($v)){

                    $h = [];
                    foreach($v as $k1 => $v1){
                        if(\str_contains($k1,'__i')) {
                            // This will eleminate internal controllers
                        } else {
                            $h[$k1] = $v1;
                        }
                    }
                    
                    if(\count($h) == 1 && (($x = \array_key_first($h)) == '@' || $x == '#@')){
                        $nav[$code] = (object) [
                            "icon" => "<i class=\"bi bi-arrow-right-short\"></i>",
                            "label" => $label, 
                            "en" => 1,
                            "code" => $code, 
                            "url" => $_REQUEST->base_url."/{$panel_path}{$code}",
                        ];
                    } else {
                        $inner = [];
                        $q = false;
                        if(isset($h['@'])){
                            $q = true;
                            unset($h['@']);
                        }
                        if(isset($h['#@'])){
                            $q = true;
                            unset($h['#@']);
                        }
                        ($fn)($inner, $h, $code);
                        $nav[$code] = (object) [
                            "icon" => "<i class=\"bi bi-arrow-right-short\"></i>",
                            "label" => "{$label}/", 
                            "en" => 1,
                            "code" => $code,
                            'inner' => $inner,
                            "url" => \_\FACET_URL."/{$panel_path}{$code}",
                            'is_accessible' => $q,
                        ];
                    }
                } else {
                    $nav[$code] = (object) [
                        "icon" => "<i class=\"bi bi-arrow-right-short\"></i>",
                        "label" => $label, 
                        "en" => 1,
                        "code" => $code, 
                        "url" => $_REQUEST->base_url."/{$panel_path}{$v}",
                    ];
                }
            }
            return $nav;
        })($nav, static::controller_tree($panel_path));
        
    }
    
    public static function controller_tree__nav($panel_path = ''){
        static::controller_tree__build_nav($nav,$panel_path);
        return $nav;
    }

    public static function control_list__attributed(){
        $list = [];
        if(\class_exists(\Attribute::class)){ //only for php 8.+
            foreach(($r = new \ReflectionObject(\_\controller()))->getMethods() as $m) {
                if($a = $m->getAttributes(\_\controller\control__a::class)[0] ?? null){
                    $list[$m->getName()] = $a->newInstance();
                }
            }
        }
        return $list;
    }
    
    public static function control_list(){
        $list = [];
        if(\class_exists(\Attribute::class)){ //only for php 8.+
            foreach(($r = new \ReflectionObject(\_\controller()))->getMethods() as $m) {
                $m_name = $m->getName();
                if($a = $m->getAttributes(\_\controller\control__a::class)[0] ?? null){
                    $list[$m_name] = $a->newInstance()->props();
                } else if(\str_starts_with($m_name,'c__')) {
                    $list[$n = \substr($m_name, 3)] = [
                        'label' => $n,
                    ];
                }
            }
        }
        return $list;
    }
    
    public static function control_tree(){
        $nav = [];
        $x = \_\linq(static::control_list())
            ->where(function($o){ return $o->en ?? true; })
            ->map(function($o,$k){ 
                return 
                    [ 'key' => $k, 'relp' => ":.{$k}" ] + $o
                ; 
            })
            ->order_by('category', SORT_ASC)
            ->select('key', 'label','category','relp','description')
            ->group_by('category')
            ->to_array()
        ;
        $uncat = $x[''] ?? [];
        unset($x['']);
        $x[''] = $uncat;
        $catindex = 0;
        foreach($x as $category => $controls){
            $nav[++$catindex]['desc'] = $category ?: '.';
            foreach($controls as $k => $v){
                $nav[$catindex]['urls'][] = [ 
                    'label' => $v['label'] ?: $k, 
                    'key' => $k,
                    'url' => \_\u($v['relp']), 
                    'desc' => $v['description'] ?? ''
                ];
            }
        }
        return $nav;
    }
        
}