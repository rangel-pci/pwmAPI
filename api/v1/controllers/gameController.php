<?php
	require_once('./models/Game.php');

	Class gameController
	{
		public function index(array $params){
			$game = new Game();
			$response = $game->listAll($params);

			return $response;
		}

		public function show(int $id){
			$game = new Game();
			$response = $game->findById($id);

			return $response;
		}

		public function store(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {game}'));
		}

		public function update(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {game}'));
		}

		public function delete(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {game}'));
		}
	}
?>