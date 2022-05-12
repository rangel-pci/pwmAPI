<?php
	function dbConnect(){
		$h = Config::$db_host;
		$n = Config::$db_name;
		$u = Config::$db_user;
		$p = Config::$db_password;

		try{
			$pdo = new PDO("mysql:host=$h;dbname=$n", $u, $p);
			return $pdo;
		}catch(PDOException $e){
			
			echo $e;
			http_response_code(500);
			echo json_encode(array('status' => '500', 'response' => 'Internal server error, cannot conect to Database'));

			exit();
		}

	}

?>