<?php
	require_once('./models/User.php');

	Class userController
	{
		public function index(array $params){
			//lista todos os usuários e filtra caso tenhas parâmetros ex /?{play}=Apex%20Legends -> lista quem joga apex legends
			$user = new User();
			$response = $user->listAll($params);

			return $response;
		}

		public function show(int $id){
			//lista o usuário de id = $id
			$user = new User();
			$response = $user->findById($id);

			return $response;
		}

		public function store(string $json_data){
			//cria um usuário no BD com base no json recebido
			$user = new User();
			$response = $user->create($json_data);

			return $response;
		}

		public function update(string $json_data){
			//atualiza os dados de um usuário
			$user = new User();
			$response = $user->update($json_data);

			return $response;
		}

		public function delete(int $id){
			//deleta o usuário de id = {id presente no token}
			$user = new User();
			$response = $user->destroy();

			return $response;
		}		
	}
?>