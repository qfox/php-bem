<?php
/**
 * @copyright 2011-2012 Alex Yaroshevich
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */

include "phpbem.php";

define('wwwroot', realpath(__DIR__ . '/../examples/www'));

$bemhtml = new bemhtml(wwwroot);
$object = array(
	'url' => 'http://example.com/',
	'text' => 'Привет',
	'title' => function ($title) {
		return 'fiew'.rand(100000, 999999).': '.$title;
	}
);
$env = array();


// trying to render json
$jout = $bemhtml->page('pages/example', ['example.en.js'])->json($object, $env, 'render');
var_dump( $jout );


// set pages/example block
$bemhtml->page('pages/example');

// trying to render html
$out = $bemhtml->render($object, $env, 'render');
var_dump( $out );

// trying to render html by json
$out = $bemhtml->render($jout, $env);
var_dump( $out );
