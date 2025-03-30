<?php

namespace core\output;


class native extends \core\root {

    const FOLDER = '.template';

    function __construct($strctr = [], $config = [])
    {
        $config['state'] = false;
        $config['sample'] = $config['sample'] ?? static::FOLDER ??  '.template';
        parent::__construct($strctr, $config);

    }

    protected function compile($strctr = [], $config = [])
    {
        $content = null;
        $config = $this->prepare($strctr, $config);

        if (is_a($strctr, '\core\root')){
            $strctr = $strctr->get(null, $config);
        } elseif (is_object($strctr)) {
            $strctr = (array) $strctr;
        }

        if($config['output'])
			$content = $this->{$config['output']}($strctr, $config);

        return $content;

    }

    protected function prepare($strctr = [], $config = [])
    {

		if (isset($config['tpl']) && !$config['tpl']) {
			return $config;
		}

		if (is_a($strctr, '\core\root')) {

			$tpl = $this->config('sample') . DIRECTORY_SEPARATOR . get_class($strctr) . '.php';
			$tpl = str_replace(chr(47), DIRECTORY_SEPARATOR, $tpl);

			if (file_exists($tpl)) {
				$config['tpl'] = $tpl;
				return $config;
			}

		}

        $config['tpl'] = $this->config('sample') . DIRECTORY_SEPARATOR . 'default.php';

        return $config;
    }

    protected function json($strctr = [], $config = [])
    {
        return json_encode($strctr);
    }

    protected function html($strctr = [], $config = [])
    {

        ob_start();

        if($strctr && is_array($strctr))
            extract($strctr, EXTR_OVERWRITE);

        include($config['tpl']);

        return ob_get_clean();

    }

    protected function tpl($strctr = [], $config = [])
    {

    }

}

?>
