<?php

namespace store;

class client extends \core\dbms\mysql\table {
    
    function __construct($strctr = [], $config = []) {
        
        parent::__construct($strctr, $config);
        
    }
    
}

?>