<?php namespace _\env\intfc;

class web extends \_\env {
    
    use \_\i\instance__t;
    
    protected function __construct(){
        parent::__construct();
    }
    
    protected function i__init(){
        # ----------------------------------------------------------------------
        $this->scheme = ($_SERVER["REQUEST_SCHEME"] ?? ((\strtolower(($_SERVER['HTTPS'] ?? 'off') ?: 'off') === 'off') ? 'http' : 'https'));
        $this->host = $_SERVER["HTTP_HOST"];
        $this->root_url = $this->scheme.'://'.$this->host;
        $this->full_url = \rtrim($this->root_url.$this->urp,'/');
        $this->site_url = \rtrim($this->root_url.$this->site_urp,'/');
        $this->lib_url = $this->site_url."/--epx";
        $this->data_url = $this->site_url."/data";
        $this->data_asset_url = $this->site_url."/data/-asset";
        $this->theme_asset_url = $this->site_url."/--epx/theme/-asset";
        $this->asset_url = $this->site_url."/-asset";
        $this->base_url = \rtrim($this->site_url."/"
            .(
                ($this->parsed['portal'] ?? null ?: '')
                .'.'.($this->parsed['role'] ?? null ?: '')
            )
            , 
            '/.'
        );
    }
    
    public function route(){
        if(\session_status() == PHP_SESSION_NONE) {
            //* if the primary starter did the session it would have managed the auth
            //* this part will be scipped
            \session_name(\_\KEY); 
            \session_start();
        }
        if($_SESSION['--AUTH']['login_in_progress'] ?? null){
            $this->i__init();
            $this->request;
            $this->session;
            return $this->auth->route();
        } else {
            return parent::route();    
        }
    }
    
}