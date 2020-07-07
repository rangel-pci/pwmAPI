<?php
	require_once('./models/Logout.php');
	Class logoutController
	{
		public function index(array $params){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {logout}'));
		}
		
		public function show(int $id){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {logout}'));
		}
		//faz 'logout', deleta o token do usuário no BD// está em 'store' porque o logout precisa
		//ser chamado com o método POST
		public function store(int $id){
			$logout = new Logout();
			$response = $logout->tryLogout();

			return $response;
		}

		public function update(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {logout}'));
		}
		
		public function delete(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {logout}'));
		}
	}
?>