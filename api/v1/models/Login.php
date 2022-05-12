<?php
	require_once('User.php');
	require_once('dbConnect.php');

	Class Login
	{
		public function tryLogIn(string $json_data){
			$pdo = dbConnect();
			$user_data = json_decode($json_data, true);

			//verifica antes se o cliente quer apenas renovar o token e não fazer login novamente
			if (isset($user_data['renew_token'])) {
				return $this->renewToken($pdo, $user_data);
			}

			//faz login
			if(isset($user_data['login']) == null || isset($user_data['password']) == null){
				
				http_response_code(400);
				return json_encode(array('status'=>'400', 'response'=>'missing json {login} or {password}'));
			}

			$login = $user_data['login'];
			$password = $user_data['password'];
			$salt = '@play$03948034$with1989@9me';
			$password = md5($password.$salt);

			//$header = getallheaders();
			//echo $header['User-Agent'];

			$query = "SELECT id, name, email, image FROM user WHERE login = ? AND password = ? AND verified = 1";
			$sql = $pdo->prepare($query);
			$sql->execute([$login, $password]);

			if ($sql->rowCount() > 0) {
				$user = $sql->fetch(PDO::FETCH_ASSOC);
				
				//remove o ultimo token usado pelo usuário para adicionar um novo
				$query = "DELETE FROM user_token WHERE user_id = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$user['id']]);

				//cria um token jwt com base no tempo atual, id, nome e email do usuário
				require_once('./inc/JWT.php');
				$time = time();
				$jti = md5($time.$user['email']);
				$jwt = new JWT();
				$token = $jwt->setToken($jti, $time, $user['id'], $user['name'], $user['email'], $user['image']);

				//salva o token no BD
				$query = "INSERT INTO user_token(user_id, jti) VALUES(?, ?)";
				$sql = $pdo->prepare($query);
				$sql->execute([$user['id'], $jti]);

				if($sql->rowCount() > 0){

					 http_response_code(200);
					 return json_encode(array('status'=>200, 'response'=>''.$token));
				}else{
					
					http_response_code(500);
					return json_encode(array('status' => '500', 'response' => 'internal server error, try again in a moment'));
				}

				// tem que ir para o BD -> jti
				//echo $token;
				//$date = new DateTime();
				//$date->setTimeStamp(time());
				//echo $date->format('d/m/Y/ H:i:s');

				//validar o token
				//var_dump($jwt->isValid($token, $jti));

				//echo $token->getHeader('jti');
				//echo $token->getClaim('uname');

			}else{
				http_response_code(422);
				return json_encode(array('status'=>'422', 'response'=>'username or password is invalid'));
			}
		}

		public function renewToken(object $pdo, array $user_data){
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
			//se o token é válido e rotornou o id do usuário, significa que ele está logado
			//e pode realizar a ação

			$query = "SELECT id, name, email, image FROM user WHERE id = ? AND verified = 1";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id]);

			if ($sql->rowCount() > 0) {
				$user = $sql->fetch(PDO::FETCH_ASSOC);
				
				//remove o ultimo token usado pelo usuário para adicionar um novo
				$query = "DELETE FROM user_token WHERE user_id = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$user['id']]);

				//cria um token jwt com base no tempo atual, id, nome e email do usuário
				require_once('./inc/JWT.php');
				$time = time();
				$jti = md5($time.$user['email']);
				$jwt = new JWT();
				$token = $jwt->setToken($jti, $time, $user['id'], $user['name'], $user['email'], $user['image']);

				//salva o token no BD
				$query = "INSERT INTO user_token(user_id, jti) VALUES(?, ?)";
				$sql = $pdo->prepare($query);
				$sql->execute([$user['id'], $jti]);

				if($sql->rowCount() > 0){

					 http_response_code(200);
					 return json_encode(array('status'=>200, 'response'=>''.$token));
				}else{
					
					http_response_code(500);
					return json_encode(array('status' => '500', 'response' => 'internal server error, try again in a moment'));
				}
			}else{
				http_response_code(500);
					return json_encode(array('status' => '500', 'response' => 'internal server error, try again in a moment'));
			}	
		}
	}
?>