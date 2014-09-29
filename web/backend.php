<?php
	session_start();
	require '../lib/autoload.php';
	
	$app = new apps\backend\BackendApplication;
	$app->run();
?>