<?php namespace epx__250706_01_ui__pax__klude_org__github;

trait request__t {

    private function i__construct_request__t(){
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
    
}