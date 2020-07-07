<?php
	//session_start();
	//$_SESSION['loggedUser'] = false;
	//$_SESSION['userId'] = 0;

	require_once('User.php');
	require_once('dbConnect.php');

	Class Game
	{
		//lista todos os jogos
		public function listAll(array $params){
			$pdo = dbConnect();
			$query = "SELECT * FROM game WHERE name LIKE ? ORDER BY RAND() LIMIT 0, 15";
			$name = '%%';

			if (isset($params['search'])) {
				$name = '%'.$params['search'].'%';
				$query = "SELECT * FROM game WHERE name LIKE ?";
			}

			$sql = $pdo->prepare($query);
			$sql->execute([$name]);

			if ($sql->rowCount() < 1) {
				
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'no games match the request'));
			}

			$i = 0;
			$games = array();
			while($row = $sql->fetch(PDO::FETCH_ASSOC)){
				$games[] = $row;
				$i++;
				if($i == 15){
					break;
				}
			}

			http_response_code(200);
			return json_encode(array('status'=>'200', 'total'=> $i, 'response'=>$games));
		}

		//lista o jogo de id = $id
		public function findById(int $id){

			$pdo = dbConnect();
			$query = "SELECT * FROM game WHERE id = ?";

			$sql = $pdo->prepare($query);
			$sql->execute([$id]);

			if ($sql->rowCount() < 1) {
				
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'game not found'));
			}

			$game = $sql->fetch(PDO::FETCH_ASSOC);
			
			http_response_code(200);
			return json_encode(array('status'=>'200', 'response'=>$game));
		}
	}

?>