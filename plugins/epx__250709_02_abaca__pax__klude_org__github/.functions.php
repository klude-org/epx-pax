<?php 

#endregion
# ######################################################################################################################
#region Path
namespace _ { if(!\function_exists(p::class)){ function p(string $expr, int $levels = 0){
    return \str_replace('\\','/', $levels ? \dirname($expr , $levels) : $expr);
}}}
namespace _ { if(!\function_exists(p__is_rooted::class)){ function p__is_rooted($expr){
    return ($expr[0]??'')=='/' || ($expr[1]??'')==':';
}}}
namespace _ { if(!\function_exists(slashes::class)){ function slashes($path){ 
    return \str_replace('\\','/',$path); 
}}}
namespace _ { if(!\function_exists(backslashes::class)){ function backslashes($path){ 
    return \str_replace('/','\\', $path); 
}}}
#endregion
# ######################################################################################################################
#region Type
namespace _\type { if(!\function_exists(basename::class)){ function basename(string $fqcn){
    $pos = \strrpos($fqcn, '\\');
    return $pos !== false ? substr($fqcn, $pos + 1) : $fqcn;
}}}
namespace _\type { if(!\function_exists(name::class)){ function name($path){
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
namespace _\type { if(!\function_exists(path::class)){ function path($path){
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
#endregion
# ######################################################################################################################
#region File
namespace _ { if(!\function_exists(f::class)){ function f(...$args){
    return \_::file(...$args);
}}}
namespace _ { if(!\function_exists(g::class)){ function g(...$args){ 
    return \_::glob(...$args);
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(get_caller::class)){ function get_caller($offset = 0){
    $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $offset);
    // Index 0: get_caller
    // Index 1: my_function
    // Index 2: caller of my_function (this is what we want)
    return $backtrace[2 + $offset] ?? null;
}}}
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(path_here::class)){ function path_here(string $path) {
    if($file = \_\get_caller(-1)['file'] ?? null){
        return \dirname($file)."/{$path}";
    }
}}}
#endregion
# ######################################################################################################################
#region Dx
namespace _ { if(!\function_exists(on_default::class)){ function on_default($on_default = null){
    if($on_default instanceof \Throwable){
        throw $on_default;
    } else if($on_default instanceof \closure){
        return ($on_default)();
    } else {
        return $on_default;
    }
}}}
# ######################################################################################################################
#region CANVAS
namespace _ { if(!\function_exists(clear::class)){ function clear($top){
    while(\ob_get_level() > \_\OB_OUT){ 
        @\ob_end_clean(); 
    }
    \ob_start();
}}}
namespace _ { if(!\function_exists(clean::class)){ function clean(callable $to = null, bool $restart = true, bool $till = \_\OB_OUT){
    ($till <= \_\OB_TOP) OR $till = \_\OB_TOP;
    if($to){
        $i = $till + 1;
        while(\ob_get_level() > $i){ 
            @\ob_end_clean(); 
        }
        $d = @\ob_get_clean();
        $restart AND \ob_start();
        if(\is_callable($to)){
            return ($to)($d);
        } else {
            return $d;
        }
    } else {
        while(\ob_get_level() > $till){ 
            @\ob_end_clean(); 
        }
        $restart AND \ob_start();
    }
}}}
namespace _ { if(!\function_exists(capture::class)){ function capture(callable|null $to = null){
    $d = \ob_get_contents(); 
    \ob_end_clean();  
    \ob_start();
    if(\is_callable($to)){
        return ($to)($d);
    } else {
        return $d;
    }
}}}
namespace _ { if(!\function_exists(render::class)){ function render(mixed $expr, bool|array $params = [], bool $texate = false){
    if(\is_bool($params)){
        $texate = $params;
        $params = [];
    }
    if(!$expr && $expr != 0){
        if($texate){
            return '';
        } else {
            echo '';
            return;
        }
    } else if(\is_scalar($expr)){
        if($texate){
            return $expr;
        } else {
            echo $expr;
            return;
        }
    }
    
    try{ 
        $texate AND \ob_start();
        if(\is_array($expr) && ($params[0] ?? null) === true){
            foreach($expr as $v){
                if(\is_scalar($v)){
                    echo $v;
                }
            }
        } else if($expr instanceof \closure) {
            \is_array($params) ? ($expr)(...$params) : ($expr)();
        } else if($expr instanceof \SplFileInfo) {
            include $expr;
        } else if($expr instanceof \_\i\prt__i){
            \is_array($params) ? $expr->prt(...$params) : ($expr)();
        } else {
            echo '<pre>'.\json_encode($expr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'<pre>';
        }
    } finally { 
        if($texate){
            $d = \ob_get_contents(); \ob_end_clean();  
        }  
    }
    if($texate){
        return $d; //* if returned in finally exceptions get lost
    }
}}}
namespace _ { if(!\function_exists(texate::class)){ function texate(mixed $expr, array $params = []){
    return \_\render($expr, $params, true);
}}}
#endregion
# ######################################################################################################################
#region Misc
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
#endregion
# ######################################################################################################################
#region Misc
namespace _ { if(!\function_exists(pre::class)){ function pre($o){
    echo "<pre>";    
    echo \json_encode($o,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo "</pre>";
}}}
#endregion
# ######################################################################################################################
#region DX
namespace _ { if(!\function_exists(dx::class)){ function dx(string|array|callable|\Throwable $k = null,...$v){
    static $dx; $dx ?? $dx = (function(){ 
        $dx = (object)[];
        $dx->FAULTS = [];
        $dx->options= [];
        return $dx;
    })();
    if(\is_null($k)){
        return (array) $dx;
    } else if($k instanceof \Throwable){
        $dx->FAULTS[\date('Y-md-Hi-s').'/'.\uniqid()] = $ex = $k;
        switch(\_\INTFC){
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
                    <pre style="overflow:auto; color:red;border:1px solid red;padding:5px;"><br>{$ex}</pre>
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
    } else if(\is_string($k)) {
        if($v){
            if(\is_null($v[0])){
                unset($dx->options[$k]);
            } else {
                $dx->options[$k] = $v[0];
            }
        } else {
            return $dx->$k ?? $dx->options[$k] ?? null;
        }  
    } else if(\is_callable($value[0])) {
        try{
            \error_clear_last();
            $dx->options['error_mask'] = true;
            return ($value[0])();
        } finally { 
            $dx->options['error_mask'] = false;
            \error_clear_last();
        }
    } else if(\is_array($k)) {
        $dx->options = \array_replace($dx->options, $k);
    }
}}}



