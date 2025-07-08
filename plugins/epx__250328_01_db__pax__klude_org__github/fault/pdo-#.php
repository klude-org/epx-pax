<?php namespace epx__250328_01_db__pax__klude_org__github\fault;

class pdo extends \_\fault\exception {
    
    public function __toString() {
        $m = $this->getMessage()."\n";
        if($q = $this->details['sql'] ?? ''){
            $m .= "Sql: ".\epx__250328_01_db__pax__klude_org__github\sql\formatter\html::format($q)."\n";    
        }
        $m .= "Exception: ".parent::__toString();
        return $m;
    }
    
}