<?php
//test
namespace core;

class root implements \JsonSerializable, \ArrayAccess
{
                                    # ядро, выполняет сложные задачи на стороне сервера (кеш, базы данных, шаблоны)
    protected static $client;      // Информация о клиенте: сесия, авторизация
    protected static $events;      // Управление событиями во время исполнения
    protected static $memory;      // Кэширование: compile, state (file),
    protected static $output;      // шаблоны static::$output['native']->render($strctr, $config);
	protected static $dbms;        // базы данных static::$dbms['mysql']['database']['table']

    protected $allowed = [];       // Разрешенные к изменению ключи кофигурации для GET параметров
    protected $required = [];      // Обязательные элементы структуры к заполнению

    /**
        Константы используются в местах кода, где конфигурация не была определена по умолчанию, либо,
        где уровень безопасности ее содержимого должен быть под контролем только разработчика.
    */
                                    # Константы переопределяются дочерними объектами
    const FOLDER = '.strctr';      // директория побочного кода (STATE, логи, настройки...)
    const OUTPUT = false;          // система вывода данных: native (html, json, xml, eny), eny
    const MEMORY = false;          // система кеширования (compile, state, memory)
    const LOCALE = 'ru_RU.UTF-8';  // Локаль по умолчанию
    const ENCODE = 'utf8';         // Кодировка по умолчанию
    const ACCESS = true;           // система разграничения доступа (true — public | false - private)
	const TESTER = false;		   // система тестирования
	const DBMS = false;            // система хранения данных
    const SYSLOG = false;          // логи
    const DEBUG = false;           // система отладки
    const CONNECT = false;

    /*
        Конфигурация важное свойство любого объекта, ее контроль осуществляется на уровне исполнения
        скрипта. Не стоить пологаться на безопасность этого свойства, т.к. конфигурацию объекта может изменить
        любой метод, предполагается что клиент может изменять конфигурацию объектов, но контроль за этим
        должен осуществлять разработчик проверяя переданную информацию.
    */

	protected $config = [           # Переопределяется при создании объекта вторым аргументом
        'output' => 'html',        // Формат вывода по умолчанию. допустимо: html, csv, json, xml, txt
        'events' => false,         // Реагировать на события (add, refresh, get, )
        'upload' => null,          // Папка в которую будут загруженны файлы из _POST
        'fetch' => false,          // При создании экземпляра получит данные
        'cache' => false,          // Будет кешировать вывод
        'state' => false,          // Хранить состояние объекта
		'save' => false,	       // если true — объект будет сохранен в указанной системе хранения данных
        'expire' => false,         // Время в секундах.
		'template' => null		   // путь до файла шаблона
    ];

    /*
        $strctr (сокр. от structure) — свойство содержащее в себе иерархию данных, каждый экземпляр root
        это гипотетический объект, который можно абстрагировать например до моделей в таких паттернах как MVC,
        MVVM, MVP или столбцы таблицы базы данных, либо файла cvs либо любой другой персистентности.

        Способ обращения к объекту вынесен на уровень массива и может быть иерархичным, например:

        $object = new \core\root(['content' => 'text', 'id' => 1], ['template' => 'index.php'])
        echo $object['content'];

        Если к объекту обратиться как к строке, то будет выведен шаблон + данные:

        echo $object;

        В момент освобождения последней ссылки на объект, будет исполнен метод __destruct(),
        который сбросит данные в буффер вывода, если это определено конфигурацией.

        Это свойство не безопасно, т.к. его ключи могут быть модифицированны клиентом. Контроль
        содержимого должен осуществляться разработчиком.
    */

    protected $strctr = [];         # Переопределяется при создании объекта первым аргументом

    /*
        Свойство $status отражает текущее состояние объекта, это полезные данные для разработчика,
        например статус может указать на ошибку в системе или отследить изменение свойств объекта.
    */

    protected $status = [           # Меняется только при исполнении методов
        'change' => NULL,         // time or true — изменен
        'update' => NULL,         // time or true — обновлен
        'create' => NULL,         // time or true — создан в какой либо персистентности, не считая состояния или кеширования
        'parse' => NULL,          // time or true — данные были обработаны
        'state' => NULL,          // time or true — загружено раннее сохраненное состояние конфигурации
        'error' => NULL,          // time or true — при обработке данных обнаружены ошибки ['code' => 000000, 'cart' => time(), 'desc' => 'empty', 'sign' => 'key']
        'hash' => NULL,
        'session' => NULL
    ];

    /*
        свойство $reference, возможно будет проигнорировано в разработке
    */

    protected $reference = [];

    function __construct($strctr = [], $config = [])
    {

        if(session_status() !== PHP_SESSION_ACTIVE)
            static::$client = new client($strctr, $config);

        $this->init($strctr, $config);

        // todo client
        if (defined('static::DBMS') && static::DBMS && !array_key_exists(static::DBMS, static::$dbms)) {

            $dbms = '\core\dbms\\' . static::DBMS;

            if(!isset(static::$dbms[static::DBMS])) {
                static::$dbms[static::DBMS] = new $dbms ($strctr, $config);
            }

        }

        $this->reference();

        if ($strctr)
            $this->set($strctr, $config);

    }

    function status($status = [], $config = [])
    {
        return $this->status;
    }

    function init($strctr = [], $config = [])
    {

        $folder = static::FOLDER ?? sys_get_temp_dir();

        if(isset($this->status['folder']))
            $folder = $folder . DIRECTORY_SEPARATOR . $this->status['folder'];

        $alias = $this->alias(true);

        $this->status['folder'] = dirname($folder . DIRECTORY_SEPARATOR . $alias);

        if(!isset($this->status['alias']))
            $this->status['alias'] = basename($alias);

        $this->status['file'] = $this->status['folder'] . DIRECTORY_SEPARATOR . $this->status['alias'] . '.php';

        $config = $this->config = $this->config($config);

        if($config['state'])
            $this->getstate();


        // 4. Загрузить данные из файла

        // 5. Загрузить данные из ОЗУ
        // 6. Загрузить данные из базы данных
        // 7. Подключить подчиненные структуры
        // 8. Актуализация данных

    }

    function alias($path = false) {

        $alias = get_class($this);
        $alias = str_replace('\\', DIRECTORY_SEPARATOR, get_class($this));

        if ($path)
            return $alias;

        return basename($alias);

    }

    function reference($strctr = [], $config = [])
    {

        if ($strctr)
            $this->reference = array_merge($this->strctr, $strctr);

        if ($this->reference && is_array($this->reference)) {
            foreach ($this->reference as $name => $ref) {

                $id = [];

                if (isset($this->id) && $this->id)
                    $id = $this->id;

                if (isset($this->strctr[$name]) && $this->strctr[$name]) // isset(null) === false
                    $id = $this->strctr[$name];

                $this->strctr[$name] = new $ref(['id' => $id], $this->config);
                unset($this->reference[$name]);
            }
        }

    }

    function set($strctr = [], $config = [])
    {

        if ($strctr && $strctr != $this->strctr) {

            if(!$this->strctr && $strctr)
                $this->status['change'] = time();

            if (is_array($strctr) && is_array($this->strctr)) {
                $this->strctr = array_merge($this->strctr, $strctr);
            } elseif (is_string($strctr) && $config) {
                $this->strctr[$strctr] = $config;
            } else {
                $this->strctr = $strctr;
            }

            return $this->strctr;

        }

        return null;

    }

    function get($strctr = [], $config = [])
    {
        // if !$this->status['create']
        //todo: get memory (MEMORY)

        if (!$strctr)
            return $this->strctr;

        if (is_string($strctr) && array_key_exists($strctr, $this->strctr))
            return $this->strctr[$strctr];

		return null;

    }

	function config($name = null, $value = null)
	{
		static $main = [];

		if (!$main)
			$main = get_class_vars('\core\root')['config'];

        if (is_null($name)) {
			return $this->config;
        }

        if (($arr = is_array($name)) && !$value) {
            return array_merge($main, $this->config, $name);
        }

        if ($arr && $value)
            return $this->config = array_merge($main, $this->config, $name);

        if (($str = is_string($name)) && ($null = is_null($value)))
            return isset($this->config[$name]) ? $this->config[$name] : null;

        if ($str && !$null) {
            $this->config[$name] = $value;
            return $this->config;
        }
	}

    function save($strctr = [], $config = []) {

        $config = $this->config ($config);

    }

    function create ($strctr = [], $config = [])
    {
        $this->status['create'] = time();

    }

    function read ($strctr = [], $config = [])
    {

    }

    function update ($strctr = [], $config = [])
    {
        $this->status['update'] = time();
    }

    function delete ($strctr = [], $config = [])
    {

    }

    function putstate()
    {
        $put = false;

        $state = get_object_vars($this);

        unset($state['strctr']);

        $dir = dirname($this->status['file']);

        if (!file_exists($dir))
            $put = mkdir($dir, 0744, true);

        $state = '<?php return ' . var_export($state, true) . "; ?>";

        $put = file_put_contents($this->status['file'], $state);

        return $put !== false;

    }

    function getstate()
    {
        if(file_exists($this->status['file'])) {
            $state = include($this->status['file']);
            $this->config = $state['config'];
            $this->status = $state['status'];
        }
    }

    protected function _locale($config = [])
    {

        $locale = setlocale(LC_ALL, 0);

        if(defined('self::LOCALE') && self::LOCALE)
            $locale = self::LOCALE;

        if(isset($this->config['locale']) && $this->config['locale'])
            $locale = $this->config['locale'];

        if(isset($config['locale']) && $config['locale'])
            $locale = $config['locale'];

        return $locale;
    }

    static function __set_state($state = [])
    {

        if (count($state['status']) == 1 && $state['status']['object']) {
            return new $state['status']['object']();
        } else {
            return [$state['config'], $state['strctr'], $state['status']];
        }

    }

    public function memory()
    {
        $memory = '\core\memory\\' . static::MEMORY;
        return static::$memory[static::MEMORY] = new $memory($this);
    }

    public function output($strctr = [], $config = [])
    {

        $engine = static::OUTPUT ? static::OUTPUT : 'native';

        $output = '\core\output\\' . $engine;

        if (!isset(static::$output[$engine]) && class_exists($output))
            $output = static::$output[$engine] = new $output();
        else
            $output = static::$output[$engine];

        if (!$strctr)
            $strctr = $this;

		$output = $output->compile($strctr, $this->config($config));

        return $output;

    }

	public function dbms($strctr = [], $config = [])
	{
        if (defined(static::DBMS) && static::DBMS)
            return static::$dbms[static::DBMS];

        return null;
	}

    static function memuse($d = 0, $config = [])
    {
        $b = memory_get_usage() - MEMUSE;

        if (array_key_exists('format', $config) && !$config['format'])
            return $b;

        $p = floor(($b ? log($b) : 0) / log(1024));

        return round($b / (1 << (10 * $p)), $d) . ' ' . ['B', 'KB', 'MB', 'GB', 'TB'][$p];
    }

	protected function is_cli()
	{
		static $sapi = (PHP_SAPI==='cli');
		return $sapi;
	}

    public function jsonSerialize():mixed{
        return $this->strctr;
    }

    function __toString()
    {
        return $this->output($this, $this->config);
    }


    public function cluster($cluster=null)
    {
        if(!$cluster)
            return $this->cluster;
        else
            return $this->cluster = $cluster;
    }

    public function offsetSet($offset, $value): void
    {
		if(is_array($value))
			$this->set($value);
		else
			$this->set($offset, $value);
    }

    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

	public function offsetExists($offset):bool
	{
		return array_key_exists($offset, $this->strctr);
	}

	public function offsetUnset($offset): void
	{
		unset($this->strctr[$offset]);
	}

	function refresh($strctr = [], $config = [])
	{}

	function clean($strctr = [], $config = [])
	{}

    function __destruct()
	{

        $config = $this->config();
        $status = $this->status();

        if($config['state'] && !$this->status['state'])
            $this->putstate();

        if($config['cache']);

        if ($config['save'])
            $this->save();

        $debug = $config['debug'] ?? defined('static::DEBUG') && static::DEBUG;

        if ($debug)
            $this->debug();

            // var_dump(get_class($this));
	}

}
