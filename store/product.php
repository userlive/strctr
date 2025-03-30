<?php

namespace store;

class product extends \core\unit {

    protected $strctr = [
        'id' => null,
        'name' => 'New product',
        'brand' => 0,
        'sku' => null,
        'description' => '',
        'set' => 0,
        'count_sale' => 0,
        'count_property' => 0,
        'count_stock' => 0,
        'count_file' => 0,
    ];
    
    protected $reference = [
        'stock' => '\store\stock',
        'property' => '\store\product\property',
        'brand' => '\store\product\brand',
        'file' => '\store\file'
    ];
    
    function __construct($product = [], $config = [])
    {
        parent::__construct($product, $config);
    }
    
}

?>