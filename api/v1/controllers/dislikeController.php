<?php
	require_once('./models/Dislike.php');

	Class dislikeController
	{
		//mostra os dislikes de todos os usuários
		public function index(array $params){
			$dislike = new dislike();
			$response = $dislike->listAll();

			return $response;
		}
		//retorna os dislikes que o usuário {$id} possui
		public function show(int $id){
			$dislike = new dislike();
			$response = $dislike->findById($id);

			return $response;
		}
		//da dislike em um usuário
		public function store(int $id){
			$dislike = new dislike();
			$response = $dislike->addDislike($id);

			return $response;
		}

		public function update(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {dislike}'));
		}
		//tira o dislike de um usuário
		public function delete(int $id){
			$dislike = new dislike();
			$response = $dislike->destroyDislike($id);

			return $response;
		}
	}

?>