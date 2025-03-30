<?php
/**

default tempalte: ./templates/test.php;

default table: test;

**/

class test extends \core\root {

	function __construct($strctr = [], $config = [])
	{

		$strctr["list"] = 12345;
		$strctr["object"] = __CLASS__;
		$strctr['dop1'] = 'test';
		$strctr['dop2'] = 'test123';
		$strctr['dop3'] = 'test456';
		$strctr['dop4'] = 'test678';

		// default model
		//$strctr['head'] = '';
		$strctr['head']['title'] = '';
		$strctr['head']['script'][] = '';
		$strctr['head']['script'][] = '';
		$strctr['body'] = '';

		// $database =

		parent::__construct($strctr, $config);
	}

	function scum()
	{}

}

?>
