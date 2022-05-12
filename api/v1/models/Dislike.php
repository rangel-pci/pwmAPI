<?php
	require_once('Like.php');
	require_once('User.php');
	require_once('dbConnect.php');

	Class Dislike
	{
		//lista os dislikes de todos os usuários
		public function listAll(){
			$pdo = dbConnect();

			$query = "SELECT disliked_user FROM user_dislike GROUP BY disliked_user";
			$sql = $pdo->prepare($query);
			$sql->execute();

			if ($sql->rowCount() < 1) {
				http_response_code(404);
				return json_encode(array('status' => '404', 'response' => 'no users disliked'));
			}

			$i = $sql->rowCount();
			$disliked_users = array();
			$dislikes = array();
			while($row = $sql->fetch(PDO::FETCH_ASSOC)){
				$dislike = json_decode(Dislike::findById($row['disliked_user']), true);
				if ($dislike['status'] == 200) {
					$dislikes['disliked_user'] = $row['disliked_user'];
					$dislikes['quantity'] = $dislike['response']['quantity'];
					$dislikes['who_disliked'] = $dislike['response']['who_disliked'];

					$disliked_users[] = $dislikes;
				}
			}

			http_response_code(200);
			return json_encode(array('status' => '200', 'total' => $i, 'response' => $disliked_users));
		}
		//retorna os dislikes que o usuário {$id} possui
		public function findById(int $id){
			$pdo = dbConnect();
			$query = "SELECT who_disliked FROM user_dislike WHERE disliked_user = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$id]);

			if ($sql->rowCount() < 1) {
				http_response_code(404);
				return json_encode(array('status' => '404', 'response' => 'user without dislikes'));
			}

			$dislikes = array();
			$dislikes['quantity'] = $sql->rowCount();
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$dislikes['who_disliked'][] = array('id'=>$row['who_disliked']);
			}

			http_response_code(200);
			return json_encode(array('status' => '200',	 'response' => $dislikes));
		}
		//da dislike em um usuário
		public function addDislike(int $id){
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

			$disliked_user = $id;
			$who_disliked = $user_id;

			$user1 = json_decode(User::findById($disliked_user), true);

			if ($user1['status'] == 200) {
				//verifica se o alvo já recebeu dislike
				$query = "SELECT * FROM user_dislike WHERE disliked_user = ? AND who_disliked = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$disliked_user, $who_disliked]);

				if ($sql->rowCount() > 0) {
					http_response_code(409);
					return json_encode(array('status'=>'409', 'response'=>'user already disliked'));
				}
				//verifica se o usuário já deu like e tira o like
				Like::destroyLike($disliked_user);

				$query = "INSERT INTO user_dislike (disliked_user, who_disliked) VALUES(?, ?)";
				$sql = $pdo->prepare($query);
				$sql->execute([$disliked_user, $who_disliked]);

				if ($sql->rowCount() > 0) {
						
					http_response_code(201);
					return json_encode(array('status'=>'201', 'response'=>'disliked'));
				}else{

					http_response_code(500);
					return json_encode(array('status'=>'500', 'response'=>'internal server error'));
				}

			}else{
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'users not found'));
			}
		}
		//tira o dislike de um usuário
		public function destroyDislike(int $id){
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
			
			$disliked_user = $id;
			$who_disliked = $user_id;

			$user1 = json_decode(User::findById($disliked_user), true);

			if ($user1['status'] == 200) {
				$query = "SELECT * FROM user_dislike WHERE disliked_user = ? AND who_disliked = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$disliked_user, $who_disliked]);

				if ($sql->rowCount() < 1) {
					http_response_code(404);
					return json_encode(array('status'=>'404', 'response'=>'the user was not disliked'));
				}

				$query = "DELETE FROM user_dislike WHERE disliked_user = ? AND who_disliked = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$disliked_user, $who_disliked]);

				if ($sql->rowCount() > 0) {
						
					http_response_code(200);
					return json_encode(array('status'=>'200', 'response'=>'dislike removed'));
				}else{

					http_response_code(500);
					return json_encode(array('status'=>'500', 'response'=>'internal server error'));
				}

			}else{
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'users not found'));
			}
		}
	}
?>