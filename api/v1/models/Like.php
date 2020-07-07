<?php
	require_once('Dislike.php');
	require_once('User.php');
	require_once('dbConnect.php');

	Class Like
	{
		//lista os likes de todos os usuários
		public function listAll(){
			$pdo = dbConnect();

			$query = "SELECT liked_user FROM user_like GROUP BY liked_user";
			$sql = $pdo->prepare($query);
			$sql->execute();

			if ($sql->rowCount() < 1) {
				http_response_code(404);
				return json_encode(array('status' => '404', 'response' => 'no users liked'));
			}

			$i = $sql->rowCount();
			$liked_users = array();
			$likes = array();
			while($row = $sql->fetch(PDO::FETCH_ASSOC)){
				$like = json_decode(Like::findById($row['liked_user']), true);
				if ($like['status'] == 200) {
					$likes['liked_user'] = $row['liked_user'];
					$likes['quantity'] = $like['response']['quantity'];
					$likes['who_liked'] = $like['response']['who_liked'];

					$liked_users[] = $likes;
				}
			}

			http_response_code(200);
			return json_encode(array('status' => '200', 'total' => $i, 'response' => $liked_users));
		}
		//retorna os likes que o usuário de id = {$id} possui
		public function findById(int $id){
			$pdo = dbConnect();
			$query = "SELECT who_liked FROM user_like WHERE liked_user = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$id]);

			if ($sql->rowCount() < 1) {
				http_response_code(404);
				return json_encode(array('status' => '404', 'response' => 'user without likes'));
			}

			$likes = array();
			$likes['quantity'] = $sql->rowCount();
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$likes['who_liked'][] = array('id'=>$row['who_liked']);
			}

			http_response_code(200);
			return json_encode(array('status' => '200',	 'response' => $likes));
		}
		//da like em um usuário
		public function addLike(int $id){
			$pdo = dbConnect();
			//verifica se o usuário está autenticado
			require_once('./inc/is_logged.php');

			//retorna o id do usuário logado
			//se a resposta não for do tipo int houve algum problema no processo
			$isLogged = is_logged($pdo); 
			if(gettype($isLogged) !== gettype(1)){
				
				http_response_code(401);
				return json_encode(array('status'=>'401', 'response'=>'invalid or expired token, login at api.playwithme/login to continue'));
			}else{
				$user_id = $isLogged;
			}
			//se o token é válido e rotornou o id do usuário, significa que ele está logado
			//e pode realizar a ação

			$liked_user = $id;
			$who_liked = $user_id;

			$user1 = json_decode(User::findById($liked_user), true);

			if ($user1['status'] == 200) {
				//verifica se já deu like no alvo
				$query = "SELECT * FROM user_like WHERE liked_user = ? AND who_liked = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$liked_user, $who_liked]);

				if ($sql->rowCount() > 0) {
					http_response_code(409);
					return json_encode(array('status'=>'409', 'response'=>'user already liked'));
				}

				//verifica se o usuário já deu dislike e tira o dislike
				Dislike::destroyDislike($liked_user);

				$query = "INSERT INTO user_like (liked_user, who_liked) VALUES(?, ?)";
				$sql = $pdo->prepare($query);
				$sql->execute([$liked_user, $who_liked]);

				if ($sql->rowCount() > 0) {
						
					http_response_code(201);
					return json_encode(array('status'=>'201', 'response'=>'liked'));
				}else{

					http_response_code(500);
					return json_encode(array('status'=>'500', 'response'=>'internal server error'));
				}

			}else{
				http_response_code(404);
				return json_encode(array('status'=>'404', 'response'=>'users not found'));
			}
		}
		//tira o like de um usuário
		public function destroyLike(int $id){
			$pdo = dbConnect();
			//verifica se o usuário está autenticado
			require_once('./inc/is_logged.php');

			//retorna o id do usuário logado
			//se a resposta não for do tipo int houve algum problema no processo
			$isLogged = is_logged($pdo); 
			if(gettype($isLogged) !== gettype(1)){
				
				http_response_code(401);
				return json_encode(array('status'=>'401', 'response'=>'invalid or expired token, login at api.playwithme/login to continue'));
			}else{
				$user_id = $isLogged;
			}
			//se o token é válido e rotornou o id do usuário, significa que ele está tá logado
			//e pode realizar a ação

			$liked_user = $id;
			$who_liked = $user_id;

			$user1 = json_decode(User::findById($liked_user), true);

			if ($user1['status'] == 200) {
				$query = "SELECT * FROM user_like WHERE liked_user = ? AND who_liked = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$liked_user, $who_liked]);

				if ($sql->rowCount() < 1) {
					http_response_code(404);
					return json_encode(array('status'=>'404', 'response'=>'the user was not liked'));
				}

				$query = "DELETE FROM user_like WHERE liked_user = ? AND who_liked = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$liked_user, $who_liked]);

				if ($sql->rowCount() > 0) {
						
					http_response_code(200);
					return json_encode(array('status'=>'200', 'response'=>'like removed'));
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