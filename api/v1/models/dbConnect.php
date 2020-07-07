<?php
	function dbConnect(){

		try{
			$pdo = new PDO('mysql:host=127.0.0.1;dbname=pwm_db', 'root', '');
			return $pdo;
		}catch(PDOException $e){
			
			echo $e;
			http_response_code(500);
			echo json_encode(array('status' => '500', 'response' => 'Internal server error, cannot conect to Database'));

			exit();
		}

	}

?>