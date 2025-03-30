<?php

class store extends \core\dbms\mysql\database {

    // const DBMS = 'mysql';

    const OUTPUT = 'native';

    //const HOST = 'localhost';
    //const PORT = '3306';
    //const USER = 'root';
    //const PASS = 'qwerty123!';

    function __construct($strctr = [], $config = [])
    {
        $config['state'] = true;
        $config['save'] = true;
        parent::__construct($strctr, $config);

    }

}

?>
