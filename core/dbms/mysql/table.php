<?php
/**

	1. Поиск таблицы по имени, если имени нет, то можно попрбовать найти по названию класса
	2. Состояние таблицы хранится вместе с DDL CREATE
	3. 

*/

namespace core\dbms\mysql;


class table extends \core\dbms\mysql
{
    
    protected $schema = [];
    
    function __construct($strctr = [], $config = [])
    {
        
        if (!isset($config['table']));
            $config['table'] = static::TABLE ?? $this->alias();
        
        parent::__construct($strctr, $config);

    }
    
    function set($strctr = [], $config = [])
    {
        
    }
    
    function explore($strctr = [], $config = [])
    {

        $config = $this->config($config);

        $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $config['table'] . "'; ";

        $pdo = static::$pdo[$config['connect']];
        $columns = $pdo->query($query);
        
        if($columns) {
            foreach($columns->fetchAll(\PDO::FETCH_ASSOC) as $column) {
                $this->schema[$column['COLUMN_NAME']] = [
                    'type' => $column['DATA_TYPE'],
                    'default' => $column['COLUMN_DEFAULT'],
                    'comment' => $column['COLUMN_COMMENT'],
                    'privileges' => $column['PRIVILEGES']
                ];
                
            }
        }
        
    }
    
    function create($strctr = [], $config = [])
    {
        
        $config = $this->config($config);

        $this->schema;
        
        $query = "INSERT ";
        
    }

    function read($strctr = [], $config = [])
    {
        
        $config = $this->config($config);
        
        $this->schema;
        
        $query = "SELECT ";
        
    }

    function update($strctr = [], $config = [])
    {
        $query = "UPDATE ";
    }

    function delete($strctr = [], $config = [])
    {
        $query = "DELETE ";
    }

}