<?php


namespace _\path { if(!\function_exists(norm::class)){ function norm($expr){
    return \str_replace('\\','/', $expr); 
}}}
namespace _\path { if(!\function_exists(is_rooted::class)){ function is_rooted($path){
    return (($path[0]??'')=='/' || ($path[1]??'')==':');
}}}
namespace _\path { if(!\function_exists(abs::class)){ function abs($rel){
    $path = \_\slashes($rel);

    /* replace '//' or '/./' or '/foo/../' with '/' */
    $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
    for($n = 1; $n > 0; $path = preg_replace($re, '/', $path, -1, $n)) {}

    return \_\slashes($path);
}}}
namespace _\path { if(!\function_exists(relative::class)){ function relative($to, $from = \_\BASE_DIR){
    //credits: https://stackoverflow.com/a/51874346
    $separator = DIRECTORY_SEPARATOR;
    $from   = \str_replace(['/', '\\'], $separator, $from);
    $to     = \str_replace(['/', '\\'], $separator, $to);

    $arFrom = \explode($separator, \rtrim($from, $separator));
    $arTo = \explode($separator, \rtrim($to, $separator));
    while(\count($arFrom) && \count($arTo) && ($arFrom[0] == $arTo[0]))
    {
        \array_shift($arFrom);
        \array_shift($arTo);
    }

    return str_pad("", count($arFrom) * 3, '..'.$separator).implode($separator, $arTo);
}}}
namespace _\fs { if(!\function_exists(hglob::class)){ function hglob($expr, $d = \_\BASE_DIR, $glob_flags = 0, $max_depth = 10){
    $list = [];
    if(\is_dir($d)){
        for (
            $i=0, $dx=$d; 
            $dx && $i < $max_depth; 
            $i++, $dx = (\strchr($dx, DIRECTORY_SEPARATOR) != DIRECTORY_SEPARATOR) ? \dirname($dx) : null
        ){ 
            foreach(\glob(\rtrim($dx,'\\/')."/{$expr}", $glob_flags) as $dy){
                $list[] = \_\path\abs($dy);
            }
        }
    }
    return $list;
}}}
namespace _\fs { if(!\function_exists(contents::class)){ function contents($region,$path,...$contents){
    switch($region){
        case '':{
            if($contents){
                $file = \_\APP_DIR."/{$path}";
            } else {
                $file = \_\f($path);
            }
        } break;
        case 'base':{
            $file = \_\BASE_DIR."/{$path}";
        } break;
        case 'local':{
            $file = \_\BASE_DIR."/.local/{$path}";
        } break;
        case 'data':{
            $file = \_\DATA_DIR."/{$path}";
        }
    }
    if($contents){
        \is_dir($d = \dirname($file)) or \mkdir($d,0777,true);
        \file_put_contents($file, $contents[0]);
        return \_\f($file);
    } else {
        if(\is_file($file)){
            return \file_get_contents($file);
        }
    }
}}}
namespace _ { if(!\function_exists(is_empty::class)){ function is_empty($obj, array $exclude = []){
    if(\is_object($obj)){
        if(!$exclude){
            foreach( $obj as $x ) return false;
        } else {
            foreach ($obj as $key => &$value) {
                if(!\in_array($key, $exclude)){
                    return false;
                }
            }
        }
    } else {
        return empty($obj);
    }
    return true;
}}}
namespace _ { if(!\function_exists(owner_of::class)){ function owner_of(object $resource, object|bool $owner = null){
    static $c = []; 
    if(\is_null($owner)){
        return $c[\spl_object_id($resource)] ?? null;
    } else if(\is_bool($owner) && $owner == false){
        unset($c[\spl_object_id($resource)]);
    } else {
        return $c[\spl_object_id($resource)] = $owner;
    }
}}}
namespace _ { if(!\function_exists(render::class)){ function render($expr,...$args){
    if(!$expr){
        //* do nothing
    } else if(is_array($expr)){
        foreach($expr as $v){
            \_\render($v,...$args);
        }
    } else if(is_scalar($expr)){
        echo $expr;
    } else if($expr instanceof \SplFileInfo) {
        include $expr;
    } else {
        if($expr instanceof \closure) {
            ($expr)(...$args);
        } else if(interface_exists(\_\i\printable__i::class) && $expr instanceof \_\i\printable__i){
            $expr->print($args);
        } else {
            \_\prt($expr);
        }
    }
}}}
namespace _ { if(!\function_exists(texate::class)){ function texate($expr,...$args){
    if(!$expr){
        return '';
    } else if(is_array($expr)){
        $x = '';
        foreach($expr as $v){
            $x .= \_\texate($v,...$args);
        }
        return $x;
    } else if(is_scalar($expr)){
        return $expr;
    } else {
        try{
            ob_start();
            if($expr instanceof \closure) {
                ($expr)(...$args);
            } else if($expr instanceof \SplFileInfo) {
                include $expr;
            } else if($expr instanceof \_\i\prt__i){
                $expr->print($args);
            } else {
                \_\prt($expr);
            }
        } finally {
            $d = ob_get_contents();
            ob_end_clean();
        }
        //* if returned in finally exceptions get lost
        //* so put it outside
        return $d;
    }
}}}
namespace _ { if(!\function_exists(session::class)){ function session(){
    static $I; return $I ?? ($I = \_\session::_());
}}}
namespace _ { if(!\function_exists(session_var::class)){ function session_var($key,...$args){
    if(!$args){
        return $_SESSION[\_\KEY]['--var'][$key] ?? '';
    } else {
        $_SESSION[\_\KEY]['--var'][$key] = $args[0];
    }
}}}
namespace _ { if(!\function_exists(flash::class)){ function flash($key,...$args){
    if(!$args){
        return $GLOBALS['_']['FLASH'][$key];
    } else {
        $_SESSION[\_\KEY]['--flash'][$key] = $args[0];
    }
}}}

namespace _ { if(!\function_exists(prt::class)){ function prt($o){
    return \_\dump($o);
}}}
namespace _ { if(!\function_exists(print_ln::class)){ function print_ln($o){
    echo '<pre>';
    if(is_scalar($o)){
        echo $o;
    } else {
        echo \json_encode($o, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    echo '</pre>';
}}}
namespace _ { if(!\function_exists(runspan::class)){ function runspan(int $decimal = 6, $unit = 's'){
    return \number_format(((\microtime(true) - \_\MSTART)), $decimal).$unit;
}}}
namespace _ { if(!\function_exists(typename::class)){ function typename($path){
    if(!$path){
        throw new \Exception("Invalid parameter: \$path cannot be empty");
    } else if(\is_string($path)){
        return \str_replace('/','\\', $path);
    } else if(is_object($path)){
        return get_class($path);
    } else {
        throw new \Exception("Invalid parameter: \$path must be a string or an object");
    }
}}}
namespace _ { if(!\function_exists(typepath::class)){ function typepath($path){
    if(!$path){
        throw new \Exception("Invalid parameter: \$path cannot be empty");
    } else if(\is_string($path)){
        return \str_replace('\\','/', $path);
    } else if(is_object($path)){
        return \str_replace('\\','/', get_class($path));
    } else {
        throw new \Exception("Invalid parameter: \$path must be a string or an object");
    }
}}}
namespace _ { if(!\function_exists(slashes::class)){ function slashes($path){ 
    return \str_replace('\\','/',$path); 
}}}
namespace _ { if(!\function_exists(backslashes::class)){ function backslashes($path){ 
    return \str_replace('/','\\', $path); 
}}}
namespace _ { if(!\function_exists(namespace_of::class)){ function namespace_of($class, $levels = 1){
    static $c = []; return $c[$class] ?? ($c[$class] = \_\backslashes(\dirname(\_\slashes($class), $levels)));
}}}
namespace _ { $GLOBALS['_']['TSP']['ALT'][html::class] = function(){ class html {
    public static function el($type, $attribs = [], ...$more){
        $a1 = [];
        $attr = '';
        $inner_html = '';
        $prop = [];
        if(is_scalar($attribs)){
            $inner_html = $attribs;
        } else if($attribs instanceof \closure) {
            $inner_html .= \_\texate($attribs);
        } else {
            foreach((array) $attribs as $k => $v){
                if(is_scalar($v)){
                    if(is_numeric($k)){
                        if($k === 0){
                            $inner_html .= $v;
                        } else {
                            foreach(explode(' ',$v) as $r){
                                $prop[$r] = true;
                            }
                        }
                    } else {
                        $a1[$k] = $v;
                    }
                } else if($v instanceof \closure) {
                    $inner_html .= \_\texate($v);
                }
            }
        }
    
        foreach($more as $ax){
            $a2 = (array) $ax;
            if(is_scalar($a2)){
                $inner_html .= $a2;
            } else if($a2 instanceof \closure) {
                $inner_html .= \_\texate($a2);
            } else {
                foreach((array) $a2 as $k => $v){
                    if(is_scalar($v)){
                        if(is_numeric($k)){
                            foreach(explode(' ',$v) as $r){
                                $prop[$r] = true;
                            }
                        } else if($v instanceof \closure) {
                            $inner_html .= \_\texate($v);
                        } else {
                            switch($k){
                                case 'class':{
                                    $a1[$k] = (empty($a1[$k])) ? $v : ($a1[$k].' '.$a2[$k]);
                                } break;
                                case 'style':{
                                    $a1[$k] = (empty($a1[$k])) ? $v : ($a1[$k].'; '.$a2[$k]);
                                } break;
                                default:{
                                    //* overwrite
                                    $a1[$k] = $v;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        foreach($a1 as $k => $v){
            $attr .= " {$k}=\"{$v}\"";
        }
        
        if($prop){
            $attr .= ' '.implode(' ', array_keys($prop));
        }
    
        if($GLOBALS['_']['XHTML__EN'] ?? false){
            try{
                $type = rtrim($type,'/');
                echo "<{$type}{$attr}", ($inner_html) ? ">\n" : " />\n";
                \_\render($inner_html);
            } finally {
                echo ($inner_html) ? "\n</{$type}>" : "";
            }
        } else { 
            if($type[-1] == '/'){
                $type = rtrim($type,'/');
                echo "<{$type}{$attr}>";
            } else {
                try{
                    echo "<{$type}{$attr}>";
                    \_\render($inner_html);
                } finally {
                    echo "</{$type}>";
                }
            }
        }
    }
    
    public static function input(array $args, ...$more){
        \_\html::el('input', $args,...$more);
    }
    public static function select(array $args, array ...$more){
        $args[0] = (\is_string($v = $args[0] ?? ''))
            ? $v
            : \_\texate(function($options, $value){ foreach($options ?? [] as $k => $v): ?>
                <?php if(\is_array($v)): ?>
                    <option value="<?=$k?>"<?=$value == $k ? ' selected':''?>><?=$v[0] ?? ''?></option>
                <?php else: ?>
                    <option value="<?=$k?>"<?=$value == $k ? ' selected':''?>><?=$v?></option>
                <?php endif ?>
            <?php endforeach; },$v, $args['value'] ?? null)
        ;
        \_\html::el('select', $args,...$more);
    }
}}; }
namespace _\ui\theme { $GLOBALS['_']['TSP']['ALT'][ff::class] = function(){ class ff {
    
    private static function __($type = 'text', array $options = []){
        $name = "";
        $label = "UNLABELED";
        $value = null;
        $uid = \uniqid('ff-');
        \extract($options);
        ?>
        <div class="row">
            <div class="col form-group mb-3">
                <label class="form-check-label" for="<?=$uid?>"><?=$label?></label>
                <input type="<?=$type?>" class="xui-field form-control" name="ff[<?=$name?>]" id="<?=$uid?>" value="<?=htmlspecialchars($value)?>">
            </div>
        </div>
        <?php 
    }
    
    public static function blank(){ ?><div class="col"></div><?php }
    public static function hidden($name, $value = null){?><input name="ff[<?=$name?>]" <?php if(!is_null($value)){?>value="<?=htmlspecialchars($value)?>"<?php }?> hidden><?php }
    public static function text(...$args){ return static::__("text", ...$args); }
    public static function daterange(...$args){ throw new NotImplementedException(); }
    public static function duration(...$args){ throw new NotImplementedException(); }
    public static function date(...$args){ return static::__("date",  ...$args); }
    public static function datetime_local(...$args){ return static::__("datetime-local",  ...$args); }
    public static function email(...$args){ return static::__("email",  ...$args); }
    public static function number(...$args){ return static::__("number", ...$args); }
    public static function time(...$args){ return static::__("time", ...$args); }
    public static function color(...$args){ return static::__("color", ...$args); }
    public static function image(...$args){ return static::__("image", ...$args); }
    public static function file(...$args){ return static::__("file", ...$args); }
    public static function month(...$args){ return static::__("month", ...$args); }
    public static function password(...$args){ return static::__("password", ...$args); }
    public static function range(...$args){ return static::__("range",...$args); }
    public static function reset(...$args){ return static::__("reset",...$args); }
    public static function search(...$args){ return static::__("search",...$args); }
    public static function submit(...$args){ return static::__("submit",...$args); }
    public static function tel(...$args){ return static::__("tel",...$args); }
    public static function url(...$args){ return static::__("url",...$args); }
    public static function week(...$args){ return static::__("week",...$args); }
    
    public static function checkbox(array $options = []){ 
        $name = "";
        $label = "UNLABELED";
        $value = null;
        $uid = \uniqid('ff-');
        \extract($options);
        ?>
        <div class="row">
            <div class="col form-group mb-3">
                <input type="checkbox" name="ff<?=$name?>" value="0" hidden="" checked="">
                <input type="checkbox" class="xui-field form-check-input" value="1" name="ff<?=$name?>" id="<?=$uid?>" <?=($value) ? 'checked' : ''?>>
                <label class="form-check-label" for="<?=$uid?>"><?=$label?></label>
            </div>
        </div>
        <?php 
    }
    
    public static function radiobtn(array $options = []){ 
        $name = "";
        $label = "UNLABELED";
        $value = null;
        $checked = null;
        $uid = \uniqid('ff-');
        \extract($options)
        ?>
        <div class="row">
            <div class="col form-group mb-3">
                <input type="radio" class="xui-field form-check-input" value="<?=$value?>" name="ff<?=$name?>" id="<?=$uid?>" <?=($checked) ? 'checked' : ''?>>
                <label class="form-check-label" for="<?=$uid?>"><?=$label?></label>
            </div>
        </div>
        <?php 
    }
    
    public static function data_uri_of($file, $mime)  {
        if($file && \is_file($file)){
            $contents = file_get_contents($file);
            $base64   = base64_encode($contents);
            return ('data:' . $mime . ';base64,' . $base64);
        }
    }
    
    public static function page($__inset__){ ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Setup</title>
            <link rel="icon" href="<?=static::data_uri_of(\_\f('favicon.png'),'image/png'); ?>">
            <script>xui = {};</script>
            
            <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
            <script src="https://code.jquery.com/jquery-3.2.1.js" crossorigin="anonymous"></script>
        
            <!-- jQuery UI -->
            <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
        
            <!-- Latest compiled and minified CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <!-- Latest compiled and minified JavaScript -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
            
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
            
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.js"></script>
        
            <!-- include summernote css/js -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" integrity="sha512-ngQ4IGzHQ3s/Hh8kMyG4FC74wzitukRMIcTOoKT3EyzFZCILOPF0twiXOQn75eDINUfKBYmzYn2AA8DkAk8veQ==" crossorigin="anonymous" reffrrerpolicy="no-reffrrer" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js" integrity="sha512-6F1RVfnxCprKJmfulcxxym1Dar5FsT/V2jiEUvABiaEiFWoQ8yHvqRM/Slf0qJKiwin6IDQucjXuolCfCKnaJQ==" crossorigin="anonymous" reffrrerpolicy="no-reffrrer"></script>
            
            <style>
                .xui-sortable-handle {
                    cursor: grab;
                }
            </style>
        </head>
        <body>
            <?php ($__inset__)(); ?>
        </body>
        <script>
            xui.sortable = {
                init(){
                    var panelList = $('.xui-sortable-pool');
                    panelList.sortable({
                        // Only make the .panel-heading child elements support dragging.
                        // Omit this to make then entire <li>...</li> draggable.
                        handle: '.xui-sortable-handle',
                        update: function () {
                            $('.xui-sortable-body', panelList).each(function (index, elem) {
                                //* code for update
                            });
                        }
                    });
                }
            };
            
            $(document).ready(function () {
                xui.sortable.init();
            });
        </script>
        </html>
    <?php }    
    
}}; }

namespace { return new class extends \stdClass {
    
    private $SESSION;
    private $HEADING = 'Setup';
    private $DB;
    private $SETTINGS;
    private $AUTH_BYPASS = ['auth' => true];
    private $REMOTE_REPO_URL;
    
    public function __construct(){
        \_\c($this);
        if(!($GLOBALS['_']['FW_SETUP__EN'] ?? true)){
            \http_response_code(404); exit('404: Not Found');
        }
        $this->SESSION =& $_SESSION[\_\KEY];
        $this->SETTINGS = $this->env_setup__json();
        $this->REMOTE_REPO_URL = $this->SETTINGS['REMOTE_REPO']['URL'] ?? "https://epx-modules.neocloud.com.au";
    }
  
    
    public function __invoke($control,...$args){
        $this->auth(($this->AUTH_BYPASS[$control] ?? false));
        if(method_exists($this, $m = "c__{$control}")){
            $this->$m(...$args);
        } else {
            throw new \Exception("Method not found ".static::class."::{$m}");
        }
    }
    
    private function url(string $sub = null, array $query = null){
        $url = \rtrim(\_\CTR_URL,'/').
            (($sub && $sub !== '.')
                ? (($sub[0] == '.') ? $sub : "/{$sub}")
                : ""
            )
        ;
        if($url){
            if($query){
                $url .= "?".\http_build_query($query);
            }
        }
        return $url;
    }  
    
    private function download($file, array $options = []){
        $headers = null;
        $download_name = false;
        \extract($options);
        if(!file_exists($file)){
            \http_response_code(404); exit('{ "status":"error", "info":"Not Found" }');
        } else {
            if(!$headers){
                if($download_name === false){
                    $download_name = \basename($file);
                } else if($download_name === true){
                    $fname = pathinfo($file, PATHINFO_FILENAME);
                    $download_name = \str_replace('/','-','download-'.date('Y-md-Hi-s')."-{$fname}");
                } else if(\is_string($download_name)){
                    
                }
                $headers = [
                    "Content-Type: application/octet-stream",
                    "Content-Transfer-Encoding: Binary", 
                    "Content-disposition: attachment; filename=\"".$download_name."\"",
                    "Content-length:".(string)(filesize($file)),
                ];
            }
            try {
                foreach($headers as $h){
                    header($h);
                }
                readfile($file);
            } finally {
                exit();
            }
        }
    }
    
    private function dump($o = null, $depth = 20){
        try {
            static $entry_obj;
            static $hashes = [];
            static $called;
            static $level = 0;
            $level++;
            
            if($level == 1){
                $hashes = [];
            }
            
            if($level > $depth){ return; }
            
            if(!$called){ $called = 1; ?>
            <style>
                table.fw-dump td.fit,
                table.fw-dump th.fit {
                    padding:1px;
                    white-space: nowrap;
                    width: 1%;
                }
                table.fw-dump.top{
                    border:1px solid #ccc;
                }
                table.fw-dump {
                    border-collapse: collapse;
                    margin:0;
                    padding:0;
                    border: 0px;
                    width:100%;
                }
                table.fw-dump td{
                    border-collapse: collapse;
                    margin:0;
                    padding:0;
                    border-left:1px solid #ccc;
                    border-top:1px solid #ccc;
                    border-right:0px;
                    border-bottom:0px;
                }
                table.fw-dump td,
                table.fw-dump td * {
                    vertical-align: top;
                }
                table.fw-dump td.val{
                    padding:1px;
                }
                table.fw-dump .t{
                    font-size: 0.7em;
                    user-select: none;
                }
                table.fw-dump .t-null{
                    color:blue;
                }
                table.fw-dump .t-obj{
                    color:blue;
                }
                table.fw-dump .t-str{
                    color:grey;
                }
                table.fw-dump .t-scl{
                    color:grey;
                }
                table.fw-dump .t-arr{
                    color:maroon;
                }
            </style>
            <?php } ?>
            
            <table class="fw-dump <?= $level==1 ?'top':''?>">
                <?php if($o === null): ?>
                <tr>
                    <td class="fit"><b><code><i class="t t-null">NULL</i><code></b>
                </tr>
                <?php elseif(is_scalar($o)): $v = $o; ?>
                <tr>
                    <td class="fit"><code><?php if(is_string($v)):?><i class="t t-str">string[<?=strlen($v)?>]</i><?php else:?><i class="t t-scl"><?=gettype($v)?></i><?php endif?></code></td>
                    <td class="val"><code><?php if(is_string($v)):?><?=highlight_string($v,true)?><?php else:?><?=$v?><?php endif?></code></td>
                </tr>
                <?php else: 
                if(is_object($o)){
                    if($level === 1){
                        if(!($hashes[$i = spl_object_id($o)] ?? false)){
                            $hashes[$i] = true;
                            $entry_obj = $i;
                        }
                    }
                    $od = method_exists($o,'dump__i') ? ((object) $o->dump__i()) : $o; ?>
                    <tr>
                        <td class="fit" colspan="2"><b><code class="t t-obj"><?=get_class($o)?> OBJECT(<?=spl_object_id($o)?>)</code></b></td>
                    </tr>
                    <?php
                } else if(is_array($o)) {
                    ?>
                    <tr>
                        <td class="fit" colspan="2"><b><code class="t t-arr">[<?=count($o)?>]</code></b></td>
                    </tr>
                    <?php
                    $od = $o;
                } else {
                    $od = $o;
                }
                foreach($od as $k => $v): 
                    if($v === null || is_scalar($v)): ?>
                    <tr>
                        <td class="fit"><b><code><?=$k?></code></b><code><?php if($v === null): ?><i class="t t-null">NULL</i><?php elseif(is_string($v)):?><i class="t t-str">string[<?=strlen($v)?>]</i><?php else:?><i class="t t-scl"><?=gettype($v)?></i><?php endif?></code></td>
                        <td class="val"><code><?php if($v === null): ?><i style="color:blue">NULL</i><?php elseif(is_bool($v)): ?><i style="color:green"><?=$v ? 'true' : 'false'?></i><?php elseif(is_string($v)):?><?=highlight_string($v,true)?><?php else:?><?=$v?><?php endif?></code></td>
                    </tr>
                    <?php elseif(is_object($v)): ?>
                    <tr>
                        <?php if($hashes[$i = spl_object_id($v)] ?? false): ?>
                            <td class="fit"><b><code><?=$k?></code></b></td>
                            <td><code style="color:blue">OBJECT(<?=($i == $entry_obj) ? '.' : $i?>)</code></td>
                        <?php else: $hashes[$i] = true; ?>
                            <td class="fit"><b><code><?=$k?></code></b></td>
                            <td><?php \_\prt($v, $depth); ?></td>
                        <?php endif ?>
                    </tr>
                    <?php else : ?>
                    <tr>
                        <td class="fit"><b><code><?=$k?></code></b></td>
                        <td><?php \_\prt($v, $depth) ?></td>
                    </tr>
                <?php endif; endforeach; endif; ?>
            </table>
        <?php 
        } finally {
            $level--;
        }
    }
    
    private function lsp__resolve($expr){
        return $this->p(\realpath($expr));
    }

    private function tsp__resolve($expr){
        return $this->p(\stream_resolve_include_path($expr));
    }
    
    private static function p(string $expr){
        return \str_replace('\\','/', $expr); 
    }
    
    private static function path_is_rooted($path){
        return(($path[0]??'')=='/' || ($path[1]??'')==':');
    }
    
    protected function data_dir__eval(&$var){
        $this->module_dir__eval($var,'data');
    }
    
    protected function app_dir__eval(&$var){
        $this->module_dir__eval($var,'app');
    }
    
    protected function core_dir__eval(&$var){
        $this->module_dir__eval($var,'core');
    }

    protected function theme_dir__eval(&$var){
        $this->module_dir__eval($var,'theme');
    }

    protected function module_dir__eval(&$var, $type = 'module'){
        $var = (\pathinfo($var, PATHINFO_EXTENSION) == $type) ? $this->lsp__resolve($var) : null;
    }

    protected function password__eval(&$var, $default){
        if(!empty($var)){
            $var = md5($var);
        } else {
            $var = $default;
        }
    }
    
    private function env_base__json(&$file = null){
        if(\file_exists($file = \dirname($_SERVER['SCRIPT_FILENAME'])."/.env-base-json")){
            $ff = \json_decode(\file_get_contents($file), true);
        } else {
            $ff = [];
        }
        return $ff;
    }
    
    private function env_base__submit(\closure $post){
        $ff = $this->env_base__json($file);
        if(($post)($ff) !== false){
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            \file_put_contents($file, json_encode($ff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
    
    private function env_mode__json(&$file = null){
        if(\file_exists($file = \_\DATA_DIR."/.env-mode-json")){
            $ff = \json_decode(\file_get_contents($file), true);
        } else {
            $ff = [];
        }
        return $ff;
    }
    
    private function env_mode__submit(\closure $post){
        $ff = $this->env_mode__json($file);
        if(($post)($ff) !== false){
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            \file_put_contents($file, json_encode($ff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
        
    private function env_current_mode__json(&$file = null){
        if(\file_exists($file = \_\DATA_DIR."/.env-mode-json")){
            $mode = $this->env_base__json()['FW_MODE'] ?? '';
            $ff = (\json_decode(\file_get_contents($file), true) ?: [])[$mode] ?? [];
        } else {
            $ff = [];
        }
        return $ff;
    }
    
    private function env_current_mode__submit(\closure $post){
        $ff = $this->env_current_mode__json($file);
        if(($post)($ff) !== false){
            $mode = $this->env_base__json()['FW_MODE'] ?? '';
            if(\is_file($file)){
                $data = \json_decode(\file_get_contents($file), true) ?? [];
            } else {
                $data = [];
            }
            $data[$mode] = $ff;
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            \file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    private function env_data__json(&$file = null){
        if(\file_exists($file = \_\DATA_DIR."/.env-data-json")){
            $ff = \json_decode(\file_get_contents($file), true);
        } else {
            $ff = [];
        }
        return $ff;
    }
    
    private function env_data__submit(\closure $post){
        $ff = $this->env_data__json($file);
        if(($post)($ff) !== false){
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            \file_put_contents($file, json_encode($ff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
    
    private function env_data__remove(){
        if(\file_exists($file = \_\DATA_DIR."/.env-data-json")){
            \unlink($file);
        }
    }

    private function env_setup__json(&$file = null){
        if(\file_exists($file = \_\DATA_DIR."/.env-setup-json")){
            $ff = \json_decode(\file_get_contents($file), true);
        } else {
            $ff = [];
        }
        return $ff;
    }
    
    private function env_setup__submit(\closure $post){
        $ff = $this->env_setup__json($file);
        if(($post)($ff) !== false){
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            \file_put_contents($file, json_encode($ff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
    
    private static function ui__tabitem($link, $title, $opt){
        if($link == \_\REQ_URL){
            $active_class = 'active';
            $active_aria = 'aria-current="page"';
        } else {
            $active_class = '';
            $active_aria = '';
        }
    ?>
    <li class="nav-item">
        <a class="nav-link <?=$active_class?>" <?=$active_aria?> href="<?=$link?>">
            <?=$opt['icon'] ?? ''?><?=$title?>
        </a>
    </li>
    <?php }

    private function ui__tabpanel__prt($__inset__){ 
        $mode = ($m = $GLOBALS['_']['FW_MODE'] ?? '')
            ? "| mode:{$m}"
            : ""
        ;
        ?>
        <div class="container-fluid pt-1">
            <div class="d-flex flex-column">
                <div class="row px-0">
                    <div class="col"><div class="h4 d-inline-block font-monospace" title="<?=\_\BASE_URL?>"><?=\basename(\_\BASE_URL)?> | Setup <?=$mode?> </div></div>
                    <div class="col">
                        <div class="float-end">
                            <a class="btn btn-outline-danger" href="<?=$this->url('.auth.logout')?>">Logout</a>
                        </div>
                    </div>
                </div>
                <style>
                    .setup-tabs.nav{
                        flex-wrap:nowrap;
                        overflow-x: auto;
                        overflow-y: hidden;
                        font-size: 0.9em;
                    }
                </style>
                <div class="row">
                    <ul class="setup-tabs nav nav-tabs mb-1">
                        <?php 
                            foreach($this->tabs() as $k => $v){
                                $this->ui__tabitem($this->url(".{$k}"), $v ?? 'N', ['icon' => '<span data-feather="file"></span>']);
                            }
                        ?>
                    </ul>
                </div>
                <div class="row">
                    <div class="container-fluid px-0">
                        <?php ($__inset__)(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    
    private function ui__tabpage__plain__prt($__inset__){
        \_\ui\theme\ff::page(function() use($__inset__){
            $this->ui__tabpanel__prt($__inset__);
        });
    }
    
    private function ui__tabpage__form_bravo___prt(\closure $__inset__){ 
        \_\ui\theme\ff::page(function() use($__inset__){ 
            $this->ui__tabpanel__prt(function() use($__inset__) {?>
                <form action="" method="POST">
                    <input type="hidden" name="--csrf" value="<?=\_\CSRF?>">
                    <div class="container-fluid sticky-top pt-1 px-3" style="z-index:1">
                        <div class="row">
                            <div class="col">
                                <div class="float-end">
                                    <input class="btn btn-outline-primary" type="submit" name="--action" value="Save">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid pt-3">
                        <?php ($__inset__)(); ?>
                    </div>
                </form>
            <?php }); 
        }); 
    }
    
    private function auth($is_auth_page = false){
        if(!$is_auth_page){
            if(($this->SESSION['user'] ?? false)){
                return true;
            } else {
                \header("Location: ".$this->url('.auth')); exit();
            }
        }
    }
    
    private function logout(){
        unset($this->SESSION['user']);
        \header("Location: ".$this->url('.auth')); exit();
    }
    
    private function c__auth($state = null){
        if(\_\IS_ACTION){
            if(empty($GLOBALS['_']['FW_USERS'])){
                $GLOBALS['_']['FW_USERS'] = [
                    'admin' => [
                        'password' => '`pass',
                    ]
                ];
            }
            if($_POST['username'] ?? false){
                if(($user = $GLOBALS['_']['FW_USERS'][$_POST['username'] ?? '']??[])){
                    $password = (($p = ($user['password'] ?? null) ?: "`")[0] == "`") 
                        ? \md5(\substr($p, 1))
                        : $p
                    ;
                    
                    $compare__fn = function ($a, $b) {
                        //credits: https://blog.ircmaxell.com/2014/11/its-all-about-time.html
                        $ret = false;
                        if (($aL = strlen($a)) == ($bL = strlen($b))) {
                            $r = 0;
                            for ($i = 0; $i < $aL; $i++) {
                                $r |= (ord($a[$i]) ^ ord($b[$i]));
                            }
                            $ret = ($r === 0);
                        }
                        return $ret;
                    };
                    
                    if(empty($password) || $compare__fn($password, md5($_POST['password'] ?? ''))){
                        $this->SESSION['user'] = [
                            'username' => $_POST['username'],
                            'name' => $user['name'] ?? $_POST['username'] ?? 'No Name',
                        ];
                        \header("Location: ".$this->url()); exit();
                    }
                    \header("Location: ".$this->url('.auth')); exit();
                }
            }
            \header("Location: {$_SERVER['REQUEST_URI']}"); exit();
        } else if($state == 'logout') {
            unset($this->SESSION['user']);
            \header("Location: ".$this->url('.auth')); exit();
        } else { \_\ui\theme\ff::page(function() { 
            ?>
            <style>
                html,
                body {
                    height: 100%;
                }
    
                .page {
                    height: 100%;
                    display: flex;
                    align-items: center;
                    padding-top: 40px;
                    padding-bottom: 40px;
                    background-color: #f5f5f5;
                }
    
                .form-signin {
                    max-width: 330px;
                    padding: 15px;
                }
    
                .form-signin .form-floating:focus-within {
                    z-index: 2;
                }
    
                .form-signin input[type="email"] {
                    margin-bottom: -1px;
                    border-bottom-right-radius: 0;
                    border-bottom-left-radius: 0;
                }
    
                .form-signin input[type="password"] {
                    margin-bottom: 10px;
                    border-top-left-radius: 0;
                    border-top-right-radius: 0;
                }
            </style>
            
            <div class="page text-center">
                <main class="form-signin w-100 m-auto">
                    <form action="" method="POST">
                        <input type="hidden" name="--auth" value="login">
                        <input type="hidden" name="--csrf" value="<?=\_\CSRF?>">
                        <img class="mb-4" src="<?=\_\ui\theme\ff::data_uri_of(\_\f('favicon.png'),'image/png'); ?>" alt="" width="72">
                        <h1 class="h3 mb-3 fw-normal">Please Sign In</h1>
    
                        <div class="form-floating">
                            <input type="text" class="form-control" name="username" id="id-username" placeholder="name@example.com">
                            <label for="id-username">Username</label>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" name="password" id="id-password" placeholder="Password">
                            <label for="id-password">Password</label>
                        </div>
    
                        <div class="checkbox mb-3">
                            <label>
                                <input type="checkbox" value="remember-me"> Remember Me
                            </label>
                        </div>
                        <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
                        <p class="mt-5 mb-3 text-muted"><a href="https://klude.com.au">klude.com.au</a> &copy; 2017–2023</p>
                    </form>
                </main>
            </div>
        <?php 
        }); }
    }

    private function c__(){
        \_\x('.data');
    }
    
    private function c__admin(){
        if(\_\IS_ACTION){
            switch($_POST['--action'] ?? false){
                case "Save":{
                    $this->env_base__submit(function(&$ff){
                        if(
                            !empty($_POST['ff']['FW_USERS']['admin']['password_change'])
                            && ($_POST['ff']['FW_USERS']['admin']['password_change'] == $_POST['ff']['FW_USERS']['admin']['password_confirm'])
                        ){
                            $_POST['ff']['FW_USERS']['admin']['password'] = \md5($_POST['ff']['FW_USERS']['admin']['password_change']);
                        }
                        
                        unset(
                            $_POST['ff']['FW_USERS']['admin']['password_change'],
                            $_POST['ff']['FW_USERS']['admin']['password_confirm']
                        );
                        
                        $ff = array_replace_recursive($ff, $_POST['ff']);
                    });
                } break;
            }
            \header("Location: {$_SERVER['REQUEST_URI']}"); exit();
        } else {
            $this->ui__tabpage__form_bravo___prt(function() {
                $ff = $this->env_base__json();
                ?>
                <div class="container-fluid">
                    <div class="h5">Admin User</div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-3">
                                <label for="<?=$uid=uniqid()?>" class="form-label">Name</label>
                                <input type="text" class="form-control" id="<?=$uid?>" name="ff[FW_USERS][admin][name]" value="<?=$ff['FW_USERS']['admin']['name'] ?? ''?>" placeholder="...">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-3">
                                <label for="<?=$uid=uniqid()?>" class="form-label">Password</label>
                                <input type="password" class="form-control" id="<?=$uid?>" name="ff[FW_USERS][admin][password_change]" placeholder="..." autocomplete="one-time-code">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group mb-3">
                                <label for="<?=$uid=uniqid()?>" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="<?=$uid?>" name="ff[FW_USERS][admin][password_confirm]" placeholder="..." autocomplete="one-time-code">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="h5">Developer</div>
                    <div class="row">
                        <div class="col form-group mb-3">
                            <input type="checkbox" name="ff[FW_SETUP__EN]" value="0" hidden="" checked="">
                            <input type="checkbox" class="xui-field form-check-input" value="1" name="ff[FW_SETUP__EN]" id="<?=$uid=uniqid()?>" <?=($ff['FW_SETUP__EN'] ?? true /* when not exists it is enabled */) ? 'checked' : ''?> onchange="return !this.checked ? (this.checked = !confirm('This will disable web access to this editor!!!\nAre you sure?')) : true">
                            <label class="form-check-label" for="<?=$uid?>">Enable Editing of this Setup Configuration</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col form-group mb-3">
                            <input type="checkbox" name="ff[FW_IS_DEV]" value="0" hidden="" checked="">
                            <input type="checkbox" class="xui-field form-check-input" value="1" name="ff[FW_IS_DEV]" id="<?=$uid=uniqid()?>" <?=($ff['FW_IS_DEV'] ?? false) ? 'checked' : ''?>>
                            <label class="form-check-label" for="<?=$uid?>">Enable Developer Mode</label>
                        </div>
                    </div>
                </div>
                <?php
            });
        }
    }
    
    private function c__data(){
        \_\a([
            "Save" => function(){
                $this->env_base__submit(function(&$ff){
                    if($dir = $_POST['ff']['FW_DATA_DIR'] ?? ''){
                        $this->data_dir__eval($_POST['ff']['FW_DATA_DIR']);
                    } else {
                        $_POST['ff']['FW_DATA_DIR'] = null;
                    }
                    $ff = \array_replace_recursive($ff, $_POST['ff']);
                });
            },
            "Clear" => function(){
                $this->env_data__remove();
            },
        ]);
        $this->ui__tabpage__form_bravo___prt(function() {
            $ff = $this->env_base__json();
            
            $options = $this->module_select_options__get('data');
            
            ?>
                <div class="h5">Data</div>
                <style>
                    .item-list .dropdown-item {
                        cursor:pointer;
                    }
                </style>
                <div class="row">
                    <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Path</span>
                            <input type="text" class="form-control <?=($valid = $this->lsp__resolve($ff['FW_DATA_DIR'] ?? '')) ? '': 'is-invalid'?>" name="ff[FW_DATA_DIR]" value="<?=$ff['FW_DATA_DIR'] ?? ''?>" placeholder="...">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Options</button>
                            <ul class="item-list dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item base-is-data">Base is DATA</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php foreach($options as $k => $v): ?> 
                                    <li><span class="dropdown-item option-text" value="<?=$k?>"><?=$v?></span></li>
                                <?php endforeach ?> 
                            </ul> 
                        </div>
                        <div class="input-group mb-3">                            
                            <span class="input-group-text">Mode</span>
                            <?php \_\html::select([
                                'value' => $ff['FW_MODE'] ?? '',
                                'name' => "ff[FW_MODE]",
                                'class'=> "form-control xui-field-select2",
                                ['' => '--NOT-SELECTED--'] + $this->mode_select_options__get(),
                            ]); ?>
                        </div>
                        <?php if(!$valid): ?>
                        <div class="invalid-feedback">
                            Invalid Location
                        </div>
                        <?php endif ?>
                    </div>
                </div>
                <script>
                    $(document).ready(function(){
                        $('.item-list .dropdown-item.option-text').click(function(){
                            $(this).closest('.input-group').find('input[type="text"]').val($(this).html());
                        });
                        $('.item-list .dropdown-item.base-is-data').click(function(){
                            $(this).closest('.input-group').find('input[type="text"]').val('<?=\_\BASE_DIR?>');
                        });
                    });
                </script>
            <?php /* ------------------------------- FORM BREAK ---------------------------------- */ ?>
                        </div></form>
            <?php /* ------------------------------- FORM BREAK ---------------------------------- */ ?>
                <form action="" method="POST">
                    <div class="container-fluid">
                        <div class="h5 mt-5">CLEAR DATA</div>
                        
                        <div class="row">
                            <div class="col">
                                <input type="hidden" name="--csrf" value="<?=\_\CSRF?>">
                                <input type="submit" class="btn btn-outline-danger" name="--action" value="CLEAR DATA SETTINGS">
                            </div>
                        </div>
            <?php /* ------------------------------- FORM BREAK ---------------------------------- */ ?>
            <?php
        });
    }
   
    
    private function db(){
        return $this->DB ?? ($this->DB = new class {
            private $pdo;
            private $dir__i;
            private $database__i = '';
            private $hostname__i = '';
            private $char_set__i = '';
            private $last_error_message;
            public function __construct(){
                $this->dir__i = \_\p(\_\DATA_DIR."/db");
            }
            public function pdo(){
                $hostname = \_\e('FW_DB_HOSTNAME', 'localhost');
                $database = \_\e('FW_DB_DATABASE', '');
                $char_set = \_\e('FW_DB_CHAR_SET', 'utf8mb4');
                $username = \_\e('FW_DB_USERNAME', 'root');
                $password = \_\e('FW_DB_PASSWORD', '');
                if(!$this->pdo){
                    try {
                        $this->pdo = new \PDO(
                            "mysql:host={$hostname};dbname={$database};charset={$char_set}", 
                            $username,
                            $password,
                            [
                                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                                \PDO::ATTR_EMULATE_PREPARES   => false,
                            ]
                        );
                    } catch (\PDOException $e) {
                        throw new \PDOException($e->getMessage(), (int)$e->getCode());
                    }
                }
                return $this->pdo;
            }
            public function last_error_message(){
                return $this->last_error_message;
            }
            public function database_name(){
                return \_\e('FW_DB_DATABASE', '');
            }
            public function connect(){
                try{
                    $this->last_error_message = null;
                    if($this->pdo()){
                        return true;
                    }
                } catch (\PDOException $ex){
                    $this->last_error_message = $ex->getMessage();
                }
                return false;
            }
            public function __call($name, $args){
                if(\method_exists($this->pdo ?? $this->pdo(),$name)){
                    return $this->pdo->$name(...$args);
                } else {
                    throw new \Exception("Method Not Found '{$name}'");
                }
            }
        });        
    }

    private function db__list_tables(){
        return $this->db()->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);        
    }
    
    private function db__clear(){
        try{
            $this->db()->query('SET foreign_key_checks = 0');
            foreach($this->db__list_tables() as $tblp){
                $this->db()->exec("DROP TABLE IF EXISTS `{$tblp}`");
            }
        } finally {
            $this->db()->query('SET foreign_key_checks = 1');
        }
    }
    
    private function db__mount(){
        //creates the database and loads the backed up copy
    }
    
    private function db__unmount(){
        //takes the backup copy and drops the database
    }
    
    private function db__install(){
        //takes the backup copy and drops the database
    }
    
    private function db__uninstall(){
        //takes the backup copy and drops the database
    }
    
    private function db__get_schema(){
        $table = [];
        foreach($this->db__list_tables() as $tblp){
            $table_create =   $this->db()->query("SHOW CREATE TABLE `$tblp`")->fetch(\PDO::FETCH_ASSOC);
            $table[$tblp]['fields'] = [];
            $table[$tblp]['constraint'] = [];
            $table[$tblp]['keys'] = [];
            $table[$tblp]['ainc'] = '';
            $table[$tblp]['last'] = '';
            $table[$tblp]['first'] = '';
            foreach(\explode("\n", $table_create['Create Table']) as $v){
                //echo "--".$v.'<br>';
                $x = \trim($v);
                if(\str_starts_with($x, 'CREATE TABLE')){
                    $table[$tblp]['first'] = $x;
                } else if(\str_starts_with($x, '`')){
                    if(\str_contains($x,'AUTO_INCREMENT')){
                        $table[$tblp]['fields'][] = str_replace(' AUTO_INCREMENT','',\rtrim($x," \t,"));
                        $table[$tblp]['ainc'] = "MODIFY ".\rtrim($x," \t,").', AUTO_INCREMENT=0'; 
                    } else {
                        $table[$tblp]['fields'][] = \rtrim($x," \t,");
                    }
                } else if(\str_starts_with($x, ')')){
                    $table[$tblp]['last'] = \preg_replace("#AUTO_INCREMENT=\d+ #",'',$x);
                } else if(\str_starts_with($x, 'CONSTRAINT')){
                    $table[$tblp]['constraint'][] = "ADD ".\rtrim($x," \t,");
                } else {
                    $table[$tblp]['keys'][] = "ADD ".\rtrim($x," \t,");
                }
            }
        }
        $schema_1 = '';
        $schema_2 = '';
        $schema_3 = '';
        $schema_4 = '';
        foreach($table as $tblp => $v){
            $schema_1.= "\n\n{$v['first']}\n  ";
            $schema_1.= \implode(",\n  ", $v['fields']);
            $schema_1.= "\n{$v['last']};\n";
            if($v['keys']){
                $schema_2.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_2.= "  ".\implode(",\n  ", $v['keys']);
                $schema_2.="\n;";
            }
            if($v['ainc']){
                $schema_3.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_3.= "  {$v['ainc']}";
                $schema_3.="\n;";
            }
            if($v['constraint']){
                $schema_4.="\n\nALTER TABLE `{$tblp}`\n";
                $schema_4.= "  ".\implode(",\n  ", $v['constraint']);
                $schema_4.="\n;";
            }
        }
        return \trim("{$schema_1}\n\n{$schema_2}\n\n{$schema_3}\n\n{$schema_4}");
    }
    
    private function db__backup_remove(){
        if($f = $_REQUEST['file'] ?? null){
            if(\is_file($f = \_\DATA_DIR."/{$f}")){
                \unlink($f);
            }
        }
    }
    
    private function db__backup_download(){
        if($f = $_REQUEST['file'] ?? null){
            if(\is_file($f = \_\DATA_DIR."/{$f}")){
                static::download($f);
            }
        }
    }
    
    private function db__backup(array $options = []){
        if($this->db__list_tables()){
            $clean = true;
            $dated_backup = true;
            \extract($options);
            $data = [];
            $schema = $this->db__get_schema();
            foreach($this->db__list_tables() as $tblp){
                $data[$tblp] = $this->db()->query("SELECT * FROM `{$tblp}`")->fetchAll();    
            }
            $date = \date("Y-md-Hi-s");
            $file = \_\DATA_DIR."/db-backup-{$date}.json";
            $file_latest = \_\DATA_DIR."/db-backup.json";
            $db['schema'] = $schema;
            $db['data'] = $data;
            \is_dir($d = \dirname($file)) OR \mkdir($d,0777,true);
            $x = json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            \file_put_contents($file_latest, $x);
            if($dated_backup){
                \file_put_contents($file, $x);
            }
            $schema_file = \_\DATA_DIR."/db-schema.sql";
            \file_put_contents($schema_file, $schema);
        }
    }
    
    private function db__restore(){
        if($f = $_REQUEST['file'] ?? null){
            if(\is_file($f = \_\DATA_DIR."/{$f}")){
                if($this->db__list_tables() && $f != \_\DATA_DIR."/db-backup.json"){
                    $this->db__backup(['dated_backup' => false]);
                }
                $this->db__load($f);
            }
        }
    }
    
    private function db__load($file = null){
        if(\is_file($file = $file ?? \_\DATA_DIR."/db-backup.json")){
            $this->db__clear();
            try{
                $this->db()->query('SET foreign_key_checks = 0');
                $db = json_decode(\file_get_contents($file), true);
                $schema = $db['schema'];
                echo $schema;
                $this->db()->exec($schema);
                foreach($db['data'] as $tblp => $data){
                    foreach($data as $record){
                        if($record){
                            $keys = array_keys($record);
                            $l1 = "`".implode('`, `',$keys)."`";
                            $l2 = ":".implode(', :',$keys);
                            $sql = "INSERT INTO `{$tblp}` ({$l1}) VALUES ({$l2})";
                        } else {
                            $sql = "INSERT INTO `{$tblp}` () VALUES();";
                        }
                        $this->db()->prepare($sql)->execute($record);
                    }
                }
            } finally {
                $this->db()->query('SET foreign_key_checks = 1');
            }
        }
    }
    
    private function c__dx(){
        $this->ui__tabpage__plain__prt(function(){
            \_\dx::_()->prt();
        });
    }
    
    private function c__modes(){
        \_\a([
            "Save" => function(){
                $this->env_base__submit(function(&$ff){
                    $input = $_POST['ff'];
                    if($mode = $input['mode'] ?? ''){
                        $ff['FW_MODE'] = $mode;
                    } else {
                        unset($ff['FW_MODE']);
                    }
                });
                
                $this->env_mode__submit(function(&$ff){
                    $input = $_POST['ff'] ?? [];
                    $l = [];
                    foreach($ff ?? [] as $k => $v){
                        if($k){
                            $l[$k] = [$k, $v];
                        }
                    }
                    $r = ['' => ['', $ff[''] ?? []]];
                    foreach($input['modes'] ?? [] as $k => $v){
                        if(\is_numeric($k)){
                            $r[$v] = [$v, []];
                        } else {
                            if($v){
                                $r[$k] = [$v, $l[$k][1] ?? []];
                            } else {
                                unset($r[$k]);
                            }
                        }
                    }
                    $modes = [];
                    foreach($r as $k => $v){
                        $modes[$v[0]] = $v[1];
                    }
                    $ff = $modes;
                });
            },
            "Copy Default" => function(){
                if($mode = $_POST['action-mode-name'] ?? ''){
                    $this->env_mode__submit(function(&$ff) use($mode){
                        $ff[$mode] = $ff[''] ?? [];
                    });
                }
            },
            "Clear" => function(){
                if($mode = $_POST['action-mode-name'] ?? ''){
                    $this->env_mode__submit(function(&$ff) use($mode){
                        $ff[$mode] = [];
                    });
                }
            },
            
        ]);
        $this->ui__tabpage__form_bravo___prt(function(){ ?>
            <style>
                .form-control-sm {
                    min-height: 25px;
                    height: 25px;
                }
                .list-group-item .btn-sm {
                    padding: 0px 0.35rem;
                    min-height: 25px;
                    height: 25px;
                }
            </style>
            <input hidden id="id-action-mode" name="action-mode-name" value="">
            <ul class="list-group modes-list">
                <?php
                    $modes = $this->env_mode__json();
                    unset($modes['']);
                    $modes = \array_keys($modes);
                    \array_unshift($modes,'');
                    $mode = $this->env_base__json()['FW_MODE'] ?? '';
                ?>
                <?php foreach($modes as $k){ ?>
                    <li class="list-group-item item-deletable">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="ff[mode]" value="<?=$k?>" <?=$mode === $k ? 'checked' : '' ?>>
                            <label class="form-check-label">
                                <?php if($k):?>
                                    <input type="text" class="mode-name form-control form-control-sm" name="ff[modes][<?=$k?>]" value="<?=$k?>">
                                <?php else: ?>
                                    -- DEFAULT --
                                <?php endif ?>
                            </label>
                            <?php if($k):?>
                            <div class="float-end d-inline-block">
                                <input type="submit" class="btn btn-outline-danger btn-sm copy-default" name="--action" value="Copy Default">
                                <input type="submit" class="btn btn-outline-danger btn-sm clear" name="--action" value="Clear">
                                <button type="button" class="btn btn-outline-danger btn-sm item-delete">&times;</button>
                            </div>
                            <?php endif ?>
                        </div>
                    </li>
                <?php } ?>
            </ul>
                <div class="row mt-3">
                    <div class="col text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm add-mode">+ Add Mode</button>
                    </div>
                </div>
                <script>
                    $(document).ready(function(){
                        $('button.add-mode').click(function(){
                            $(`
                                <li class="list-group-item item-deletable">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ff[mode]" value="">
                                        <label class="form-check-label">
                                            <input type="text" class="mode-name form-control form-control-sm" name="ff[modes][]" value="">
                                        </label>
                                        <div class="float-end d-inline-block">
                                            <button type="button" class="btn btn-outline-danger btn-sm item-delete">&times;</button>    
                                        </div>
                                    </div>
                                </li>
                            `).appendTo('.modes-list');
                        });
                        $(document).on('change', 'input.mode-name', function(){
                            var v = $(this).val();
                            if(v){
                                $(this).closest('.form-check').find('.form-check-input').val(v);
                                console.log(v);
                            }
                        });
                        $(document).on('click', 'button.item-delete',function(){
                            $(this).closest('.item-deletable').remove();
                        });
                        $(document).on('click', 'input.copy-default',function(){
                            $('#id-action-mode').val($(this).closest('.form-check').find('.mode-name').val());
                        });
                        $(document).on('click', 'input.clear',function(){
                            $('#id-action-mode').val($(this).closest('.form-check').find('.mode-name').val());
                        });
                    });
                    
                </script>
        <?php });
    }

    private function mode_select_options__get(){
        $modes = $this->env_mode__json();
        unset($modes['']);
        $modes = \array_keys($modes);
        \array_unshift($modes,'');
        $list = [];
        foreach($modes as $mode){
            $list[$mode] = $mode;
        }
        return $list;
    }
    
    private function library_select_options__get(){
        static $cache = null;
        if(!\is_null($cache)){
            return $cache;
        } else {
            $list = [];
            foreach(\_\fs\hglob(".epx*", \_\BASE_DIR, GLOB_ONLYDIR | GLOB_BRACE) as $m){
                $list[$m] = $m;
            }
            foreach($this->env_setup__json()['FW_LOCATIONS'] ?? [] as $k => $en){
                if($en && \is_dir($k)){
                    foreach(\glob("{$k}/.epx*", GLOB_ONLYDIR | GLOB_BRACE) as $m){
                        $list[$m] = $m;
                    }
                }
            }
            return $cache = $list;
        }
    }
    
    private function module_select_options__get($type = 'module', array $options = []){
        static $cache = [];
        if(isset($cache[$type])){
            return $cache[$type];
        } else {
            $list = [];
            foreach(\_\fs\hglob(".epx*/{,*}.{$type}", \_\BASE_DIR, GLOB_ONLYDIR | GLOB_BRACE) as $m){
                $list[$m] = $m;
            }
            foreach($this->env_setup__json()['FW_LOCATIONS'] ?? [] as $k => $en){
                if($en && \is_dir($k)){
                    foreach(\glob("{$k}/.epx*/{,*}.{$type}", GLOB_ONLYDIR | GLOB_BRACE) as $m){
                        $list[$m] = $m;
                    }
                }
            }
            return $cache[$type] = $list;
        }
    }
    
    
    private function library_modules__get($libdir){
        static $cache = [];
        if(isset($cache[$libdir])){
            return $cache[$libdir];
        } else {
            $list = [];
            foreach(\glob("{$libdir}/{,*}.{theme,app,module,core}", GLOB_ONLYDIR | GLOB_BRACE) as $m){
                $list[$m] = $m;
            }
            return $cache[$libdir] = $list;
        }
    }
    
    private function c__remote_repo(){
        \_\a([
            "Save" => function(){
                $this->env_setup__submit(function(&$ff){
                    $ff['REMOTE_REPO']['URL'] = $_POST['ff']['url'] ?? '';
                });
            },
        ]);
        $this->ui__tabpage__form_bravo___prt(function() {
            ?><h5>Remote Repo</h5><?php
            \_\ui\theme\ff::text([
                'label' => 'URL',
                'name' => "url",
                'value' => $this->env_setup__json()['REMOTE_REPO']['URL'] ?? '',
            ]);
        });
    }
    
    private function c__library(){
        \_\a([
            "Submit" => function(){
                \_\session_var('selected-lib', $_REQUEST['ff']['lib'] ?? []);
            },
        ]);
        $this->ui__tabpage__form_bravo___prt(function() { ?>
            <input type="hidden" name="--action" value="Submit">
            <div class="row">
                <h5>Library</h5> 
                <?php \_\html::select([
                    'value' => $dir = \_\session_var('selected-lib'),
                    'name' => "ff[lib]",
                    'class'=> "form-control xui-field-select2",
                    ['' => '--NOT-SELECTED--'] + $this->library_select_options__get(),
                    'onchange' => 'this.form.submit()'
                ]); ?>
            </div>
            <?php if(\is_dir($dir)): ?>
            <div class="row">
                <h5 class="mt-3">Installed Modules</h5>
                <ul class="list-group" id="id-installed">
                    <?php foreach($this->library_modules__get($dir) as $k): ?>
                        <li class="list-group-item" data-code="<?=basename($k)?>">
                            <span class="fw-bold"><?=\basename($k)?></span>
                            <span class="font-monospace text-secondary" style="font-size:0.8em"><?=$k?></span>
                        </li>
                    <?php endforeach ?>
                </ul>
                
            </div>
            <?php endif ?>
            <?php if(\is_dir($dir)): ?>
            <div class="row">
                <h5 class="mt-3">Installable Modules</h5>
                <ul class="list-group" id="id-installables">
                </ul>                    
            </div>
            <script>
                $(document).ready(function(){
                    fetch('<?=$this->REMOTE_REPO_URL?>/alpha/--repo/module/manifest')
                    .then(data => {
                        return data.json();
                    })
                    .then(post => {
                        post.forEach((v) => {
                            if($(`#id-installed .list-group-item[data-code="${v.mcode}"]`).length){
                                v.instbtn = {
                                    text:'Installed',
                                    class: "btn-success",
                                }
                            } else {
                                v.instbtn = {
                                    text:'Install',
                                    class: "btn-outline-primary x-install",
                                }
                            }
                            var html = `
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">${v.info.name} <code>[${v.mcode}]</code></div>
                                        ${v.info.desc}
                                    </div>
                                    <button type="button" class="btn btn-sm ${v.instbtn.class}" data-mcode="${v.mcode}">${v.instbtn.text}</button>
                                </li>
                            `;
                            $('#id-installables').append(html);
                        });
                    });
                    
                    $(document).on('click','button.x-install',function(){
                        var mcode = $(this).data('mcode');
                        var vcode = $(this).data('vcode');
                        $(this).html('Installing');
                        var url = `<?=$this->url('.library_module_install').'?mcode=${mcode}&vcode=${vcode}'?>`;
                        console.log({url});
                        fetch(url)
                        .then((response) => {
                            if (!response.ok) {
                                throw Error(response.statusText);
                            }
                            return response;
                        }).then(data => {
                            return data.json();
                        })
                        .then(info => {
                            console.log(info);
                            $(this).html('Installed');
                            window.location.reload();
                        });
                    });
                })
            </script>
            <?php endif ?>
            
        <?php });
    }
    
    private function c__library_module_install(){
        if(!($mcode = $_GET['mcode'] ?? false)){
            \http_response_code(500);
            exit(<<<JSON
                {"status":"error"}
            JSON);
        }
        $uri = "{$this->REMOTE_REPO_URL}/alpha/--repo/module/get?mcode={$mcode}&version=&key=12895746";
        if(1){
            $content = \file_get_contents(
                $uri,
                false, 
                \stream_context_create([
                        'http' => [
                            'method'  => 'GET',
                        ],
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false,
                        ],
                    ]
                ),
            );
            if($content){
                $file = \_\fs\contents('local',"installer/install.zip", $content);
                $dir = \_\session_var('selected-lib');
                if(!\is_dir($dest = "{$dir}/{$mcode}")){
                    $zip = new \ZipArchive;
                    if ($zip->open($file) === TRUE) {
                        $zip->extractTo($dest);
                        $zip->close();
                        \_\x(['content' => ['status' => 'ok', 'status-info' => 'install-completed']]);
                    } else {
                        \_\x(['content' => ['status' => 'error', 'status-info' => 'install-error']]);
                    }
                } else {
                    \_\x(['content' => ['status' => 'error', 'status-info' => 'already-exist']]);
                }
            }
                
        } else {
            $handle = curl_init();

            \curl_setopt($handle, CURLOPT_URL, $uri);
            \curl_setopt($handle, CURLOPT_POST, false);
            \curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
            \curl_setopt($handle, CURLOPT_HEADER, true);
            \curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
        
            $response = \curl_exec($handle);
            $hlength  = \curl_getinfo($handle, CURLINFO_HEADER_SIZE);
            $httpCode = \curl_getinfo($handle, CURLINFO_HTTP_CODE);
            $body     = \substr($response, $hlength);
        
            // If HTTP response is not 200, throw exception
            if ($httpCode != 200) {
                \http_response_code(500);
                exit(<<<JSON
                    {"status":"error"}
                JSON);
                //throw new Exception($httpCode);
            }
        
            $content = $body;
        
            if($content){
                \_\fs\contents('local',"installer/{$mcode}.zip", $contents);        
                \_\x(['content' => ['status' => 'install-completed']]);
            }
        }
    }
    
    private function c__modules(){
        \_\a([
            "Save" => function(){
                0 AND \_\flash('input', $_POST);
                1 AND $this->env_current_mode__submit(function(&$ff){
                    $data["FW_MODULES"] = [];
                    unset($ff["FW_MODULES"]);
                    foreach($_POST['ff']['modules'] ?? [] as $k => $m){
                        switch($k){
                            case 'app':{
                                $data["FW_APP_DIR"] = $m['path'];
                            } break;
                            case 'theme':{
                                $data["FW_THEME_DIR"] = $m['path'];
                            } break;
                            case 'core':{
                                $data["FW_CORE_DIR"] = $m['path'];
                            } break;
                            default: {
                                if(($m['path'] ?? false) && \str_ends_with($k, 'module')){
                                    $data["FW_MODULES"][$m['path']] = ($m['en'] ?? null) ? true : false;
                                }
                            }
                        }
                    }
                    
                    $ff = \array_replace_recursive($ff, $data);
                });
            },
        ]);        
        $this->ui__tabpage__form_bravo___prt(function() {
            $ff = $this->env_current_mode__json();
            ?>
                <style>
                    .form-control-sm {
                        min-height: 25px;
                        height: 25px;
                    }
                    .list-group-item .btn-sm {
                        padding: 0px 0.35rem;
                        min-height: 25px;
                        height: 25px;
                    }
                </style>
                <?php 
                    $cx__fn = function($k, $en, $i, $label, $options, $valid = true, $is_module = true){ ?>
                    <li class="list-unstyled item-deletable <?=$is_module ? 'xui-sortable-body': ''?>">
                        <div class="input-group mb-3">
                            <span class="input-group-text <?=$is_module ? 'xui-sortable-handle': ''?>" style="width:100px"><?=$label?></span>
                            <?php if($is_module): ?>
                            <div class="input-group-text">
                                <?php \_\html::el('input', [
                                       'class' => "form-check-input mt-0", 
                                       'type' => "checkbox", 
                                       'name' => "ff[modules][$i][en]", 
                                       'value' => "1",
                                       null,
                                       $en ? 'checked' : ''
                                ]) ?>
                            </div>
                            <?php endif ?>
                            <?php \_\html::select([
                                'value' => $k,
                                'name' => "ff[modules][$i][path]",
                                'class'=> "form-control xui-field-select2",
                                ['' => '--NOT-SELECTED--'] + $options,
                            ]); ?>
                            <?php if($is_module): ?>
                            <button type="button" class="btn btn-outline-danger btn-sm item-delete">&times;</button>
                            <?php endif ?>
                        </div>
                        <?php if(!$valid): ?>
                            <div class="invalid-feedback">
                                Invalid Location
                            </div>
                        <?php endif ?>
                    </li>
                    <?php };
                ?>
                <h5>Fixed Modules</h5>
                <?php
                    $cx__fn($k = $ff['FW_THEME_DIR'] ?? '', isset($ff['FW_THEME_DIR']), 'theme', 'Theme', $this->module_select_options__get('theme'), \is_dir($k), false);
                    $cx__fn($k = $ff['FW_APP_DIR'] ?? '', isset($ff['FW_APP_DIR']), 'app', 'App', $this->module_select_options__get('app'), \is_dir($k), false);
                    $cx__fn($k = $ff['FW_CORE_DIR'] ?? '', isset($ff['FW_CORE_DIR']), 'core', 'Core', $this->module_select_options__get('core'), \is_dir($k), false);
                ?>
                <h5>Dynamic Modules</h5>
                <ul class="list-group modules-list xui-sortable-pool">
                <?php 
                    $i=0; 
                    foreach($ff['FW_MODULES'] ?? [] as $k => $v){
                        $i++;
                        $cx__fn($k, $v, "{$i}.module", 'Module', $this->module_select_options__get('module'), \is_dir($k), true);
                    }
                ?>
                </ul>
                <div class="row mt-3">
                    <div class="col text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm add-module">+ Add Module</button>
                    </div>
                </div>
                <?php 0 AND \_\print_ln(\_\flash('input')) ?>
                <script>
                    $(document).ready(function(){
                        $('button.add-module').click(function(){
                            var i = Date.now();
                            $(`
                                <?php $cx__fn(null, true, '${i}.module', 'Module', $this->module_select_options__get('module'), true, true) ?>
                            `).appendTo('.modules-list');
                        });
                        $(document).on('click', 'button.item-delete',function(){
                            $(this).closest('.item-deletable').remove();
                        });
                    });
                </script>
            <?php
        });
    }
    
    private function c__site_info(){
        if(\_\IS_ACTION){
            switch($_POST['--action'] ?? false){
                case "Save":{
                    $this->env_current_mode__submit(function(&$ff){
                        $ff = array_replace_recursive($ff, $_POST['ff']);
                    });
                } break;
            }
            \header("Location: {$_SERVER['REQUEST_URI']}"); exit();
        } else {
            \_\ui\theme\ff::page(function() {
                $this->ui__tabpanel__prt(function() { $ff = $this->env_current_mode__json();
                ?>
                    <form action="" method="POST">
                        <input type="hidden" name="--csrf" value="<?=\_\CSRF?>">
                        <div class="container-fluid sticky-top pt-1 px-2" style="z-index:1">
                            <div class="row">
                                <div class="col">
                                    <div class="float-end">
                                        <input class="btn btn-outline-primary" type="submit" name="--action" value="Save">
                                    </div>
                                </div>
                                <input type="hidden" name="--cfg" value="<?=$_GET['--cfg'] ?? ''?>">
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="ff(FW_SITE)(TITLE)" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="ff(FW_SITE)(TITLE)" name="ff[FW_SITE][TITLE]" placeholder="My Site" value="<?=$ff['FW_SITE']['TITLE'] ?? ''?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="ff(FW_SITE)(TITLE)" class="form-label">Info</label>
                                        <textarea class="form-control" id="ff(FW_SITE)(INFO)" name="ff[FW_SITE][INFO]" rows="3"><?=$ff['FW_SITE']['INFO'] ?? ''?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="ff(FW_SITE)(EMAIL)" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="ff(FW_SITE)(EMAIL)" name="ff[FW_SITE][EMAIL]" placeholder="site@company.com" value="<?=$ff['FW_SITE']['EMAIL'] ?? ''?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="ff(FW_SITE)(CONTACT)" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="ff(FW_SITE)(CONTACT)" name="ff[FW_SITE][CONTACT]" placeholder="(+XX) _ _ _ _ _ _" value="<?=$ff['FW_SITE']['CONTACT'] ?? ''?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="ff(FW_SITE)(ADDRESS)" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="ff(FW_SITE)(ADDRESS)" name="ff[FW_SITE][ADDRESS]" placeholder="..." value="<?=$ff['FW_SITE']['ADDRESS'] ?? ''?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php
                });
            });
        }
    }     
    
    private function c__db_schema(){
        echo "<pre>".$this->db__get_schema();
    }
    
    private function c__htaccess(){
        if(\_\IS_ACTION){
            switch($_POST['--action'] ?? false){
                case "Save":{
                    $this->env_base__submit(function(&$ff){
                        $ff = array_replace_recursive($ff, $_POST['ff']);
                    });
                    $ff = $this->env_base__json($file);
                    $f_htaccess = \_\BASE_DIR.'/.htaccess';
                    $d_htaccess = '';
                    { $d_htaccess.=<<<HTACCESS
                    <IfModule mod_rewrite.c>
                        RewriteEngine On
                    HTACCESS; }
                    if($ff['FW_HTACCESS_FORCE_HTTPS'] ?? null){ $d_htaccess.=<<<HTACCESS
                        
                        #-------------------------------------------------------------------------------
                        #* note: for auto https
                        RewriteCond %{HTTPS} off 
                        RewriteCond %{SERVER_PORT} 80
                        RewriteRule (.*) https://%{SERVER_NAME}%{REQUEST_URI} [L]
                    HTACCESS; }
                    if($ff['FW_HTACCESS_FORCE_WWW'] ?? null){ $d_htaccess.=<<<HTACCESS
                        
                        #-------------------------------------------------------------------------------
                        #* note: if you need www
                        RewriteCond %{HTTP_HOST} !^www\. [NC]
                        RewriteRule ^(.*)$ https://www.%{HTTP_HOST}%{REQUEST_URI} [R=302,L]
                    HTACCESS; }
                    if($ff['FW_HTACCESS_ENABLE_BASIC_AUTH'] ?? null){ $d_htaccess.=<<<HTACCESS
                        
                        #-------------------------------------------------------------------------------
                        #* note: for basic http authorization
                        RewriteCond %{HTTP:Authorization} ^(.+)$
                        RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
                        #-------------------------------------------------------------------------------
                        #* note: for content type 
                        RewriteRule .* - [E=HTTP_CONTENT_TYPE:%{HTTP:Content-Type},L]
                    HTACCESS; }
                    { $d_htaccess.=<<<HTACCESS
                        
                        #-------------------------------------------------------------------------------
                        #* note: for pax legacy routing
                        RewriteCond %{REQUEST_URI} !(favicon.ico)|(/.*\-pub[\.\/].*)
                        RewriteRule . index.php [L,QSA]
                        RewriteCond %{REQUEST_FILENAME} !-f
                        RewriteCond %{REQUEST_FILENAME} !-d
                        RewriteRule . index.php [L,QSA]
                    </IfModule>                        
                    HTACCESS; }
                    \file_put_contents($f_htaccess, $d_htaccess);
                } break;
            }
            \header("Location: {$_SERVER['REQUEST_URI']}"); exit();
        } else {
            \_\ui\theme\ff::page(function() {
                $this->ui__tabpanel__prt(function() { $ff = $this->env_base__json();
                ?>
                    <form action="" method="POST">
                        <input type="hidden" name="--csrf" value="<?=\_\CSRF?>">
                        <div class="container-fluid sticky-top pt-1 px-2" style="z-index:1">
                            <div class="row">
                                <div class="col">
                                    <div class="float-end">
                                        <input class="btn btn-outline-primary" type="submit" name="--action" value="Save">
                                    </div>
                                </div>
                                <input type="hidden" name="--cfg" value="<?=$_GET['--cfg'] ?? ''?>">
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col form-group mb-3">
                                    <input type="checkbox" name="ff[FW_HTACCESS_ENABLE_BASIC_AUTH]" value="0" hidden="" checked="">
                                    <input type="checkbox" class="xui-field form-check-input" value="1" name="ff[FW_HTACCESS_ENABLE_BASIC_AUTH]" id="ff(FW_HTACCESS_ENABLE_BASIC_AUTH)" <?=($ff['FW_HTACCESS_ENABLE_BASIC_AUTH'] ?? false) ? 'checked' : ''?>>
                                    <label class="form-check-label" for="ff(FW_HTACCESS_ENABLE_BASIC_AUTH)">Enable Basic AUTH</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col form-group mb-3">
                                    <input type="checkbox" name="ff[FW_HTACCESS_FORCE_WWW]" value="0" hidden="" checked="">
                                    <input type="checkbox" class="xui-field form-check-input" value="1" name="ff[FW_HTACCESS_FORCE_WWW]" id="ff(FW_HTACCESS_FORCE_WWW)" <?=($ff['FW_HTACCESS_FORCE_WWW'] ?? false) ? 'checked' : ''?>>
                                    <label class="form-check-label" for="ff(FW_HTACCESS_FORCE_WWW)">Force WWW</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col form-group mb-3">
                                    <input type="checkbox" name="ff[FW_HTACCESS_FORCE_HTTPS]" value="0" hidden="" checked="">
                                    <input type="checkbox" class="xui-field form-check-input" value="1" name="ff[FW_HTACCESS_FORCE_HTTPS]" id="ff(FW_HTACCESS_FORCE_HTTPS)" <?=($ff['FW_HTACCESS_FORCE_HTTPS'] ?? false) ? 'checked' : ''?>>
                                    <label class="form-check-label" for="ff(FW_HTACCESS_FORCE_HTTPS)">Force HTTPS</label>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php                    
                });
            });
        }
    }
    
    private function c__database(){
        
        if(\_\IS_ACTION){
            switch($_POST['--action'] ?? false){
                case "Save":{
                    $this->env_data__submit(function(&$ff){
                        $ff = array_replace_recursive($ff, $_POST['ff']);
                        ($ff['FW_DB_HOSTNAME'] ?? null) OR $ff['FW_DB_HOSTNAME'] = null;
                        ($ff['FW_DB_USERNAME'] ?? null) OR $ff['FW_DB_USERNAME'] = null;
                        ($ff['FW_DB_PASSWORD'] ?? null) OR $ff['FW_DB_PASSWORD'] = null;
                        ($ff['FW_DB_DATABASE'] ?? null) OR $ff['FW_DB_DATABASE'] = null;
                        ($ff['FW_DB_CHAR_SET'] ?? null) OR $ff['FW_DB_CHAR_SET'] = null;
                    });
                } break;
                case 'Mount':{
                    $this->db()->execute(include $this->model()->file('schema','.sql-php'));
                } break; 
                case 'Unmount':{
                    $this->db__backup();
                    //$this->db()->execute("DROP TABLE `{$this->tblp()}`");
                } break;
                case 'Backup':{
                    $this->db__backup();
                } break;
                case 'Restore':{
                    $this->db__restore();
                } break;
                case 'Remove':{
                    $this->db__backup_remove();
                } break;
                case 'Load':{
                    $this->db__load();
                } break;
                case 'Clear':{
                    $this->db__clear();
                } break;
                case 'Download':{
                    $this->db__backup_download();
                }
            }
            \header("Location: {$_SERVER['REQUEST_URI']}"); exit();
            
        } else { \_\ui\theme\ff::page(function() { $this->ui__tabpanel__prt(function() { 
            
            $ff = $this->env_data__json();
            if($this->db()->connect()){
                $db_is_connected = true;
                $alert_type = 'success';
                if($dbname = $this->db()->database_name()){
                    $alert_message = "Connected To Database <strong>{$dbname}</strong>";
                } else {
                    $alert_message = "Connected To Server (Database not specified!)";
                }
            } else {
                $db_is_connected = false;
                $alert_type = 'danger';
                $alert_message = $this->db()->last_error_message();
            }
            
            ?>
                <form action="" method="POST">
                    <input type="hidden" name="--csrf" value="<?=\_\CSRF?>">
                    <div class="container-fluid sticky-top mt-1" style="z-index:1">
                        <div class="row">
                            <div class="col">
                                <div class="float-end">
                                  <div class="btn-group">
                                        <input type="submit" class="btn btn-outline-primary btn-sm" name="--action" value="Save">
                                        <?php if($db_is_connected):?>
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Backup"></li>
                                            <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Load"></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Clear"></li>
                                        </ul>
                                        <?php endif;?>
                                    </div>                                    
                                </div>
                            </div>
                            <input type="hidden" name="--cfg" value="<?=$_GET['--cfg'] ?? ''?>">
                        </div>
                    </div>
                    <div class="container-fluid mt-1">
                        <div class="row">
                            <div class="col">
                                <div class="alert alert-<?=$alert_type?>" role="alert">
                                    <?= $alert_message ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="ff(FW_DB_HOSTNAME)" class="form-label">Host</label>
                                    <input type="text" class="form-control" id="ff(FW_DB_HOSTNAME)" name="ff[FW_DB_HOSTNAME]" placeholder="..." value="<?=$ff['FW_DB_HOSTNAME'] ?? ''?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="ff(FW_DB_USERNAME)" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="ff(FW_DB_USERNAME)" name="ff[FW_DB_USERNAME]" placeholder="..." value="<?=$ff['FW_DB_USERNAME'] ?? ''?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="ff(FW_DB_PASSWORD)" class="form-label">Password</label>
                                    <input type="text" class="form-control" id="ff(FW_DB_PASSWORD)" name="ff[FW_DB_PASSWORD]" placeholder="..." value="<?=$ff['FW_DB_PASSWORD'] ?? ''?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="ff(FW_DB_DATABASE)" class="form-label">Database</label>
                                    <input type="text" class="form-control" id="ff(FW_DB_DATABASE)" name="ff[FW_DB_DATABASE]" placeholder="..." value="<?=$ff['FW_DB_DATABASE'] ?? ''?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="ff(FW_DB_CHAR_SET)" class="form-label">Char Set</label>
                                    <input type="text" class="form-control" id="ff(FW_DB_CHAR_SET)" name="ff[FW_DB_CHAR_SET]" placeholder="..." value="<?=$ff['FW_DB_CHAR_SET'] ?? ''?>">
                                </div>
                            </div>
                        </div>                           
                    </div>
                </form>
                <?php if($db_is_connected): ?>
                    <div class="container-fluid">
                        <ul class="list-group mt-3">
                            <?php 
                                $list = \glob(\_\DATA_DIR."/db-backup-*.json");
                                \rsort($list);
                                if(\is_file($latest = \_\DATA_DIR."/db-backup.json")){
                                    \array_unshift($list, $latest);
                                }
                                foreach($list as $f): ?>
                                <li class="list-group-item">
                                    <span title="<?=\hash_file("md5",$f)?>"><?=\basename($f)?></span>
                                    <form class="float-end" action="" method="POST">
                                        <input hidden name="--csrf" value="<?=\_\CSRF?>">
                                        <input type="hidden" name="file" value="<?=\basename($f)?>">
                                        <div class="float-end">
                                            <div class="btn-group">
                                                <input type="submit" class="btn btn-outline-primary btn-sm" name="--action" value="Restore" onclick="return confirm('This will change the database!!!\nAre you sure?')) ? true : event.preventDefault()">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Download"></li>
                                                    <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Remove"></li>
                                                </ul>
                                            </div>                                    
                                        </div>
                                    </form>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php elseif(\_\f('db/schema.sql', true)): ?>
                    <form action="" method="POST">
                        <div class="card">
                            <div class="card-body">
                                <input type="submit" class="btn btn-primary" name="--action" value="Initialize">
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center">
                        This is not a valid database
                    </div>
                <?php endif ?>

            <?php
        }); }); }
    }
    

    private function c__composer(){
        \_\a([
            "Save" => function(){
                $this->env_current_mode__submit(function(&$ff){
                    ($_POST['ff']['enabled'] ?? null) AND $ff['INITS']['composer'] = "include:../.pkg/vendor/autoload.php";
                });
            },
        ]);
        $this->ui__tabpage__form_bravo___prt(function(){
            $ff = $this->env_current_mode__json();
            \_\ui\theme\ff::checkbox(['name' => '[enabled]', 'label' => 'Enable Vendor Package', 'value' => ($ff['INITS']['composer'] ?? null) ? 1 : 0 ]);
        });
    }

    private function c__cmd(){
        if(\_\IS_ACTION){

            // \chdir('..');
            
            // $cmd = "";
            // $output = "";
            // $error = "";
            // $capture = "";
            
            // try {
            //     ob_start();
            //     if($command_exec = $_GET['cmd'] ?? null){
            //         echo
            //             "<h3>CMD</h3>",
            //             "<pre>", 
            //             $command_exec ?: 'No Command', 
            //             "</pre>"
            //         ;        
            //         if(function_exists('systemx')) {
            //             echo "<h4>system</h4>";
            //             system_x($command_exec);
            //             //$output = ob_get_contents();
            //         } else if(function_exists('passthrux')) {
            //             echo "<h4>passthru</h4>";
            //             passthru_x($command_exec);
            //             //$output = ob_get_contents();
            //         } else if(function_exists('execx')) {
            //             echo "<h4>exec</h4>";
            //             exec_x($command_exec , $output);
            //             $output = implode("\n" , $output);
            //         } else if(function_exists('shell_execx')) {
            //             echo "<h4>shell_exec</h4>";
            //             $output = shell_exec_x($command_exec);
            //         } else {
            //             $output = 'Command execution not possible on this system';
            //         }
            //     }
            // } catch (\Exception $ex) {
            //     $error = (string) $ex;
            // } finally {
            //     $capture = ob_get_contents();
            //     ob_end_clean();    
            // }
            
            // while(\ob_get_level() > 0){ @\ob_end_clean(); }
            
            // echo
            //     "<h3>Output</h3>",
            //     "<pre>", 
            //     $output, 
            //     "</pre>",
            //     "<h3>Capture</h3>",
            //     "<pre>", 
            //     $capture, 
            //     "</pre>",
            //     "<h3>Error</h3>",
            //     "<pre>", 
            //     $error, 
            //     "</pre>"
            // ;
            
            \header("Location: {$_SERVER['REQUEST_URI']}"); exit();
        } else { \_\ui\theme\ff::page(function() { $this->ui__tabpanel__prt(function() { 
            
            ?>
            <div class="container-fluid mt-2">
                <style>
                    .cmd-container {
                        background-color: #000000;
                        width: 100%;
                        height: calc(100vh - 180px);
                        padding: 3em;
                    }
                </style>
                <pre class="cmd-container">
                
                
                </pre>
                <form action="">
                    <div class="row">
                        <div class="col">
                            <input type="text" name="cmd" class="form-control">
                        </div>
                    </div>
                    
                </form>
            </div>
            <?php
        }); }); }
    }
    
    
    private function c__email(){
        \_\a([
            "Save" => function(){
                $this->env_setup__submit(function(&$ff){
                    $ff = \array_replace_recursive($ff, $_POST['ff']);
                });
            },
        ]);
        $this->ui__tabpage__form_bravo___prt(function(){

        });
    }   
    
    private function c__install(){
        \_\a([
            "Save" => function(){
                // todo: save action
            },
        ]);
        $this->ui__tabpage__form_bravo___prt(function(){
                // todo: ui code
        });
    }   
    
    
    private function c__settings(){
        \_\a([
            "Save" => function(){
                $this->env_setup__submit(function(&$ff){
                    $data["FW_LOCATIONS"] = [];
                    unset($ff["FW_LOCATIONS"]);
                    foreach($_POST['ff']['locations'] ?? [] as $k => $m){
                        if(($m['path'] ?? false)){
                            $data["FW_LOCATIONS"][$m['path']] = ($m['en'] ?? null) ? true : false;
                        }
                    }
                    $data['tabs'] = $_POST['ff']['tabs'];
                    $ff = \array_replace_recursive($ff, $data);
                });
            },
        ]);
        $this->ui__tabpage__form_bravo___prt(function(){
            $ff = $this->env_setup__json();
            ?>
                <style>
                    .form-control-sm {
                        min-height: 25px;
                        height: 25px;
                    }
                    .list-group-item .btn-sm {
                        padding: 0px 0.35rem;
                        min-height: 25px;
                        height: 25px;
                    }
                </style>
                <?php 
                    $cx__fn = function($k, $en, $i, $label, $valid = true, $is_module = true){ ?>
                    <li class="list-unstyled item-deletable <?=$is_module ? 'xui-sortable-body': ''?>">
                        <div class="input-group mb-3">
                            <span class="input-group-text <?=$is_module ? 'xui-sortable-handle': ''?>" style="width:100px"><?=$label?></span>
                            <div class="input-group-text">
                                <?php \_\html::el('input', [
                                       'class' => "form-check-input mt-0", 
                                       'type' => "checkbox", 
                                       'name' => "ff[locations][$i][en]", 
                                       'value' => "1",
                                       null,
                                       $en ? 'checked' : ''
                                ]) ?>
                            </div>
                            <?php \_\html::input([
                                'value' => $k,
                                'name' => "ff[locations][$i][path]",
                                'class'=> "form-control ".($valid ? '' : 'is-invalid'),
                            ]); ?>
                            <?php if(!$valid): ?>
                                <div class="input-group-text text-danger">
                                    Invalid Location
                                </div>
                            <?php endif ?>
                            <button type="button" class="btn btn-outline-danger btn-sm item-delete">&times;</button>
                        </div>
                    </li>
                    <?php };
                ?>
                <h5>Library Locations</h5>
                <div class="item-list">
                    <ul class="list-group xui-sortable-pool">
                    <?php 
                        $i=0; 
                        foreach($ff['FW_LOCATIONS'] ?? [] as $k => $v){
                            $i++;
                            $cx__fn($k, $v, $i, 'Location', \is_dir($k), true);
                        }
                    ?>
                    </ul>
                    <div class="row mt-3">
                        <div class="col text-end">
                            <button type="button" class="btn btn-outline-primary btn-sm x-item-add">+ Add Location</button>
                        </div>
                    </div>
                </div>
                <?php 0 AND \_\print_ln(\_\flash('input')) ?>
                <script>
                    $(document).ready(function(){
                        $('.item-list button.x-item-add').click(function(){
                            var i = Date.now();
                            $(`
                                <?php $cx__fn(null, true, '${i}', 'Location', true, true) ?>
                            `).appendTo($(this).closest('.item-list').find('.list-group'));
                        });
                        $(document).on('click', 'button.item-delete',function(){
                            $(this).closest('.item-deletable').remove();
                        });
                    });
                </script>
            <?php
            
            ?><h5>Tabs</h5><?php
            \_\ui\theme\ff::checkbox(['name' => '[tabs][modes]', 'label' => 'Modes', 'value' => $ff['tabs']['modes'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][library]', 'label' => 'Library', 'value' => $ff['tabs']['library'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][modules]', 'label' => 'Modules', 'value' => $ff['tabs']['modules'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][database]', 'label' => 'Database', 'value' => $ff['tabs']['database'] ?? 1 ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][remote_repo]', 'label' => 'Remote Repo', 'value' => $ff['tabs']['remote_repo'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][site_info]', 'label' => 'Site Info', 'value' => $ff['tabs']['site_info'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][cmd]', 'label' => 'CMD', 'value' => $ff['tabs']['cmd'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][composer]', 'label' => 'Composer', 'value' => $ff['tabs']['composer'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][email]', 'label' => 'Email', 'value' => $ff['tabs']['email'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][dx]', 'label' => 'Diagnostics', 'value' => $ff['tabs']['dx'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][htaccess]', 'label' => 'Htaccess', 'value' => $ff['tabs']['htaccess'] ?? null ]);
            \_\ui\theme\ff::checkbox(['name' => '[tabs][admin]', 'label' => 'Admin', 'value' => $ff['tabs']['admin'] ?? null ]);

        });
    }
    
    protected function tabs(){
        $tabs = $this->SETTINGS['tabs'] ?? null;
        ($tabs['data'] ?? 1) AND yield 'data' => 'Data';
        if(\is_dir(\_\DATA_DIR)){
            ($tabs['modes'] ?? 0) AND yield 'modes' => 'Modes';
            ($tabs['modules'] ?? 0) AND yield 'modules' => 'Modules';
            ($tabs['library'] ?? 0) AND yield 'library' => 'Library';
            ($tabs['database'] ?? 1) AND yield 'database' => 'Database';
            ($tabs['remote_repo'] ?? 0) AND yield 'remote_repo' => 'Remote Repo';
            ($tabs['site_info'] ?? 0) AND yield 'site_info' => 'Site&nbsp;Info';
            ($tabs['cmd'] ?? 0) AND yield 'cmd' => 'CMD';
            ($tabs['htaccess'] ?? 0) AND yield 'htaccess' => 'Htaccess';
            ($tabs['email'] ?? 0) AND yield 'email' => 'Email';
            ($tabs['composer'] ?? 0) AND yield 'composer' => 'Composer';
            ($tabs['dx'] ?? 0) AND yield 'dx' => 'Diagnostics';
            ($tabs['admin'] ?? 0) AND yield 'admin' => 'Admin';
        }
        yield 'settings' => '<i class="bi bi-gear"></i>';
    } 
    
    
    private function c__extra(){
        \_\a([
            "Save" => function(){
                0 AND \_\flash('input', $_POST);
                1 AND $this->env_setup__submit(function(&$ff){
                });
            },
        ]);
        $this->ui__tabpage__form_bravo___prt(function() {
            $ff = $this->env_setup__json();

        });
    }
    
}; }