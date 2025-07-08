<?php namespace __;

final class customer extends \epx__component__pax__klude_org__github {
    
    public function __construct(){
        $this->NAV = [
            
        ];
        
        
        // https://fw.local/web-github/klude-org/epx-pax/sandbox-02/--ops/customer/@25/editor
        // _/customer/xui/editor/-@.php
        // https://fw.local/web-github/klude-org/epx-pax/sandbox-02/--ops/customer/@25/editor/tab/main
        // _/customer/xui/editor/-@.php
        
    } 
    
    protected function i__db_fields(){
        $this->sql_field('name','');
        $this->sql_field('name','');
        $this->sql_field('address_id','')->link('addresses__rec');
        $this->sql_field('product_id')->link('product__rec');
    }
    
    
    protected function i__views($type){
        
        $this->views['xui/listing/table_row'] = function($row){ ?>
            <tr>
                
            </tr>
        <?php }; 
        
        $this->views['xui/listing/item_card'] = function($row){ ?>
            
        <?php };
        
        $this->view['xui/editor/tab'] = function(){
            
        };
        
        $this->views['xui/editor/tab/one'] = function($row){ ?>
            
        <?php };
        
        $this->views['xui/editor/tab/two'] = function($row){ ?>
            
        <?php };
        
    }
    
}