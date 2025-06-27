<?php namespace epx__neo_ui__pax__klude_org__github\web;

class view {
    
    use \_\i\singleton__t;
    
    private $I__PLICS = [];
    
    public function plugin($expr, $attribs = []){
        $type = '';
        $url = '';
        if(!$expr){
            throw new \Exception("Invalid Argument: empty value for the \$expr");
        }
        if(\is_string($expr)){
            if($expr[0] == '@'){
                $region = \strtok($expr, '/');
                $path = \strtok('');
                throw new \Exception("Plugin resolve by anchor expressions are not supported");
            } else if(
                \str_starts_with($expr,'-asset/')
                || \str_ends_with(pathinfo($expr,PATHINFO_FILENAME),'-asset')
            ){
                $url = o()->ui->lib_url.'/'.$expr;
                $type = \pathinfo($url, PATHINFO_EXTENSION);
                if(\is_file($f = \_\LIB_DIR.'/'.$expr)){
                    $url .= '?i='.(filemtime($f));
                }
            } else if(\str_starts_with($expr,'/')){
                throw new \Exception("Plugin resolve by file is not Implemented");
            } else if(\str_starts_with($expr,'https://')){
                $url = $expr;
                $type = \pathinfo($url, PATHINFO_EXTENSION);
            } else if(\str_starts_with($expr,'http://')){
                $url = $expr;
                $type = \pathinfo($url, PATHINFO_EXTENSION);
            } else {
                throw new \Exception("Invalid plugin expression");
            }
        } else if($expr instanceof \epx__neo_ui__pax__klude_org__github\web\plugin_pack\__i) {
            throw new \Exception("Plugin pack is not Implemented");
        } else if($expr instanceof \SplFileInfo) {
            throw new \Exception("Plugin resolve by file is not Implemented");
        } else if(\is_array($expr)){
            throw new \Exception("Plugin array is not implemented");
        }
        
        $a = '';
        if($attribs){
            foreach($attribs as $k => $v){
                if($v){
                    $a.=' '.$k.'="'.\htmlspecialchars($v).'"';
                }
            }
        }        
        
        if($url){
            if($type){
                switch($type){
                    case 'js':
                    case 'text/javascript':
                    case 'script': {
                        $this->plic__set(
                            ($is_pre ?? false) ? 'head_plugins' : 'tail_plugins',
                            "\t<script src=\"{$url}\" onerror=\"xui?.debug?.plugin_load_error('script',this,event)\"{$a}></script>\n"
                        );
                    } break;
                    case 'css':
                    case 'text/css':
                    case 'style': {
                        $this->plic__set(
                            'head_plugins',
                            "\t<link href=\"{$url}\" rel=\"stylesheet\" onerror=\"xui?.debug?.plugin_load_error('style',this,event)\"{$a}>\n"
                        );
                    } break;
                    case 'ico':
                    case 'icon':{
                        $this->plic__set(
                            'head',
                            "\t<link href=\"{$url}\" rel=\"icon\" onerror=\"xui?.debug?.plugin_load_error('icon',this,event)\"{$a}>\n"
                        );
                    } break;
                    default: 
                    $this->plic__set(
                        'head_plugins',
                            "\t<link href=\"{$url}\" onerror=\"xui?.debug?.plugin_load_error('style',this,event)\"{$a}>\n"
                        );
                    break;
                }
            } else {
                $this->plic__set(
                    'head_plugins', 
                    "\t<link href=\"{$url}\" onerror=\"xui?.debug?.plugin_load_error('style',this,event)\"{$a}>\n"
                );
            }
        } else {
            //\_\dx::report(__METHOD__.": Unable to locate plugin '{$expr}'");
            throw new \Exception("Unable to locate plugin '{$expr}'");
        }
    }
    
    public function plic__set($k, $v){
        //* plic__set processes information immediately
        isset($this->I__PLICS[$k]) OR $this->I__PLICS[$k] = '';
        $this->I__PLICS[$k] .= (\is_string($v)) ? $v : \_\texate($v);
    }
        
    public function prt($k){
        echo $this->I__PLICS[$k] ?? '';
    }
    

    
}