<?php 

namespace core;

class cluster extends \core\root implements \Iterator, \ArrayAccess {
	
	function __construct($strctr = [], $config = [])
	{

        $config['unit'] = '\core\unit';

        $this->status['length'] = 0;
        $this->status['current'] = 0;
        $this->status['previos'] = 0;
        $this->status['next'] = 0;

		parent::__construct($strctr, $config);
	}
	

	function set($strctr = [], $config = [])
	{
		if(!$strctr) return false;

		foreach($strctr as $set) {

            $unit = null;

            if($this->strctr)
                $unit = $this->get($set);

            if ($unit) {
                $unit->set($set);
            } else {
                $unit = $this->prepare($set);
                $this->strctr[$unit->id()] = $unit;
            }

		}

		return $this;

	}

    function get($id = [], $config = [])
    {
        
        if(is_array($id) && array_key_exists($this->idname(), $id))
            $id = $id[$this->idname()];
        
        if(is_numeric($id) || is_string($id))
            return $this->offsetGet($id);

        return null;
    }

	function prepare($strctr = [], $config = [])
	{

		if($this->is_unit($strctr)) {
			$strctr->config('cluster', $this);
			return $strctr;
		}

		$unit = $this->config('unit');
		$unit = new $unit($strctr, ['cluster' => $this]);
		
		if(!$unit->error()) return $unit;
		
		return false;
	}

    public function offsetSet($offset, $value) {
		
		if (is_null($offset)) {
            $this->add($value);
        } else {
			$unit = $this->get($offset);
			
			if($unit) {
				$unit->set($value);
			} else {
                $value[$this->idname()] = $offset;
//				$this->add($value);
			}
		   
        }
    }
	
    function idname()
    {
        return $this->config('unit')::IDNAME;
    }
    
	protected function is_unit($unit) {
		return is_a($unit, $this->config('unit'));
	}

    public function offsetExists($offset) {
		return array_key_exists($offset, $this->strctr);
    }

    public function offsetUnset($offset) {
        unset($this->strctr[$offset]);
    }

    public function offsetGet($offset) {
		return $this->offsetExists($offset) ? $this->strctr[$offset] : null;
    }
	
	public function current(){
		return current($this->strctr);
	}
	
	public function key(){
		return key($this->strctr);
	}
	
	public function next(){
		return next($this->strctr);
	}
	
	public function rewind(){
		return reset($this->strctr);
	}
	
	public function valid(){
		return key($this->strctr) !== null;
	}
	
}

?>