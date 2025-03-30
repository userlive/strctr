<?php
namespace core;

class unit extends \core\root
{
	
	const IDNAME = 'id';
	static protected $index = 0;
    protected $cluster;
    protected $id;

	protected $config = [
		'validate' => false
	];

	protected $persistence = [
		'origin' => [],
		'previes' => [],
		'changed' => []
	];

	protected $status = [
		'changes' => false,
		'parse' => false,
		'error' => false,
		'event' => false,
		'new' => true
	];

	protected $defaults = [];

	function defaults($strctr = [], $config = [])
	{
		var_dump($this);
	}

    function __construct($strctr=[], $config=[])
    {
		$this->id = $config['index'] = --static::$index;

        if(array_key_exists('cluster', $config)) {
            $this->cluster = $config['cluster'];
            unset($config['cluster']);
        }

		parent::__construct($strctr, $config);
    }

    function get($strctr = [], $config = [])
    {
        return parent::get($strctr, $config);
    }

    public function set($key=null, $val=null, $config=[])
    {

        if(!$key) return $this;

        if (is_string($key))
            $strctr[$key] = $val;

        if (is_integer($key));
            //$strctr = $this->fetch($key);

        if (is_array($key)) {
            $strctr = $key;
            $config = $config ? $config : $val;
        }

        if (is_object($key) && $key instanceof \core\root) {
            $this->strctr = $key;
            return $this;
        }

        // Проверяем данные
        if(!$this->_validate($strctr, $config)) return false;
        // Сохраняем оригинал структуры
        if ($this->strctr && !$this->persistence['origin']) $this->persistence['origin'] = $this->strctr;
        // Присваиваем идентификатор
        if (isset($strctr[$this::IDNAME])) {
            $this->id = $strctr[$this::IDNAME];
            $this->status['new'] = false;
        }

        if ($strctr) {
            $changed = array_diff_assoc($strctr, $this->strctr);
            if ($changed) {
                if($this->strctr) {
                    $this->persistence['changed'] = array_merge($this->persistence['changed'], $changed);
                    $this->status['changes'] = true;
                }
                foreach ($changed as $name => $value) {
                    if (isset($this->strctr[$name])) {
                        $this->persistence['previes'][$name] = $this->strctr[$name];//$this[$name];
                    }
                    $this->strctr[$name] = $value;
                }
            }
        }
/*  TODO прозвон событий при методе сет
        if($changed) {
            foreach ($changed as $element) {
                $this->_trigger('change:' . $element, $this, $config);
            }
            $this->_trigger('change', $this, $config);
        }
*/
        if(isset($config['cluster']))
            $this->cluster = $config['cluster'];
            
        return $this;

    }

    public function structure()
    {
        return $this->strctr;
    }

    protected function validate($strctr = [], $config = [])
    {
        return null;
    }

    protected function _validate($strctr=[], $config=[])
    {
        if((isset($config['validate']) && $config['validate'] === true) || $this->config['validate'] === true) {
            $this->status['error'] = $this->validate($strctr, $config);
            if(is_null($this->status['error'])){
                return true;
            } else {
                $this->_trigger('invalid', $this, $config);
                return false;
            }

        } else return true;

    }

    public function error()
    {
        return $this->status['error'];
    }

    public function changes()
    {
        return $this->status['changes'];
    }

    public function changed()
    {
        return $this->persistence['changed'];
    }

    public function origin()
    {
        return $this->persistence['origin'];
    }

    public function previus()
    {
        return $this->persistence['previes'];
    }

	public function persistence()
	{
		return $this->persistence;
	}
	 
    protected function _eventChange($name=null)
    {
        return $this->status['event'] = (bool) $this->event = $name;
    }

    public function id()
    {
        return $this->id;
    }

    public function isNew()
    {
        return $this->status['new'];
    }

    public function cluster($cluster=null)
    {
        if(!$cluster)
            return $this->cluster;
        else
            return $this->cluster = $cluster;
    }

}