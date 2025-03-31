<?php
$executionStartTime = microtime(true);
define('MEMUSE', 0);

spl_autoload_extensions();
spl_autoload_register();

define('DEF_ROUTE', '/test.html');
define('DEF_OUTPUT', 'html');
define('DEF_DEBUG', 0);

define('DEF_MYSQL_USER', 'root');
define('DEF_MYSQL_PASS', '');
define('DEF_MYSQL_BASE', 'test');
define('DEF_MYSQL_HOST', '127.0.0.1');
define('DEF_MYSQL_PORT', '3306');

new core\init();

$executionEndTime = microtime(true);
 
//The result will be in seconds and milliseconds.
$seconds = $executionEndTime - $executionStartTime;
 
//Print it out
echo "\nThis script took $seconds to execute.\n";
?>