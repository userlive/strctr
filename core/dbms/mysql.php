<?php

namespace core\dbms;

class mysql extends \core\root
{

    const HOST = null;
    const PORT = null;
    const USER = null;
    const PASS = null;

    const DATABASE = null;
    const TABLE = null;
    const ENGINE = null;

    protected static $pdo = [];

    function __construct($strctr = [], $config = [], $pass = null, $options = [])
    {
        if (!array_key_exists('connect', $config)) {
            if (is_string($config) && $config) {

                $config = ['user' => $config, 'pass' => $pass];
                $pattern = '/([a-z]+)\=([a-zA-Z0-9_\-.]+)/';

                if (strstr($strctr, ':', true)=='mysql' && preg_match_all($pattern, $strctr, $matches)) {

                    $dsn = array_combine($matches[1], $matches[2]);

                    unset($matches);

                    $config['dsn'] = $strctr;
                    $strctr = [];

                    foreach($dsn as $name => $value) {
                        $config[$name] = $value;
                    }

                    unset($dsn);

                } else {
                    echo 'Oops... ' . $strctr . ' — it mysql?';
                    exit;
                }

            } else {
                $config['user'] = $config['user'] ?? static::USER ?? (defined('DEF_MYSQL_USER') ? DEF_MYSQL_USER : NULL);
                $config['host'] = $config['host'] ?? static::HOST ?? (defined('DEF_MYSQL_HOST') ? DEF_MYSQL_HOST : NULL);
                $config['port'] = $config['port'] ?? static::PORT ?? (defined('DEF_MYSQL_PORT') ? DEF_MYSQL_PORT : NULL);
            }
            
            $config['connect'] = $config['connect'] ?? $config['user'] . '@' . $config['host'] . ':' . $config['port'];
        }

        if (!array_key_exists($config['connect'], static::$pdo)) {

            if (array_key_exists('dbname', $config))
                $config['database'] = $config['dbname'];

            if (!array_key_exists('database', $config))
                $config['database'] = static::DATABASE ?? (defined('DEF_MYSQL_BASE') ? DEF_MYSQL_BASE : NULL);// ?? ;

            $config['charset'] = $config['charset'] ?? static::ENCODE;
            $config['pass'] = $pass ?? $config['pass'] ?? static::PASS ?? (defined('DEF_MYSQL_PASS') ? DEF_MYSQL_PASS : null);

        } else {
            $config['table'] = $config['table'] ?? static::TABLE;
        }
        
        parent::__construct($strctr, $config);

        if(isset($config['connect']) && !isset(static::$pdo[$config['connect']]))
            $this->connect($strctr, $config);
        
        if(static::ACCESS)
            unset($this->config['host'], $this->config['port'], $this->config['user'], $this->config['pass']);

        $config = $this->config();

        $this->explore($strctr, $config);
        
    }

    function get($strctr = [], $config = [])
    {
        return parent::get($strctr, $config);
    }

    function set($strctr = [], $config = [])
    {
        parent::set($strctr, $config);
    }

    function save($strctr = [], $config = [])
    {
        return null;
    }

	protected function connect($strctr = [], $config = [])
	{

		if(isset($config['dsn']) && $config['dsn']) {
			$dsn = $config['dsn'];
		} else { 
			$dsn = $this->dsn($strctr, $config);
		}

        $user = $config['user'];
        $pass = $config['pass'];
        $dsn .= "dbname=".$config['database'].";";

		try {

            static::$pdo[$config['connect']] = new \PDO($dsn, $user, $pass);
            static::$pdo[$config['connect']]->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            \core\root::$dbms['mysql'] = $this;

		} catch(\PDOException $e) {
			
            switch($e->getCode()) {
                case 1049: // SQLSTATE[HY000] [1049] Unknown database

                    break;
                case 2002: // SQLSTATE[HY000] [2002] Connection refused
                    
                    break;
                case 2006: // SQLSTATE[HY000] [2006] MySQL server has gone away
                
                    break;
            }
            
            // var_dump(
                // $dsn, $user, $pass,
                // '$errorInfo: ' . $e->errorInfo,
                // 'getCode: ' . $e->getCode(),
                // 'getMessage: ' . $e->getMessage(),
                // 'getFile: ' . $e->getFile(),
                // "getTraceAsString: \n" . $e->getTraceAsString()
            // );
            
            throw $e; // TODO: PDOException 
		}

	}
    
    function explore($strctr = [], $config = [])
    {   // databases

        $config = $this->config($config);
        // $where = ' WHERE ';

        // if(array_key_exists('database', $config) && $config['database'])
            // $where .= " TABLE_SCHEMA = '" . $config['database'] . "' "; 
        // else
            // $where .= " TABLE_SCHEMA <> 'information_schema' ";

        // $query = "SELECT * FROM `information_schema`.`TABLES` " . $where;
        
        $pdo = static::$pdo[$this->config('connect')];
        $query = "SHOW DATABASES;";
        $databases = $pdo->query($query);

        if (($databases = $databases->fetchAll(\PDO::FETCH_ASSOC))) {

            foreach ($databases as &$database) {

                //if (!array_key_exists($table['TABLE_SCHEMA'], $this->strctr))
                //    $this->strctr[$table['TABLE_SCHEMA']] = new \core\dbms\mysql\database([], $config);

                //$this->strctr[$table['TABLE_SCHEMA']]->set($table['TABLE_NAME'], $table + $config);
                //var_dump($table);
            }
            
        }
        
    }

	protected function dsn($strctr = [], $config = [])
	{
		$dsn = 'mysql:';

        $host = $config['host'];
        $port = $config['port'];
        
        if (isset($config['charset']) && $config['charset']) {
            $char = $config['charset'];
        } elseif (defined('DEF_MYSQL_CHAR') && DEF_MYSQL_CHAR) {
            $char = DEF_MYSQL_CHAR;
        } elseif (defined('static::ENCODE') && static::ENCODE) {
            $char = static::ENCODE;
        }

        $dsn .= 'host='.$host.';'; 
        $dsn .= 'port='.$port.';';
        
        if($char)
            $dsn .= 'charset='.$char.';';


		return $dsn;

	}

    function query($strctr = [], $config = [])
    {

        $config = $this->config($config);

        if(is_string($strctr)) {

            $verb = stristr($strctr, ' ', true);
            switch($verb) {
                case 'select':
                        
                        // $query 
                        
                    break;
                case 'insert':
                
                    break;
                case 'delete':
                
                    break;
                case 'update':
                
                    break;
                default:
                    $query = $this->query($strctr);
                    
                    break;
            }
        }
    }
    
    function prepare()
    {
        
    }

	function offsetExists ($offset)
	{
		return array_key_exists($offset, $this->strctr);
	}

	function offsetGet ($offset)
	{
		if(!$this->offsetExists($offset))
			$this->set($offset);

		return $this->get($offset);
	}

	function offsetSet($offset, $value )
	{
		$this->set($offset, $value);
	}
	
	function offsetUnset ($offset){
		unset($this->strctr[$offset]);
	}

}
