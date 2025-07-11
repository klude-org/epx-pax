<?php namespace epx__250710_01_studio__pax__klude_org__github;

class console extends \stdClass {
    
    use \_\env\com\node__t;
    
    public function nav_tree(){
        return [];
    }
    
    public function sidebar(){
        return $this->sidebar ?? $this->sidebar = (function(){ 
            if($file = \_::file($this->COM::class.'/sidebar-$.json')){
                return \json_decode(\file_get_contents($file));
            } else {
                return (object)['nav' => $this->nav_tree()];
            }
        })();
    }
    
}