<?php
	//mostra erros e warnings no script
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	// classe com as configurações globais do sistema Config::$static_config_name
	require_once('./inc/config.php');

	header('Content-Type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: '.Config::$access_control_allow_origin);
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization, Content-Type, origin, accept, host, date');

	date_default_timezone_set('America/Sao_Paulo');

	require_once('./inc/vendor/autoload.php');
	
	if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
		
		$this->next->call();
	}else{
		require_once('./server.php');

		if(isset($_REQUEST)){
			echo Rest::open($_REQUEST);
		}
	}

	
?>