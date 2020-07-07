<?php
	require_once('dbConnect.php');

	Class Logout
	{
		public function tryLogout(){
			$pdo = dbConnect();
			//verifica se o usuário está autenticado
			require_once('./inc/is_logged.php');

			//retorna o id do usuário logado
			//se a resposta não for do tipo int houve algum problema no processo
			$isLogged = is_logged($pdo); 
			if(gettype($isLogged) !== gettype(1)){
				
				http_response_code(401);
				return json_encode(array('status'=>'401', 'response'=>'invalid or expired token, login at api.playwithme/login to continue'));
			}else{
				$user_id = $isLogged;
			}
			//se o token é válido e rotornou o id do usuário, significa que ele está logado
			//e pode realizar a ação

			$query = "DELETE FROM user_token WHERE user_id = ?";
			$sql = $pdo->prepare($query);
			$sql->execute([$user_id]);

			if ($sql->rowCount() > 0) {
				
				http_response_code(200);
				return json_encode(array('status'=>'200', 'response'=>'logged out successfully'));
			}else{
				
				http_response_code(500);
				return json_encode(array('status'=>'500', 'response'=>'internal server error'));
			}
		}
	}
?>