<?php

namespace core\render\dbms\mysql;


class ddl extends \core\root
{

	protected $datatype = [
		'string' => ['char', 'varchar', 'binary', 'varbinary', 'tinyblob','tinytext', 'blob','text', 'mediumblob', 'mediumtext', 'longblob', 'longtext', 'enum', 'set'],
		'date' => ['date', 'datetime', 'timestamp', 'time', 'year'],
		'numeric' => ['tinyint', 'bit', 'bool', 'boolean','smallint','mediumint', 'int','integer','bigint','decimal','dec','numeric','fixed', 'float','double', 'double precision', 'real']
	];

	protected $config = [
		'act' => 'CREATE', 			// CREATE | ALTER | DROP | RENAME | TRUNCATE
		'object' => 'DATABASE',		// TABLE | DATABASE | SCHEMA | VIEW | EVENT | etc ...
		'name' => 'test',			// Object name
		'exists' => false,			// is true, IF NOT EXISTS
		'temp' => false,			// is true, TEMPORARY object
		'charset' => null,
		'collate' => null,
	];
	
	function compile($strctr = [], $config = [])
	{

		$ddl = null;
		$object = null;

		switch(true) {
			case $strctr instanceof \core\dbms\mysql\database:
				$object = 'DATABASE';
				break;
			case $strctr instanceof \core\dbms\mysql\event:
				$object = 'EVENT';
				break;
			case $strctr instanceof \core\dbms\mysql\table:
				$object = 'TABLE';
				break;
			case $strctr instanceof \core\dbms\mysql\view:
				$object = 'VIEW';
				break;
			case $strctr instanceof \core\dbms\mysql\index:
				$object = 'INDEX';
				break;
			case $strctr instanceof \core\dbms\mysql\proc:
				$object = 'PROCEDURE';
				break;
			case $strctr instanceof \core\dbms\mysql\func:
				$object = 'FUNCTION';
				break;
			case $strctr instanceof \core\dbms\mysql\trigger:
				$object = 'TRIGGER'; 
				break;
		}

		if(!isset($config['object'])) return null;

		return $ddl;
	}

	/*
	function set($strctr = [], $config = [])
	{
		parent::set($strctr, $config);
		
		$ddl = '';
		
		switch(strtolower($this->config('object'))) {
			case 'schema':
			case 'database':
				$ddl = $this->database($strctr, $config);
				break;
			case 'table':
				$ddl = $this->table($strctr, $config);
				break;
			case 'index':
			
				break;
		}
		
		return $this->ddl = $ddl;
		
		if($this->strctr) {

			$columns = [];
			$types  = $this->datatype['string'];
			$types += $this->datatype['date'];
			$types += $this->datatype['numeric'];
			
			foreach ($this->strctr as $name => $definition) {
				$column = '';
				if(is_string($definition))
					$column .= ' ' . $definition;
				
				if(is_array($definition)) {

					$type = null;
					
					foreach($definition as $def => $opt) {

						if(is_numeric($def))
							$column .= ' ' . $opt;

						if(is_string($def)) {
						
							if(!$type) {
								$type = in_array(strtolower($def), $types);
							}
						
							$column .= ' ' . $def . ' ' . self::$dbms['mysql']::$pdo->quote($opt);
						
						}
					}

				}

				$columns[] = $column;

			}

			$ddl = "\nCREATE TABLE " . $this->config('name') . " (" . PHP_EOL . "\t";
			$ddl = $ddl . implode(",".PHP_EOL."\t", $columns) . PHP_EOL . ");\n";

		}
		
		return $this->strctr = $ddl;
		
	}
	
	function database($strctr = [], $config = [])
	{
		$ddl = '';

		$act = strtoupper($this->config('act'));
		$object = strtoupper($this->config('object'));
		$name = $this->config('name');

		$ddl = $act . ' ' . $object . ' ' . $name;
		
		if (($charset = $this->config('charset')))
			$ddl .= ' CHARACTER SET ' . $charset . PHP_EOL;
		
		if (($collate = $this->config('collate')))
			$ddl .= ' COLLATE ' . $collate . PHP_EOL;
		
		$ddl .= ';';
		
		return $ddl;
	}
	
	function table($strctr = [], $config = [])
	{}
	*/
}