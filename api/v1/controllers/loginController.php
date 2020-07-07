<?php
	require_once('./models/Login.php');

	Class loginController
	{
		public function index(array $params){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {login}'));
		}
		
		public function show(int $id){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {login}'));
		}
		//faz 'login', gera e retorna um token caso as credencias estejam corretas
		public function store(string $json_data){
			$login = new Login();
			$response = $login->tryLogIn($json_data);

			return $response;
		}

		public function update(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {login}'));
		}
		
		public function delete(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {login}'));
		}
	}
?>