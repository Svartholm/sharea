<?php
	session_start();
	require '../lib/autoload.php';

	$frontend = new apps\frontend\FrontendApplication;
	$frontend->run();
?>
