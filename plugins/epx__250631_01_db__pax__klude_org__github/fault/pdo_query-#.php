<?php namespace epx__250631_01_db__pax__klude_org__github\fault;

class pdo_query extends \_\fault\exception {
    
    public function __toString() {
        $m = $this->getMessage().":\n";
        if($previous = $this->getPrevious()){
            $m .= $previous->getMessage()."\n";
        }
        if($q = $this->details['sql'] ?? ''){
            $m .= "<u>Query</u>:\n";
            $m .= \epx__250631_01_db__pax__klude_org__github\sql\formatter\html::format($q)."\n";    
        }
        $m .= "<u>Exception(s)</u>:\n".parent::__toString();
        return $m;
    }
    
}