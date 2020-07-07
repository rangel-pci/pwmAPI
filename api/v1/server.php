<?php
	class Rest
	{
		public static function open($request){

			$req_method = $_SERVER['REQUEST_METHOD'];

			
			if (!isset($request['url'])) {

				http_response_code(404);
				return json_encode(array('status' => '400', 'response' => 'missing {class}'));
				exit();
			}
			//separa a url em classe e método
			$url = explode('/', $request['url']);
			
			//chama o controller de acordo com a classe especificada no link ex: 'userController'.php
			$class = ($url[0]);

			if (!file_exists('controllers/'.$class.'Controller.php')) {
				
				http_response_code(400);
				return json_encode(array('status' => '400', 'response' => 'nonexistent class {'.$class.'}'));
			}else{
				$class .= 'Controller';
				require_once('controllers/'.$class.'.php');
			}

			//tira o primeiro indice do array
			array_shift($url);

			//target pode ser um id ou uma key pra verificação de conta
			$target = 0;
			//ex: http://localhost/www/PlayWithMe/api/v1/user/{alvo}
			if (isset($url[0])) {
				$target = $url[0];
				array_shift($url);
			}

			//parametros passados no link
			$params = array();
			$params = $url;

			//pega os parametros passados
			foreach ($_REQUEST as $key => $value) {
				$params += array($key => $value);
			}
			//tira os 2 primeiros índices do array (class e target)
			array_shift($params);

			if(class_exists($class)){
				//chama a classe se existir
				//define as rotas
				
				switch ($req_method) {
					case 'GET':

						if($target == 0)

							return $results = call_user_func_array(array(new $class, 'index'), array($params));
						else
							
							return $results = call_user_func_array(array(new $class, 'show'), array(intval($target)));
						break;
					case 'POST':

						//recebe um json ex: dados de usuário a ser criado no BD
						//só recebe o json se a classe chamada for login ou user
						if($target == 0 && ($class == 'userController' || $class == 'loginController')){
							$json_data = file_get_contents('php://input');
							
							return $results = call_user_func_array(array(new $class, 'store'), array($json_data));
						}else{
							
							return $results = call_user_func_array(array(new $class, 'store'), array(intval($target)));							
						}						
						
						break;
					case 'PUT':

						if(file_get_contents('php://input') ==! null){
							//json com dados a serem inseridos no BD
							$json_data = file_get_contents('php://input');
							
							return $results = call_user_func_array(array(new $class, 'update'), array($json_data));
						}
						if($class == 'userController'){
							
							http_response_code(400);
							return json_encode(array('status'=>'400', 'response'=>'missing json {data}'));
						}
						return $results = call_user_func_array(array(new $class, 'update'), array($target));
						
						break;
					case 'DELETE':
		
							return $results = call_user_func_array(array(new $class, 'delete'), array(intval($target)));

						break;
					default:

						http_response_code(405);
						return json_encode(array('status'=>'405', 'resposne'=>'allowed methods GET, POST, PUT, DELETE'));
						break;
				}
			}
		}
	}
?>