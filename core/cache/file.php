<?php

namespace core\cache;

class file extends \core\root 
{
    protected $source;

    function __construct($strctr = [], $config = [])
    {
        
        $this->source = $strctr;
        
        if(is_object($strctr))
            $strctr = get_object_vars($strctr);
        
        parent::__construct($strctr, $config);
    }
    
    function set($strctr = [], $config = [])
    {
        parent::set($strctr, $config);

        if($this->strctr['strctr']) {
            foreach($this->strctr['strctr'] as $key => $val) {

                if(is_a($val, '\core\root') && $val->config['state']) {
                    $this->strctr['strctr'][$key] = $val->status['file'];
                } else {
                    unset($this->strctr['strctr'][$key]);
                }

            }
        }

        //$this->status = $this->strctr['status'];
        //$this->strctr = $this->strctr['strctr'];
        //$this->source = get_class($this->source);

        var_dump(var_export($this->strctr,true));
    }
    
    function save($strctr = [], $config = [])
    {
        
    }
    
    static function __set_state($state = [])
    {
        if($state['strctr']) {
            foreach($state['strctr'] as $key => $strctr) {
                $state['strctr'][$key] = include($strctr);
            }
        }
        
        return new $state['source']($state['strctr']);
        
    }
    
    function __destruct()
    {

        $this->save();

    }
    
}