<?php

namespace core\dbms\mysql;


class database extends \core\dbms\mysql
{

	function __construct($strctr = [], $config = [])
	{

        // $strctr string — database name
        // $strctr object instanceof \core\dbms\mysql\table
        // $strctr array — table list: names, names + objects
        if (!isset($config['database']));
            $config['database'] = static::DATABASE ?? $this->alias();

		parent::__construct($strctr, $config);
	}

    function explore($strctr = [], $config = []) {

        $config = $this->config($config);

        $query = "SELECT * FROM `information_schema`.`TABLES` WHERE  TABLE_SCHEMA = '" . $config['database'] . "' ";
        
        $pdo = static::$pdo[$config['connect']];
        $tables = $pdo->query($query);

        if (($tables = $tables->fetchAll(\PDO::FETCH_ASSOC))) {

            foreach ($tables as &$table) {

                if(!array_key_exists($table['TABLE_NAME'], $this->strctr)) {

                    $alias = $table['TABLE_SCHEMA'] . '\\' . $table['TABLE_NAME'];
                    $config['table'] = $table['TABLE_NAME'];
                    
                    if (!class_exists($alias)) {
                        class_alias('\core\dbms\mysql\table', $alias);
                        $config['alias'] = $table['TABLE_SCHEMA'] . DIRECTORY_SEPARATOR . $table['TABLE_NAME'];
                    }
                    
                    $this->strctr[$table['TABLE_NAME']] = new $alias($strctr, $config);

                }

            }
            
            unset($config['table']);
            
        }
        
    }
    
	function set($strctr = [], $config = [])
	{
        parent::set($strctr, $config);

        $table = '';
		if(is_string($strctr) && $strctr)    {
            $config = $this->config(['name' => $strctr, 'database' => $this]);
			$this->strctr[$strctr] = new \core\dbms\mysql\table($config, $table);
		}
	}

    function save($strctr = [], $config = [])
    {

        if ($this->status['change']) {

            if ($this->strctr) {

                foreach ($this->strctr as $table) {

                    //$table->save();

                }

            }

        }

    }
    
    function read($strctr = [], $config = [])
    {
        
    }

	function create($strctr = [], $config = [])
	{
        $query = "CREATE DATABASE IF NOT EXISTS " . $this->name() . "; "; // База данных будет создана 
	}

	function update($strctr = [], $config = [])
	{
        $config = $this->config($config);

		$query = "ALTER DATABASE " . $this->name() . " " . $option;
	}

	function delete($strctr = [], $config = [])
	{
		$query = "DROP DATABASE IF EXISTS " . $this->name() . "; "; // Возвращает кол-во удаленных таблиц
                                                                    // Не удаляет временные таблицы
	}

	function name()
	{
		$config = $this->config();
        $status = $this->status;
        return $config['database'] || $status['alias'] || $this->alias();
	}

    function option($option = [])
    {
        
    }

}