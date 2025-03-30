<?php

namespace core;

class init extends root
{

    function __construct($strctr = [], $config = [])
    {
        $strctr['methods'] = [];
        
        parent::__construct($strctr, $this->config(['route' => DEF_ROUTE, 'output' => DEF_OUTPUT]));
        
        $url = $_SERVER['REQUEST_URI'];

		if($url == '/' || $url == '\\')
			$url = null;

		$this->request($url);

        $this->make();
        $this->method($_SERVER['REQUEST_METHOD']);
		
    }
	
    protected function request($url)
	{

		list($_46, $_47, $_92) = [chr(46), chr(47), chr(92)];
		
		if (!$url)
			$url = $this->config('route');

		$url = ltrim($url, $_47.$_92);
		$url = parse_url($url);
		$url = array_merge(pathinfo($url['path']), $url);

        if(!$url['basename'])
            $url['filename'] = $this->config('route');
		
		if(array_key_exists('dirname', $url)) {
			$url['dirname'] = ltrim($url['dirname'], $_46);

			if($url['dirname']!==$_47 || $url['dirname']!==$_92)
				$url['dirname'] .= DIRECTORY_SEPARATOR;

		} else {
			$url['dirname'] = $_47;
		}

        $class = str_replace($_47, $_92, $url['dirname'].$url['filename']);

        if(!class_exists($class)) {

            do {

                $methods[] = ($method = substr(strrchr($class, $_92),1));
                $class = strstr($class, $_92.$method, true);

            } while($class && !class_exists($class));

            if(!$class) {
                header("HTTP/1.0 404 Not Found");
				echo "Object not found...";
				exit(0);
            }

        }

        if(isset($methods))
            $this->strctr['methods'] = $methods;

        if(isset($url['extension']) && $url['extension'])
            $this->strctr['extension'] = $url['extension'];
        else
            $this->strctr['extension'] = $this->config('output');

        $this->strctr['class'] = $class;

        return $this;

	}
	
	function method($strctr = [], $config = [])
	{
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'CONNECT':
				$this->connect($strctr, $config);
			break;
			case 'DELETE':
				$this->remove($strctr, $config);
			break;
			case 'GET':
				$this->get($strctr, $config);
            break;
			case 'HEAD':
				$this->head($strctr, $config);
			break;
			case 'OPTIONS':
				$this->options($strctr, $config);
			break;
			case 'PATCH':
				$this->patch($strctr, $config);
			break;
			case 'POST':	
				$this->post($strctr, $config);
			break;
			case 'PUT':
				$this->put($strctr, $config);
			break;
			default:
				$this->h(405);
                exit(1);
		}
	}

	function connect()
	{
        die("query connect");
    }

	function remove()
	{
        die("query delete");
    }

	function get($strctr = [], $config = [])
    {
        $this->execute($strctr, $config);
        print($this->strctr['object']);
        return $this;
	}

	function head()
	{
        exit(0);
    }

	function options()
	{
        exit(0);
    }

	function patch()
	{
        // save
    }

	function post()
	{
        //save
        die("query post");
    }

	function put()
	{
        //save
        die("query put");
    }

	function make($strctr = [], $config = [])
	{
		$class = new $this->strctr['class']($strctr, ['output' => $this->strctr['extension']]);
        $this->strctr['object'] = $class;
		return $class;
	}

    function execute($strctr = [], $config = [])
	{

        if($this->strctr['methods']) {
            do {

                if(!method_exists($this->strctr['object'], ($method = array_pop($this->strctr['methods'])))) continue;

                $this->strctr['object']->$method($strctr, $config);

            } while ($this->strctr['methods']);
        }
		
		switch($this->strctr['extension']) {
			case 'json':
				header('Content-type: application/json');
				break;
			case 'htm':
			case 'html':
				header('Content-type: text/html');
				break;
			
		}

    }

	function argv()
	{
		$config = ['route' => null];
		$options = "u:r:";
		$longopts = ['url:', 'uri:', 'route'];
		$argv = getopt($options, $longopts, $path);

		if($argv) {
			foreach($argv as $key => $val) {
				switch($key) {
					case 'u':
					case 'r':
					case 'uri':
					case 'url':
					case 'route':
						if($val)
							$config['route'] = $val;
						break;
				}
			}
		}

		return $config;
	}

	function h($h, $v = '')
	{
		if($this->is_cli()) return;

		if (is_numeric($h) && isset($this->state[$h])) {
			return http_response_code($h);
		}

		return header($h . ": " . $v);

	}

    function __destruct()
    {
        
    }

}