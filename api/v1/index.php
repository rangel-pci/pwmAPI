<?php
	//mostra erros e warnings no script
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	//

	header('Content-Type: application/json; charset=utf-8');
	header('Access-Control-Allow-Origin: http://localhost:3000');
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization, Content-Type, origin, accept, host, date');

	date_default_timezone_set('America/Sao_Paulo');
	
	if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
		
		$this->next->call();
	}else{
		require_once('./server.php');

		if(isset($_REQUEST)){
			echo Rest::open($_REQUEST);
		}
	}

	
?>