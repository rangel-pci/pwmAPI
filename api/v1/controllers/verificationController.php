<?php
	
	require_once('./models/Verification.php');

	Class verificationController
	{
		public function index(array $params){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {verification}'));
		}

		public function show(int $id){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {verification}'));
		}

		public function store(string $json_data){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {verification}'));
		}

		public function update(string $vkey){
			//verifica um usuário de acordo com a chave recebida
			$verification = new Verification();
			$response = $verification->verify($vkey);

			return $response;
		}

		public function delete($id){
			http_response_code(501);
			return json_encode(array('status'=>'501', 'response'=>'method not implemented for {verification}'));
		}
	}

?>