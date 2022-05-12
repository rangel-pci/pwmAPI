<?php
	require_once('Game.php');
	require_once('dbConnect.php');

	Class Gamelist
	{
		//lista todas as game lists
		public function listAll(array $params){

			$pdo = dbConnect();
			$query = "SELECT user_id FROM user_game GROUP BY user_id";

			$sql = $pdo->prepare($query);
			$sql->execute();

			if ($sql->rowCount() < 1) {
				
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'no game lists found'));
			}
			
			$i = $sql->rowCount();
			$users_id = array();
			$game_lists = array();

			while($row = $sql->fetch(PDO::FETCH_ASSOC)){
				$users_id[] = $row;
			}

			foreach ($users_id as $key => $value) {
				foreach ($value as $id) {
					$game_list = json_decode(Gamelist::findById($id), true);
					$game_lists[] = array('user_id'=>$id, 'game_list'=>$game_list['response']['game_list']);
				}
			}

			http_response_code(200);
			return json_encode(array('status'=>'200', 'total'=> $i, 'response'=>$game_lists));
		}

		//lista a game list do usuário de id = {$id}
		public function findById(int $id){

			$pdo = dbConnect();
			$query = "SELECT game_id FROM user_game WHERE user_id = ?";

			$sql = $pdo->prepare($query);
			$sql->execute([$id]);

			if ($sql->rowCount() < 1) {
				
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'game list not found'));
			}

			$game_list = array();
			while($row = $sql->fetch(PDO::FETCH_ASSOC)){
				$game_details = json_decode(Game::findById($row['game_id']), true);
				$game_list[] = $game_details['response'];
			}

			// $name = array_column($game_list, 'name');
			// array_multisort($name, SORT_ASC, $game_list);
			$game_list = array_reverse($game_list);
			
			http_response_code(200);
			return json_encode(array('status'=>'200', 'response'=>array('game_list'=>$game_list)));
		}

		//adiciona um jogo {$id} a lista de um usuário
		public function linkGame(int $game_id){
			$pdo = dbConnect();

			//verifica se o usuário está autenticado
			require_once('./inc/is_logged.php');

			//retorna o id do usuário logado
			//se a resposta não for do tipo int houve algum problema no processo
			$isLogged = is_logged($pdo); 
			if(gettype($isLogged) !== gettype(1)){
				
				http_response_code(401);
				return json_encode(array('status'=>'401', 'response'=>'invalid or expired token, log in to continue'));
			}else{
				$user_id = $isLogged;
			}
			//se o token é válido e rotornou o id do usuário, significa que ele está tá logado
			//e pode realizar a ação
			
			$game = json_decode(Game::findById($game_id));
			
			//verifica se o jogo existe no BD e cria o registro
			if($game->status == 200){				
				$query = "SELECT * FROM user_game WHERE user_id = ? AND game_id = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$user_id, $game_id]);

				if ($sql->rowCount() > 0) {
						
					http_response_code(409);
					return json_encode(array('status'=>'409', 'response'=>'the same game has already been added'));
				}			

				$query = "INSERT INTO user_game(user_id, game_id) VALUES (?, ?)";
				$sql = $pdo->prepare($query);
				$sql->execute([$user_id, $game_id]);

				if ($sql->rowCount() > 0) {
						
					http_response_code(201);
					return json_encode(array('status'=>'201', 'response'=>'game added to list'));
				}else{

					http_response_code(500);
					return json_encode(array('status'=>'500', 'response'=>'internal server error'));
				}
			}else{
				
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'game not found'));
			}
		}

		//remove um jogo da lista de um usuário
		public function unlinkGame(int $game_id){
			$pdo = dbConnect();

			//verifica se o usuário está autenticado
			require_once('./inc/is_logged.php');

			//retorna o id do usuário logado
			//se a resposta não for do tipo int houve algum problema no processo
			$isLogged = is_logged($pdo); 
			if(gettype($isLogged) !== gettype(1)){
				
				http_response_code(401);
				return json_encode(array('status'=>'401', 'response'=>'invalid or expired token, log in to continue'));
			}else{
				$user_id = $isLogged;
			}
			//se o token é válido e rotornou o id do usuário, significa que ele está tá logado
			//e pode realizar a ação

			$query = "SELECT * FROM user_game WHERE user_id = ? AND game_id = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id, $game_id]);

			if($sql->rowCount() < 1){
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'game not found in list'));
			}

			$query = "DELETE FROM user_game WHERE user_id = ? AND game_id = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id, $game_id]);

			if($sql->rowCount() > 0){
				
				http_response_code(200);
				return json_encode(array('status'=>'200', 'response'=>'game removed from list'));
			}else{

				http_response_code(500);
				return json_encode(array('status'=>'500', 'response'=>'internal server error'));
			}
		}
	}

?>