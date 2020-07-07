<?php
	require_once('./models/Gamelist.php');

	Class gamelistController
	{
		//lista a game list de todos os usuários
		public function index(array $params){
			$game_list = new Gamelist();
			$response = $game_list->listAll($params);

			return $response;
		}

		//lista a game list do usuário de id = {$id}
		public function show(int $id){
			$game_list = new Gamelist();
			$response = $game_list->findById($id);

			return $response;
		}

		//adiciona um jogo de id {$id} a game list do usuário logado
		public function store(int $id){
			$game_list = new Gamelist();
			$response = $game_list->linkGame($id);

			return $response;
		}

		public function update(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {game}'));
		}

		//remove um jogo da game list do usuário {$id}
		public function delete(int $id){
			$game_list = new Gamelist();
			$response = $game_list->unlinkGame($id);

			return $response;
		}
	}
?>