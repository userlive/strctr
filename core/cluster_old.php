<?php

namespace core;



class cluster extends \core\root implements \ArrayAccess, \Iterator
{

	public $config = [
		'unit' => '\core\unit',
		'size' => null,
		'add' =>  true,
		'merge' => true,
		'remove' => true,
		'parse' => false,
	];

	protected $status = [
		'length' => 0,
		'position' => 0
	];

	protected $vector = [];

	function __construct($units=[], $config=[])
	{
		parent::__construct();
		$this->reset($units, $config);
	}

	function reset($units = [], $config = [])
	{
		if($this->status['length']) $this->status['length'] = 0;
		if($this->strctr) $this->strctr = [];
		if($this->vector) $this->vector = [];
		$this->set($units, array_merge($config, ['events' => false]));
	}

	function add($units = [], $config = [])
	{
		return $this->set($units, array_merge($config, ['merge'=>false]));
	}

	function set($units = [], $config = [])
	{
		if(!$units) return null;

		$add = (isset($config['add']) && $config['add']) ? $config['add'] : $this->config['add'];
		$merge = (isset($config['merge']) && $config['merge']) ? $config['merge'] : $this->config['merge'];
		$remove = (isset($config['remove']) && $config['remove']) ? $config['remove'] : $this->config['remove'];
		$parse = (isset($config['parse']) && $config['parse']) ? $config['parse'] : $this->config['parse'];

		foreach($units as $unit) {
			$existing = $this->get($unit);
			if($existing){
				if($merge && $existing != $unit);
			} elseif($add){
				$unit = $this->_prepareUnit($unit);
				$this->_addReference($unit);
			}
		}
	}

	function get($unit=[], $config = [])
	{

		if($this->is($unit) && isset($this->vector[$unit->id()])) return $this->vector[$unit->id()];
		if((is_numeric($unit) || is_string($unit)) && isset($this->vector[$unit])) return $this->vector[$unit];
		if(is_array($unit) && ($id = $this->id($unit)) && isset($this->vector[$id])) return $this->vector[$id];

		return null;
	}
	
	function is($unit)
	{
		return (is_a($unit, $this->config['unit'])) ? true : false;
	}
	
	function id($strctr = []) {
		$prop = get_class_vars($this->config['unit']);
		if(isset($prop['config']['name_id']))
			return isset($strctr[$prop['config']['name_id']])?$strctr[$prop['config']['name_id']]:false;
		else
			return false;
	}
	
    protected function _addReference($unit=[], $options=[])
    {

		// TODO: CLUSTER — событие ALL
        // $unit->on('all', $this->_onModelEvent, $this);
    }

    protected function _prepareUnit($strctr=[], $config = []){

		if($this->is($strctr)){
            $strctr->cluster($this);
            return $strctr;
        }

        $unit = new $this->config['unit']($strctr, array_merge($config, ['cluster' => $this]));
        if(!$unit->error()) return $unit;

        //TODO: "клацнуть" ошибку при подготовке юнита по событию
        //$this->trigger('invalid', $this, $unit->error(), $options);

        return false;

    }
	
/*
	public function get($obj = null, $options = []){
        if($this->_isUnit($obj) && isset($this->strctr[$obj->sid()])) {
            return $this->strctr[$obj->sid()];
        } else {
            if((is_numeric($obj) || is_string($obj)) && isset($this->_hash[$obj]))
                return $this->_hash[$obj];
            if(is_array($obj) && isset($obj['id']) && isset($this->_hash[$obj['id']]))
                return $this->_hash[$obj['id']];
            if(is_object($obj) && isset($obj->id) && isset($this->_hash[$obj->id]))
                return $this->_hash[$obj->id];
        }

        if((is_numeric($obj) || is_string($obj)) && isset($this->strctr[$obj])) return $this->strctr[$obj];

        return null;
    }
*/
	public function offsetExists ($offset)
	{}

	public function offsetGet ($offset)
	{}

	public function offsetSet ($offset, $value)
	{
		
	}

	public function offsetUnset ($offset)
	{}
		
	public function current ()
	{}

	public function key ()
	{}

	public function next ()
	{}

	public function rewind ()
	{}

	public function valid ()
	{}
	
}
 
class _cluster extends \core\root {

    protected $unit = '\core\unit';

    public $length;         // Кол-во элементов в списке

    public $even;           // true если последний элемент к которому обращались четный в списке, иначе false

    public $last;           // индекс последнего элемента

    public $first;          // индекс первого элемента

    public $next;           // Индекс следующего за текущим

    public $prev;           // Индекс предудущего

    protected $units  = [];

    protected $_hash = [];

    function __construct($units=[], $options=[]){
		
        parent::__construct($units, $options);

        isset($options['unit']) ? $this->unit = $options['unit']:null;
        isset($options['comparator']) ? $this->comparator = $options['comparator']:null;

        if($units) $this->reset($units, $options);
    }

    /**
     * Добавляет нового юнита, если идентификаторы структур совпадают,
     * то существующий юнит в кластере будет заменен
     *
     * @param array $units
     * @param array $options
     */

    public function add($units = [], $options = []){
        $this->set($units, array_merge($options, ['merge' => false]));
    }

    /**
     * Обновление списка юнитов кластера, по правилам:
     * 1. Отсутствующие модели в списке будут удалены из кластера
     * 2. Структура юнита из списка будет объединена с структурой юнита в кластере
     * 3. Отсутствующие юниты в кластере будут добавлены из списка
     *
     * @param $units    mixed  — список юнитов
     * @param $options  array  — опции
     */

    public function set($units = [], $options = []){
        if(!$units) return null;

        // TODO: При создании кластера и передачи списка готовых юнитов
        if(($this->_options['parse'] || (isset($options['parse']) && $options['parse'])) && !$this->_isUnit($units, $options)) {
            $units = $this->parse($units, $options);
        }

        // Выясняем сколько объектов передано
        ($singular = !is_array(current($units)))?$units = [$units]:null;

        // Опция как бэ говорит, что объекты будут добавлены. По умолчанию true
        ($this->_options['add'] || isset($options['add']) && $options['add'])?$add=true:$add=false;

        // Опция как бэ говорит, что данные в существующем будут объеденены с данными из переданного. По умолчанию true
        ($this->_options['merge'] || isset($options['merge']) && $options['merge'])?$merge=true:$merge=false;

        // Опция как бэ говорит, что данные будут удалены из кластера если их не будет в переданном массиве. По умолчанию true
        ($this->_options['remove'] || isset($options['remove']) && $options['remove'])?$remove=true:$remove=false;

        // Данные будут преобразованы
        ($this->_options['parse'] || isset($options['parse']) && $options['parse'])?$parse=true:$parse=false;

        $map = []; // Карта записанных и измененных юнитов

        //$set = []; // Юниты данные которых были объеденены
        //$add = []; // Юниты которые были добавлены
        //$rem = []; // Юниты которые будут удалены из кластера

        foreach($units as $unit){
            $existing = $this->get($unit, $options);
            if($existing) {
                if($merge && $existing != $unit) {
                    $strctr = $this->_isUnit($unit)?$unit->structure():$unit;
                    if($parse) $strctr = $existing->parse($strctr, $options);
                    $existing->set($strctr, $options);
                }
                $set[] = $existing->sid();
            }
            // TODO: сформировать хеш массив с внешними идентификаторами
            if($add){
                $unit = $this->_prepareUnit($unit, $options);
                if($unit) $this->_addReference($unit, $options);
            }
        }
    }

    /**
     * Вернет юнита по его ID, SID или по переданному экземпляру
     *
     * @param array $unit
     * @param array $options
     */

    public function get($obj = null, $options = []){
        if($this->_isUnit($obj) && isset($this->strctr[$obj->sid()])) {
            return $this->strctr[$obj->sid()];
        } else {
            if((is_numeric($obj) || is_string($obj)) && isset($this->_hash[$obj]))
                return $this->_hash[$obj];
            if(is_array($obj) && isset($obj['id']) && isset($this->_hash[$obj['id']]))
                return $this->_hash[$obj['id']];
            if(is_object($obj) && isset($obj->id) && isset($this->_hash[$obj->id]))
                return $this->_hash[$obj->id];
        }

        if((is_numeric($obj) || is_string($obj)) && isset($this->strctr[$obj])) return $this->strctr[$obj];

        return null;
    }

    /**
     * Перезагружает кластер, полностью заменяя содержимое
     *
     * @param
     */

    function reset($units = [], $options = []){
        $this->_reset();
        $units = $this->add($units, array_merge($options, ['events' => false]));
    }

    /**
     * Возвращает идентификатор структуры
     */

    public function unitSid(\core\unit $unit){
        return $unit->sid();
    }

    /**
     * Возвращает идентификатор юнита
     *
     * @param null $unit
     *
     * @return mixed
     */

    public function unitId($unit=null){
        if(is_array($unit) && isset($unit['id'])) {
            return $unit['id'];
        }

        if(is_object($unit) && isset($unit->id)){
            return $unit->id;
        }

        if($this->_isUnit($unit)){

        }

        return null;
    }

    protected function _addReference($unit=[], $options=[]) {

        if($unit->id()) {
            $this->_hash[$unit->id()] = $unit;
            $this->strctr[$unit->sid()] = $unit;
        } else
            $this->strctr[$unit->sid()] = $unit;

        // TODO: CLUSTER — событие ALL
        //$unit->on('all', $this->_onModelEvent, $this);
    }

    protected function _prepareUnit($strctr=[], $options){
        if($this->_isUnit($strctr)){
            if(!$strctr->cluster()) $strctr->cluster($this);
            return $strctr;
        }
		
        $unit = new $this->unit($strctr, array_merge($options, ['cluster' => $this]));
        if(!$unit->error()) return $unit;
        //TODO: "клацнуть" ошибку при подготовке юнита по событию
        //$this->trigger('invalid', $this, $unit->error(), $options);
        return false;
    }

    protected function _reset(){
        $this->length = 0;
        $this->strctr = [];
        $this->_hash = [];
    }

    protected function _isUnit($unit, $options=[]){
        if($unit instanceof $this->unit)
            return true;
        else
            return false;
    }

    public function offsetSet($offset, $value) {
        $this->current = $offset;
        $this->set($value);
    }

    public function offsetGet($offset) {
        $this->current = $offset;
        return $this->get($offset);
    }

}
