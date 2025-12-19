<?php
require 'utils/utils.php';
require 'utils/splAutoload.php';

$path = $_SERVER['REDIRECT_URL'];


if ($path == '/') {
	require 'controllers/indexController.php';
} else {
	$path = explode('/', $path)[1];

	$controlleur = 'controllers/' . $path . 'Controller.php';

	if (file_exists($controlleur)) {
		require $controlleur;
	} else {
		require 'views/404.php';
	}
}

// Au tout début de router.php
if ($_SERVER['REQUEST_URI'] === '/test_simple.php') {
    require 'test_simple.php';
    exit;
}