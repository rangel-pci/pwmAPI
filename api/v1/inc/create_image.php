<?php
	function create_image(array $user_data)
	{
		//recebe uma imagem em base64 e converte em uma imagem.jpeg
		$image = $user_data['image'];
		$exploded = explode(',', $image, 2);
		$encoded = $exploded[1];

		//casso contenha 'false' significa que o usuário não alterou a imagem de perfil
		if($exploded[1] == "false"){
			header("content-type: image/jpeg");
			$image = file_get_contents($exploded[0]);
			$base64image = base64_encode($image);

			$encoded = $base64image;
		}
		
		$decoded = base64_decode($encoded);

		set_error_handler(function() { 
			//retorna 400 se a string informada não for uma base64
			http_response_code(400);
			return json_encode(array('status' => '400', 'response' => 'the data is not a base64 image'));
			// exit();
		});

		$img = imagecreatefromstring($decoded);
		
		// corrigre a orientação da imagem
		$exif = exif_read_data('data://image/jpeg;base64,' . $encoded);
		// var_dump($exif);
		if (!empty($exif['Orientation'])) {
			switch ($exif['Orientation']) {
				case 3:
					$img = imagerotate($img, 180, 0);
					break;
				
				case 6:
					$img = imagerotate($img, -90, 0);
					break;
				
				case 8:
					$img = imagerotate($img, 90, 0);
					break;
			}
		}

		restore_error_handler();
			
		$temp_dir = $_SERVER['DOCUMENT_ROOT'].'/temp/';
		$temp_name = uniqid('temp'.bin2hex(random_bytes(50))).time();
		
		imagejpeg($img, $temp_dir.$temp_name . image_type_to_extension(IMAGETYPE_JPEG));
		imagedestroy($img);

		$img_size = filesize($temp_dir.$temp_name.'.jpeg');

		//se a imagem for menor que 4mb salva em profile_image e apaga a temporária
		if ($img_size < 4194304) {
								
			$_FILES['file'] = file_get_contents($temp_dir.$temp_name.'.jpeg');

			$file_name = md5($temp_name.$user_data['login']).'.'.'jpeg';
			$dir = $_SERVER['DOCUMENT_ROOT'].'/profile_image/';

			file_put_contents($dir.$file_name, $_FILES['file']);
			unlink($temp_dir.$temp_name.'.jpeg');
			
			$file_path = Config::$app_url.'profile_image/'.$file_name;
			return $file_path;

		}else{

			unlink($temp_dir.$temp_name.'.jpeg');

			return false;
		}
	}

?>