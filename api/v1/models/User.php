<?php
	require_once('Gamelist.php');
	require_once('Like.php');
	require_once('Dislike.php');
	require_once('dbConnect.php');


	Class User
	{
		//public $id = 99;
		//private image, name, info, positive, negative, discord, steam, origin, twitch, psn, xbox

		//lista todos os usuários
		public function listAll(array $params){
			$pdo = dbConnect();
			$query;

			$users = array();
			$users_recommended = array();

			//busca os usuários por $name
			if (isset($params['search'])) {

				$name = '%'.$params['search'].'%';

				$query = "SELECT id, image, name, info, discord, steam, origin, twitch, psn, xbox, created FROM user WHERE name LIKE ? AND verified = ?";

				$sql = $pdo->prepare($query);
				$sql->execute([$name, 1]);
				
			}else{
				//verifica se o usuário está autenticado se sim, lista os usuário quem possuem o mesmo jogo que ele na game_list
				require_once('./inc/is_logged.php');

				//retorna o id do usuário logado
				//se a resposta não for do tipo int houve algum problema no processo
				$isLogged = is_logged($pdo); 
				if(gettype($isLogged) !== gettype(1)){
					$isLogged = false;
				}else{
					$user_id = $isLogged;
				}
				//se o token é válido e rotornou o id do usuário, significa que ele está tá logado e pode realizar a ação

				if($isLogged){
					// pega o jogos do usuário logado
					$query = "SELECT game_id FROM user_game WHERE user_id = ? ORDER BY RAND() LIMIT 0, 15";
					$sql = $pdo->prepare($query);
					$sql->execute([$user_id]);

					$games_id = array();
					while($row = $sql->fetch(PDO::FETCH_ASSOC)){
						$games_id[] = $row['game_id'];
					}

					if(count($games_id) > 0){
						// pega os usuários que possuem um ou mais jogo iguais ao do usuário logado
						$in  = str_repeat('?,', count($games_id) - 1) . '?';
						$query = "SELECT user_id FROM user_game WHERE game_id IN ($in) AND user_id != ? GROUP BY user_id ORDER BY RAND() LIMIT 0, 15";
						$sql = $pdo->prepare($query);
						$sql->execute(array_merge($games_id, [$user_id]));

						$users_id = array();
						while($row = $sql->fetch(PDO::FETCH_ASSOC)){
							$users_id[] = $row['user_id'];
						}

						if(count($users_id) > 0){
							$users_recommended = [];
							foreach($users_id as $id){
								$user = json_decode($this->findById($id), true);
								if ($user['status'] == 200) {
									$users_recommended[] = $user['response'];
								} 	
							}

							$limit = 15 - count($users_recommended);

							$in  = str_repeat('?,', count($users_id) - 1) . '?';
							$query = "SELECT id, image, name, info, discord, steam, origin, twitch, psn, xbox, created FROM user WHERE id NOT IN ($in) AND verified = ? ORDER BY RAND() LIMIT 0, $limit";
							$sql = $pdo->prepare($query);
							$sql->execute(array_merge($users_id, [1]));
						}else{
							$query = "SELECT id, image, name, info, discord, steam, origin, twitch, psn, xbox, created FROM user WHERE verified = ? ORDER BY RAND() LIMIT 0, 15";
							$sql = $pdo->prepare($query);
							$sql->execute([1]);	
						}
					}else{
						$query = "SELECT id, image, name, info, discord, steam, origin, twitch, psn, xbox, created FROM user WHERE verified = ? ORDER BY RAND() LIMIT 0, 15";
						$sql = $pdo->prepare($query);
						$sql->execute([1]);
					}
				}else{
					//caso não esteja logado, paxa 15 usuários de forma aleatória
					$query = "SELECT id, image, name, info, discord, steam, origin, twitch, psn, xbox, created FROM user WHERE verified = ? ORDER BY RAND() LIMIT 0, 15";
					$sql = $pdo->prepare($query);
					$sql->execute([1]);
				}
			}			

			$users = array();

			if($sql->rowCount() < 1){
				http_response_code(404);
				return json_encode(array('status' => '404', 'total' => count($users), 'response' => 'no users match the request'));
			}
			$i = 0;
			while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
				$users[] = $row;

				//retorna também a lista de jogos do usuário
				for ($i = 0; $i < count($users); $i++) { 
					$game_list = json_decode(Gamelist::findById($users[$i]['id']), true);
					if ($game_list['status'] == 200) {
						$users[$i]['game_list'] = $game_list['response']['game_list'];
					}
				}
			}

			if(count($users_recommended) == 0){
				if (isset($params['search'])){
					http_response_code(200);
					return json_encode(array('status' => '200', 'total' => count($users), 'response' => $users));
				}
				http_response_code(200);
				return json_encode(array('status' => '200', 'total' => count($users), 'response' => ['users_random' => $users]));
			}else{
				http_response_code(200);
				return json_encode(array(
					'status' => '200',
					'total' => count(array_merge($users, $users_recommended)), 
					'response' => ['users_recommended' => $users_recommended, 'users_random' => $users]));
			}
		}

		//lista o usuário de id = $id
		public function findById(int $id){
			
			$pdo = dbConnect();
			$query;

			
			$query = "SELECT id, image, name, info, discord, steam, origin, twitch, psn, xbox, created FROM user WHERE id = ? AND verified = 1";
			
			$sql = $pdo->prepare($query);
			$sql->execute([$id]);

			if ($sql->rowCount() < 1) {

				http_response_code(404);
				return json_encode(array('status' => '404', 'response' => 'user not found'));
			}

			$user = $sql->fetch(PDO::FETCH_ASSOC);

			//retorna também a lista de jogos do usuário
			$game_list = json_decode(Gamelist::findById($id), true);
			if ($game_list['status'] == 200) {
				$user['game_list'] = $game_list['response']['game_list'];
			}

			//retorna também os likes e dislikes do usuário
			$likes = json_decode(Like::findById($id), true);
			if ($likes['status'] == 200) {
				$user['likes'] = $likes['response'];
			}
			//dislikes
			$dislikes = json_decode(Dislike::findById($id), true);
			if ($dislikes['status'] == 200) {
				$user['dislikes'] = $dislikes['response'];
			}

			//retorna também as interações do usuário -> soma de likes e dislikes dados e recebidos
			$query = "SELECT liked_user, COUNT(liked_user) AS Qtd FROM user_like WHERE liked_user = ? OR who_liked = ?";
			
			$sql = $pdo->prepare($query);
			$sql->execute([$id, $id]);

			$likesQtd = $sql->fetch(PDO::FETCH_ASSOC)['Qtd'];
			
			$query = "SELECT disliked_user, COUNT(disliked_user) AS Qtd FROM user_dislike WHERE disliked_user = ? OR who_disliked = ?";
			
			$sql = $pdo->prepare($query);
			$sql->execute([$id, $id]);

			$dislikesQtd = $sql->fetch(PDO::FETCH_ASSOC)['Qtd'];
			//total de interações
			$user['interactions'] = $likesQtd + $dislikesQtd;

			http_response_code(200);
			return json_encode(array('status' => '200', 'response' => $user));
		}

		//cria um usuário no BD com base no json recebido
		public function create(string $json_data){

			if($json_data == null){
				http_response_code(400);
				return json_encode(array('status' => '400', 'response' => 'missing json user data'));
			}

			$user_data = json_decode($json_data, true);

			//verifica se os dados estão aptos a irem para o BD
			require_once ('./inc/is_eligible.php');
			$is_eligible = is_eligible($user_data);

			if (!$is_eligible['is_ok']){
				//retorna em uma string os campos invalidos

				http_response_code(400);
				return json_encode(array('status' => '400', 'response' => $is_eligible['not_eligible']));
			}else{

				//cria a imagem e retorna o caminho, retorna falso se ocorrer algum erro
				require_once ('./inc/create_image.php');
				$created = create_image($user_data);

				if (!$created){

					http_response_code(400);
					return json_encode(array('status' => '400', 'response' => 'only images smaller than 4mb are allowed'));
				}else{
					//salva os dados no BD

					$pdo = dbConnect();
					$query;

					//url da img
					$img_path = $created;

					//criptografa a senha
					$salt = '@play$03948034$with1989@9me';
					$password = md5($user_data['password'].$salt);

					//chave de verificação
					$vkey = md5(time().$user_data['login']);

					//tira espaços multiplos do nome e tags html de alguns campos
					str_replace('  ', ' ', $user_data['name']);
					$user_data['name'] = ucfirst($user_data['name']);

					$user_values = array(
						$user_data['login'],
						strip_tags($user_data['name']),
						$user_data['age'],
						$user_data['email'],
						$password,
						strip_tags($user_data['info']),
						$img_path,
						strip_tags($user_data['discord']),
						strip_tags($user_data['steam']),
						strip_tags($user_data['origin']),
						strip_tags($user_data['twitch']),
						strip_tags($user_data['psn']),
						strip_tags($user_data['xbox']),
						$verified = 0,
						$vkey
					);
					
					$query = "SELECT id FROM user WHERE login = ?";
					$sql = $pdo->prepare($query);
					$sql->execute([$user_values[0]]);

					//verifica se já esixte algum usuário com o mesmo login ou email
					if ($sql->rowCount() > 0){

						//echo $_SERVER['DOCUMENT_ROOT'];
						$img_path = explode('/', $img_path);
						unlink($_SERVER['DOCUMENT_ROOT'].'/profile_image/'.end($img_path));						
						http_response_code(409);
						return json_encode(array('status' => '409', 'response' => 'the login already exists, choose another one'));
					}else{
						$query = "SELECT id FROM user WHERE email = ?";
						$sql = $pdo->prepare($query);
						$sql->execute([$user_values[3]]);

						if ($sql->rowCount() > 0) {

							$img_path = explode('/', $img_path);
							unlink($_SERVER['DOCUMENT_ROOT'].'/profile_image/'.end($img_path));

							http_response_code(409);
							return json_encode(array('status' => '409', 'response' => 'the email already exists, choose another one'));
						
						}
					}

					//se não existe, cadastra o usuário no BD
					$query = "INSERT INTO user(login, name, age, email, password, info, image, discord, steam, origin, twitch, psn, xbox, verified, vkey) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

					$sql = $pdo->prepare($query);
					$sql->execute($user_values);
					
					if ($sql->rowCount() > 0){

						//envia um email com o código de ativação da conta
						$email = $user_data['email'];
						$name = $user_data['name'];
						$subject = Config::$app_name.' Ativação de conta';
						$body = "Clique \"<a href='".Config::$app_url."register/verification?key=$vkey'><strong>AQUI</strong></a>\" para ativar a sua conta";
						
						require_once ('./inc/send_mail.php');

						http_response_code(201);
						return json_encode(array('status' => '201', 'response' => 'a verification key was sent to \''.$email.'\''));
					}else{

						http_response_code(500);
						return json_encode(array('status' => '500', 'response' => 'internal server error, the user could not be created'));
					}
				}
			}			
		}

		//atualiza os dados de um usuário no BD
		public function update(string $json_data){
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

			$user_data = json_decode($json_data, true);
			//define 4 valores para reaproveitar is_eligible() sem problemas
			$user_data['password'] = 'userpassword';
			$user_data['login'] = 'userlogin';
			$user_data['email'] = 'user@email.com';
			$user_data['age'] = '10';

			//verifica se os dados estão aptos a irem para o BD
			require_once ('./inc/is_eligible.php');
			$is_eligible = is_eligible($user_data);

			if (!$is_eligible['is_ok']){
				//se for falso retorna em uma string os campos invalidos

				http_response_code(400);
				return json_encode(array('status' => '400', 'response' => $is_eligible['not_eligible']));
			}else{

				//cria a imagem e retorna o caminho, retorna falso se ocorrer algum erro
				require_once ('./inc/create_image.php');
				$created = create_image($user_data);

				if (!$created){

					http_response_code(400);
					return json_encode(array('status' => '400', 'response' => 'only images smaller than 4mb are allowed'));
				}else{
					//salva os dados no BD
					//url da img
					$img_path = $created;

					//tira espaços multiplos do nome e tags html de alguns campos
					str_replace('  ', ' ', $user_data['name']);
					$user_data['name'] = ucfirst($user_data['name']);

					$user_values = array(
						strip_tags($user_data['name']),
						strip_tags($user_data['info']),
						$img_path,
						strip_tags($user_data['discord']),
						strip_tags($user_data['steam']),
						strip_tags($user_data['origin']),
						strip_tags($user_data['twitch']),
						strip_tags($user_data['psn']),
						strip_tags($user_data['xbox']),
						$user_id
					);

					$query = "SELECT image FROM user WHERE id = ?";
					$sql = $pdo->prepare($query);
					$sql->execute([$user_id]);

					if($sql->rowCount() < 1){

						$img_path = explode('/', $img_path);
						unlink($_SERVER['DOCUMENT_ROOT'].'/profile_image/'.end($img_path));

						http_response_code(404);
						return json_encode(array('status' => '404', 'response' => 'user not found'));
					}

					$row = $sql->fetch();
					$old_img_path = $row['image'];
					//apos upar a nova imagem exclui a antiga
					//echo $_SERVER['DOCUMENT_ROOT'];
					$old_img_path = explode('/', $old_img_path);
					
					unlink($_SERVER['DOCUMENT_ROOT'].'/profile_image/'.end($old_img_path));

					
					$query = "UPDATE user SET name = ?, info = ?, image = ?, discord = ?, steam = ?, origin = ?, twitch = ?, psn = ?, xbox = ? WHERE id = ?";

					$sql = $pdo->prepare($query);
					$sql->execute($user_values);
					
					if ($sql->rowCount() > 0){

						http_response_code(200);
						return json_encode(array('status' => '200', 'response' => 'user updated'));
					}else{

						http_response_code(500);
						return json_encode(array('status' => '500', 'response' => 'internal server error, the user could not be updated'));
					}					
				}
			}
		}

		//delete um usuário de id = $id
		public function destroy(){
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

			//exclui a imagem do servidor
			$query = "SELECT image FROM user WHERE id = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id]);

			$row = $sql->fetch();
			$img_path = $row['image'];
			$img_path = explode('/', $img_path);
			unlink($_SERVER['DOCUMENT_ROOT'].'/profile_image/'.$img_path[6]);

			//exclui a game list do usuário
			$query = "DELETE FROM user_game WHERE user_id = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id]);

			//exclui os likes e deslikes
			$query = "DELETE FROM user_like WHERE liked_user = ? OR who_liked = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id, $user_id]);

			$query = "DELETE FROM user_dislike WHERE disliked_user = ? OR who_disliked = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id, $user_id]);

			//exclui o token do usuário
			$query = "DELETE FROM user_token WHERE user_id = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id]);

			//exclui o usuário do BD
			$query = "DELETE FROM user WHERE id = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id]);

			http_response_code(200);
			return json_encode(array('status'=>'200', 'response'=>'the user and their connections have been deleted'));
		}
	}
?>