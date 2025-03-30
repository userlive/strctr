<?php

namespace core\element;
/**
	ТАБЛИЦА registry (СПРАВОЧНИК)

	Все строки таблицы это объект. Все объекты типизированы. тип объекта является наивысшей точкой
	иерархии в справочнике с типом 0 и не имеет родителей. По умолчанию в таблице содержится 2 типа объектов:
	category, property

	

 Справочник типов/категорий/параметров/свойств
	— Допустимо держать данные в одной таблице
	— MyISAM 
	— Древовидная струтура в таблице (id|parent)
**/

class registry extends \core\dbms\mysql {

    const DATABASE = null;      // default: static::DATABASE || $config['database'] || __CLASSNAME__
    const TABLE = null;         // default: static::TABLE || $config['table'] || __CLASSNAME__
    const ENGINE = 'MYISAM'     // default: InnoDB

    protected $strctr = [
        'id' => null,
        'parent' => null,
        'name' => null,
        'value' => null,
        'type' => null,
    ];

    function __construct($strctr = [], $config = [])
    {
        
    }

}

?>