<?php

require_once "../phpbem/phpbem.php";

// use bem\bemhtml;

// Path with pages dirs
$wwwpath = __DIR__ . '/www';

// Create BEM renderer
$bem = new bemhtml($wwwpath);

// making dynamic context
$context = array(
	'url'   => 'http://example.com/',
	'text'  => 'Привет',
	'title' => function ($title) {
		return sprintf('(%s) %s', $_SERVER['REQUEST_URI'], $title);
	}
);
$env = $_REQUEST;

// render it
$bem->page('pages/example', array('example.en.js'));
echo $bem->render($context, $env, 'render');

