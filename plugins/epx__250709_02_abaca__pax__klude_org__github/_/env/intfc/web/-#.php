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
        $this->root_url = \_\ROOT_URL;
        $this->site_url = \_\SITE_URL;
        $this->full_url = \rtrim($this->root_url.$this->urp,'/');
        $this->lib_url = $this->site_url."/--epx";
        $this->data_url = $this->site_url."/data";
        $this->data_asset_url = $this->site_url."/data/-asset";
        $this->theme_asset_url = $this->site_url."/--epx/theme/-asset";
        $this->asset_url = $this->site_url."/-asset";
        $this->base_url = \rtrim($this->site_url."/"
            .(
                (\_\REQ['portal'] ?? null ?: '')
                .'.'.(\_\REQ['role'] ?? null ?: '')
            )
            , 
            '/.'
        );
    }
    
}