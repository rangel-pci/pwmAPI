<?php
	require_once('./models/Like.php');

	Class likeController
	{
		//mostra os likes de todos os usuários
		public function index(array $params){
			$like = new like();
			$response = $like->listAll();

			return $response;
		}
		//retorna os likes que o usuário {$id} possui
		public function show(int $id){
			$like = new Like();
			$response = $like->findById($id);

			return $response;
		}
		//da like em um usuário
		public function store(int $id){
			$like = new Like();
			$response = $like->addLike($id);

			return $response;
		}

		public function update(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {like}'));
		}
		//tira o like de um usuário
		public function delete(int $id){
			$like = new Like();
			$response = $like->destroyLike($id);

			return $response;
		}
	}

?>