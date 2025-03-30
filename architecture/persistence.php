<?php

namespace architecture;

class persistence extends \core\output\native {
	
	const DBMS = 'mysql';
	
	function __construct($strctr = [], $config = [])
	{
														// after
		parent::__construct($strctr, $config) 			// inizialize
		// parent hardcoding:							// before
        // $config['state'] = false;
        // $config['sample'] = '.templates';

	}
	
	function sql($strctr = [], $config = [])
	{
		$config = $this->config($config);
		$table = $config['table'];
		$base = $this->dbms();
		$table = $base->get($table, $config);							// get list

	}
	
	function csv($strctr = [], $config = [])
	{}
	
	function docx($strctr = [], $config = [])
	{}
	
	function xlsx($strctr = [], $config = [])
	{}
	
	function pdf($strctr = [], $config = [])
	{}
	
	function attach($strctr = [], $config = [])
	{}
	
}

?>