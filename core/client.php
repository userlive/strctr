<?php

namespace core;

class client extends root /* implements SessionHandlerInterface */{

	static $session = null;

	protected $strctr = [
		'id' => null,
		'type' => 'unknown',
		'created' => 0,
		'host' => '0.0.0.0',
		'agent' => '',
		'expired' => 0
	];

    function __construct($strctr = [], $config = [])
    {

		// ini
		// session.cookie_lifetime=0
		// session.use_cookies=On
		// session.use_only_cookies=On
		// session.use_strict_mode=On
		// session.cookie_httponly=On
		// session.cookie_secure=On
		// session.cookie_samesite="Lax" или session.cookie_samesite="Strict"
		// Разработчик также должен использовать session_regenerate_id() для обеспечения безопасности сессий.
		// header
		// etag
		$config['group'] = ['client', 'user', 'operator', 'administrator', 'developer'];
		$config['auth'] = ['public', 'base', 'custom', 'OAuth', 'OpenID', 'SAML'];
		/**
			$config['access'] — задает доступ скрипта к параметрам чтения, записи, удаления
			любое вычесленное false выражение предоставляет доступ только к чтению объекта

			true в значении предоставит полный доступ к чтению, записи, удаления

			можно перечислить в массиве конкретные или указать строкой только что-то одно

			Дискреционное управление доступом

			$client['group'] = 'client' | 'user' ...; // 32b len
			$client['user'] = 'client'; // 64b len
			$client['auth'] = true; // 1b

			// group client cache session
			$config['cache'] = 'session' | 'file' (tmp) | state | 'memory' | sqlite | mysql
			$strctr['profile'] = [];
		*/

		if(is_string($strctr)) {
			$strctr = ['user' => $strctr];
		}

		if(is_string($config)) {
			$config = ['password' => $config];
		}

		$config['access'] = false; // [NULL | false | 0 | []] = select | read

		$this->auth($strctr, $config);

		if(session_status() !== PHP_SESSION_ACTIVE) {
			session_start();

			if(!$strctr) {
				$strctr = $_SESSION;
			}

		} else
			return;

		setlocale(LC_ALL, $this->_locale());

        parent::__construct($strctr, $config);

    }

	function set($strctr = [], $config = [])
	{
		parent::set($strctr, $config);
	}

	function auth($strctr = [], $config = [])
	{
		// auth base

	}

	function pass($strctr = [], $config = [])
	{

	}

	function http_auth()
	{

	}

	function __destruct()
	{

		$_SESSION = $this->strctr;

		session_write_close();

		parent::__destruct();
	}

	// public close () {}
	// public destroy ( string $session_id ) {}
	// public gc ( int $maxlifetime ) {}
	// public open ( string $save_path , string $session_name ) {}
	// public read ( string $session_id ) {}
	// public write ( string $session_id , string $session_data ) {}

}
