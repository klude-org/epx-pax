<?php namespace epx__250706_01_ui__pax__klude_org__github\web;

class nav {

    use \_\i\instance__t;
    
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