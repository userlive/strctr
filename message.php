<?php

class message extends \core\root {
	
	const ACCESS = true;
	
	protected $config = [
		'parse' => true
	];
	
	protected $strctr = 'message';
	
	protected $status = [
		'created' => null,
		'updated' => null,
		'changed' => null,
		'deleted' => null,
		'from' => 0,
		'to' => 0,
	];
	
	function __construct($strctr = [], $config = [])
	{
		parent::__construct($strctr, $config);
	}
	
}

?>