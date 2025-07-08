<?php namespace epx__250627_01_neo_ui__pax__klude_org__github\web;

class response {
    
    use \_\i\singleton__t;
    
    protected function __construct(){ }
    
    public function __get($n){
        static $N =[];  return $N[$k = \strtolower($n)] ?? ($N[$k] = (static::class.'\\'.$k)::_());
    }
    
    public static function abort(int $httpcode_or_level = 1, string $message = null){
        if($httpcode_or_level < 100){
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', $httpcode_or_level);
            exit();
        } else {
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
            \http_response_code($code);
            echo $message;
            exit();
        }
    }
    public static function redirect(bool|string|null $url = null, bool|string|array|null $query = null){
        if(\is_null($url)){
            $goto = $_SERVER['REQUEST_URI'];
        } else if($url === true){
            $goto = \strtok($_SERVER['REQUEST_URI'],'?');
            //$query_is_merged
        } else if($url === false){
            $goto = \strtok($_SERVER['REQUEST_URI'],'?');
        } else if($url === '.'){
            if(defined('_\CTR_URL')){
                $goto = \_['ctr_url'];
            } else {
                $goto = \_\URL['panel'];
            }
        } else if($url[0] == '?') {
            //* note: URP - pure path
            $goto = \_['ctr_url'].$url; 
        } else if($url[0] == '.') {
            if(defined('_\CTR_URL')){
                if(($url[1] ?? '') == '/'){
                    $goto = \rtrim(\_['ctr_url'].\substr($url,1), '/.');
                } else {
                    $goto = \rtrim(\_['ctr_url'].$url,'.');
                }
            } else {
                $goto = \_\URL['panel'].'/'.substr($url,1);
            }
        } else if($url[0] == '/') {
            $goto = $url;
        } else if(preg_match('/^http[s]?:/',$url)){
            $goto = $url;
        } else {
            $goto = \_\URL['panel'].'/'.$url;
        }
        
        if($goto){
            if($query === true){
                $goto .= "?0=".\_\MSTART;
            } else if(\is_array($query)){
                $goto .= "?".\http_build_query($query);
            }
        }
        
        if($goto){
            \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
            while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
            \header("Location: ". $goto);
        }
        exit();
    }
    public static function respond($response = null){
        if(\is_null($response) || $response === false){
            exit();
        } else if(\is_scalar($response)){
            $GLOBALS['--EXIT'] = (object)[
                'type' => 'scalar',
                'code' => 200,
                'content' => $response,
            ];
        } else if($response instanceof \Throwable) {
            $ex = $response;
            $GLOBALS['--EXIT'] = (object)[
                'type' => 'scalar',
                'code' => 500,
                'content' => <<<HTML
                    <pre style="overflow:auto; color:red;border:1px solid red;padding:5px;">Unhandled Exit Error [E0.4]:
                    <i>Request: {$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}</i>
                    <b>{$ex}</b>
                    </pre>
                HTML
            ];        
        } else if($response instanceof \SplFileInfo){
            $GLOBALS['--EXIT'] = $response;
        } else if(\is_array($response)) {
            $GLOBALS['--EXIT'] = $response;
        } else if(\is_object($response)) {
            switch($response->type ?? null){
                case 'download':{
                    $GLOBALS['--EXIT'] = $x = (object)[];
                    if(!\is_null($content = $response->content ?? null)){
                        $x->type = $response->type;
                        if(!($response->download_name ?? null)){
                            $download_name = \basename($file);
                        } else if($response->download_name === true){
                            $fname = pathinfo($file, PATHINFO_FILENAME);
                            $download_name = \str_replace('/','-','download-'.date('Y-md-Hi-s')."-{$fname}");
                        } else if(\is_string($response->download_name)){
                            $download_name = $response->download_name;
                        }
                        $x->headers[] = "Content-Type: application/octet-stream";
                        $x->headers[] = "Content-Transfer-Encoding: Binary";
                        $x->headers[] = "Content-disposition: attachment; filename=\"".$download_name."\"";
                        if($content instanceof \SplFileInfo){
                            $x->headers[] = "Content-length: ".(string)(filesize($content));
                        } else if(\is_string($content) && \is_file($content)){
                            $x->content = \_\f::_($content);
                            $x->headers[] = "Content-length: ".(string)(filesize($content));
                        } else if(\is_scalar($content)) {
                            $x->type = 'download-string';
                            $x->content = (string) $content;
                            $x->headers[] = "Content-length: ".\strlen((string) $content);
                        }
                    } else {
                        $x->content = 'Unable To Download';
                    }
                } break;
                default: {
                    $GLOBALS['--EXIT'] = $response;
                } break;
            }
        } else {
            //* do nothing!
        }
        exit();
    }
    public static function respond_json($content){
        \defined('_\SIG_ABORT') OR \define('_\SIG_ABORT', 0);
        while(\ob_get_level() > \_\OB_OUT){ @\ob_end_clean(); }
        \header('Content-Type: application/json');
        exit(\json_encode($content ?? [],JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
    
    public static function respond_form_data($data){
        $form_values = [];
        if($f = $data['form_values'] ?? null){
            foreach($f as $k => $v){
                foreach(\_\i\assoc::flatten($v,'][', "{$k}[", ']') as $k1 => $v1){
                    $form_values[$k1] = $v1;
                }
            }
        }
        $data['form_values'] = $form_values;
        return static::respond_json($data);
    }
    
}