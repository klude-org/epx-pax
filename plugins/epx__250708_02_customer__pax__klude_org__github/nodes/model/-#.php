<?php namespace epx__250708_02_customer__pax__klude_org__github\nodes ;

class model extends \_\env\com\db_model\nodes\model {
    
    protected function i__struct(){
        return [
            "name" => ["TEXT NOT NULL", "is-unique" => true ],
            "client_associate_id" => ["BIGINT NULL", 'link' => ["client_associate","id"]],
            "open" => "BOOLEAN NOT NULL DEFAULT '1'",
            "acn" => "TEXT NULL",
            "company_name" => "TEXT NULL",
            "abn" => "TEXT NULL",
            "trust_name" => "TEXT NULL",
            "trading_name" => "TEXT NULL",
            "contact_given_name" => "TEXT NULL",
            "contact_family_name" => "TEXT NULL",
            "contact_phone" => "TEXT NULL",
            "contact_email" => "TEXT NULL",
            "address_line_0" => "TEXT NULL",
            "address_line_1" => "TEXT NULL",
            "address_line_2" => "TEXT NULL",
        ];    
    }
    
    public function get($id){
        return $this[$id];
    }
    
}