<?php

if(empty($_GET)){
	header('Location:/');
	exit();
}

require_once('../../app/app.php');

if($app_loaded):
	$app = new App();

	$action = $_GET['action'];

	$app->debug("action", $action);

	switch($action){
		case 'set':
			$key = $_GET['key'];
			$value = $_GET['value'];
			$app->config($key, $value);
			echo $app;
			break;
		case 'load':
			$app->fetch()->toJSON();
			break;
		case 'claim':
			if(empty($_POST['date'])  || empty($_POST['value'])):
				die('{"error": "invalid post data"}');
			endif;

			$app->claim($_POST['date'], $_POST['value'])->fetch()->toJSON();

			break;
		case 'debug':
			echo $app->fetch();
			break;
		default:
			die("invalid action provided");
	}
endif;
