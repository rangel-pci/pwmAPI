<?php
	
	require_once('dbConnect.php');

	Class Verification
	{
		public function verify(string $vkey){
			if(!isset($vkey)){

					http_response_code(400);
					return json_encode(array('status'=>'400', 'response'=>'missing {key}'));
			}

			$pdo = dbConnect();
			$sql = $pdo->prepare($query = "SELECT verified FROM user WHERE vkey = ?");
			$sql->execute([$vkey]);

			$row = array();
			$row = $sql->fetch();

			if(!empty($row)){
				if ($row['verified'] == 0) {
					
					//seta verified para 1 caso encontre o usuário e ele ainda não tenha sido verificado
					$sql = $pdo->prepare($query = "UPDATE user SET verified = ? WHERE vkey = ?");
					$sql->execute([1, $vkey]);

					http_response_code(200);
					return json_encode(array('status'=>'200', 'response'=>'your account has been successfully verified'));
				}else{

					//retorna 409 caso já tenha sido verificado
					http_response_code(409);
					return json_encode(array('status'=>'409', 'response'=>'the user has already been verified'));
				}
			}else{

				//retorna 400 caso não encontre um usuário com essa chave de verificação
				http_response_code(400);
					return json_encode(array('status'=>'400', 'response'=>'invalid key, get another verification key here {link}'));
			}

		}
	}

?>