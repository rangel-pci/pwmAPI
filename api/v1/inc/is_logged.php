<?php
	function is_logged(object $pdo){
				require_once('./inc/JWT.php');
				$header = getallheaders();

				if (!isset($header['Authorization'])) {
					http_response_code(401);
					return json_encode(array('status'=>'401', 'response'=>'login to continue, POST /login {json user and password}'));
				}

				//cria o token a partir da string recebida
				$jwt = new JWT();
				try {
					$token = $jwt->getToken($header['Authorization']);
				} catch (Exception $e) {

					http_response_code(400);
					return json_encode(array('status'=>'400', 'response'=>$e->getMessage()));
				}

				try {
					$user_id = $token->getClaim('uid');

				} catch (Exception $e) {
					
					http_response_code(400);
					return json_encode(array('status'=>'400', 'response'=>$e->getMessage().', invalid token'));
				}

				//busca o id do token no BD para verificar a veracidade
				$query = "SELECT jti FROM user_token WHERE user_id = ?";
				$sql = $pdo->prepare($query);
				$sql->execute([$user_id]);

				$jti = $sql->fetch()['jti'];
				
				if ($jti == null) {
					
					http_response_code(500);
					return json_encode(array('status' => '500', 'response' => 'internal server error, try again in a moment'));
				}
				if($jwt->isValid($token, $jti)){
					return $user_id;
				}else{
					return false;
				}
			}
?>