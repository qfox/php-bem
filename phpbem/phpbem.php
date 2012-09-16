<?php
/**
 * Simple BEM implementation
 *
 * @author Alex Yaroshevich <zxqfox@gmail.com>
 * @uses v8js
 * @link https://github.com/zxqfox/php-bem.git
 * @link https://github.com/bem
 * @license  MIT License
 * @todo make support of .bem/level file
 */

// namespace bem;

class bemhtml {

	/** @var array path roots */
	protected $roots;

	/** @var bool */
	protected $techs = array('*.bemhtml.js', '*.priv.js');

	/** @var bool */
	protected $useExtensions = false;

	/** @var array */
	protected $extensions = array();

	/** @var string */
	protected $extensionPattern  = 'bem/_extra_:%(suffix)s'; // what's that?

	/** @var bool ? */
	protected $cacheContext = false;

	/** @var array ? */
	protected $contexts = array();

	//protected $render = 'BEMHTML.apply(%s)';

	protected static $v8js = null;

	/**
	 * Constructor
	 * @param string|array $roots paths to bem blocks (usually just one)
	 * @param array|null $params
	 */
	public function __construct ($roots, $params = null) {
		$this->roots = (array) $roots;
		!empty($params['techs']) && $this->techs = $params['techs'];
		!empty($params['cacheContext']) && $this->cacheContext = $params['cacheContext'];

		if (!empty($params['useExtensions'])) {
			!empty($params['extensionPattern']) && $this->extensionPattern = $params['extensionPattern'];
			// $this->extensions = static::loadExtensions($this->extensionPattern);
		}
	}


	/** @var string */
	protected $pagePath;

	/** @var array */
	protected $extraFiles;

	/**
	 * Set current page to render
	 * @param string $page page to process
	 * @param array $files additional files to use with techs
	 * @return bemhtml $this
	 */
	public function page($page = null, $files = null) {
		$this->pagePath = $this->roots[0] . '/' . $page;
		$this->extraFiles = (array) $files;
		return $this;
	}


	/**
	 * Lazy v8 getter
	 */
	protected function v8js() {
		if (empty(static::$v8js)) {
			static::$v8js = new v8js();
			static::$v8js->pprint = function ($value, $to_term = false) {
				$s = print_r($value, 1); // pformat?
				($to_term) && syslog(LOG_INFO, $s);
				return $s;
			};
			static::$v8js->executeString("pprint = PHP.pprint;"); // todo: uhh... bad v8js. bbad!
		}

		// prepare v8js and create context but v8js cant create any contexts
		// but there are no contexts in v8js.
		// V8Js::registerExtension(... string ext_name, string script [, array deps [, bool auto_enable = FALSE]])
		/*if ($this->cacheContext) {
			$context_name = $pagedir + '/'.join(extra_files)
			$context = $this->contextscontexts.get(context_name)
			if ($context) {
				return $context
			}
		}*/

		/*$exts = [];
		$prepare = '';
		if ($this->useExtensions) {
			list ($name, $_exts_objs) = $this->get_extensions();
			$exts = [$name];
		} else {
			$prepare = $this->load_pagejs_data();
		}*/
		//$context = PyV8.JSContext(self.toplevelcls(),
		//	extensions=exts)

		//if self.cache_context:
		//	self.contexts[context_name] = context

		$prepare = $this->load_pagejs_data();
		$prepare && static::$v8js->executeString($prepare);

		return static::$v8js;
	}

	/**
	 * loading needed js files as string
	 * @return string
	 */
	protected function load_pagejs_data() {
		$jsData = array();
		$files = static::fs_fetch_files($this->pagePath, array_merge($this->extraFiles, $this->techs), false);
		foreach ($files as $fileName) {
			if (!is_readable($fileName) || !is_file($fileName)) {
				$jsData[] = '///! file skipped: '.$fileName;
				continue;
			}
			$jsData[] = '///! file loaded: '.$fileName;
			$jsData[] = file_get_contents($fileName);
		}

		return join("\n", $jsData);
	}

	/**
	 * dead by now
	 * @param string $pagedir
	 * @param array $extra_files
	 * @return mixed
	 */
	protected function get_extensions() {
        /*
        suffix = '/'.join(extra_files)
        name = JS_EXTENSION_NAME % {'pagedir': pagedir,
                                    'suffix': suffix}
        ext = self.pageextensions.get(name)*/
		//str_replace();

		$ext = $this->pageextensions;

        if (empty($ext)) {
            $page_js = $this->load_pagejs_data($pagedir, $extra_files);
            $ext = $this->v8js()->lol($name, $page_js);
            $this->pageextensions[$name] = $ext;
		}

        return array($name, $ext);
	}

	/**
	 * Create bemjson and render it
	 * BEMHTML.apply(entrypoint(context, env))
	 *
	 * @param mixed $context
	 * @param array $env
	 * @param string $entrypoint callable js object. render for example
	 * @param bool $json return json if true. html otherwise
	 * @return string json or html
	 */
	public function render ($context = array(), $env = array(), $entrypoint = '', $json = false) {
		$result = null;
		try {
			$jsctx = $this->v8js();
			// set variables
			is_string($context) && $context = json_decode($context);
			$jsctx->context = $context;
			$jsctx->env = $env;
			$jsctx->bemjson = $entrypoint
				? $jsctx->executeString(sprintf('%s(PHP.context, PHP.env)', $entrypoint))
				: $context;

			$result = $json
				? json_encode($jsctx->bemjson)
				: $jsctx->executeString('BEMHTML.apply(PHP.bemjson)');

		} catch (V8JsException $e) {
			echo (isset($e->xdebug_message) ? $e->xdebug_message : $e->getMessage()) . "\n";
			echo "\nJSTrace: " . $e->getJsTrace()."\n";
		}

		return $result;
	}

	/**
	 * Renders bemjson
	 * helper
	 * @see ::render
	 */
	public function json ($context = array(), $env = array(), $entrypoint = '') {
		return $this->render($context, $env, $entrypoint, true);
	}


	/**
	 * File search by level redefining rules
	 * @static
	 * @param string|array $where   basic paths for search
	 * @param string|array $what    file name patterns to search (wildcard syntax supported)
	 * @param int          $limit   count of results. return string if 1
	 * @return string|array|bool    returns the first found filepath or false
	 */
	static public function fs_fetch_files($where, $what, $limit = 1) {
		$where = (array)($where);
		$what  = (array)($what);
		$limit = (int)($limit);

		$results = array();
		//var_dump(compact('where', 'what', 'limit'));die;
		foreach ($where as $path) {
			if (!is_dir($path) || !is_readable($path)) {
				continue;
			}
			foreach ($what as $file) {
				foreach (glob($path . '/' . $file) as $filename) {
					if (!is_readable($filename) || !is_file($filename)) {
						continue;
					}
					$results[] = $filename;
					if ($limit and $limit >= count($results)) {
						break 3;
					}
				}
			}
		}

		if (empty($results)) {
			return false;
		}

		return $limit === 1 ? reset ($results) : $results;
	}
}
