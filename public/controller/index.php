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
		case 'init':
			$key = $_GET['key'];
			$value = $_GET['value'];
			$app->setup($key, $value);
			echo $app;
			break;
		case 'load':
			$app->fetch()->toJSON();
			break;
		case 'insert':
			break;
		case 'debug':
			echo $app->fetch();
			break;
		default:
			die("invalid action provided");
	}
endif;
