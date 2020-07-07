<?php
	function is_eligible(array $data){
		$is_eligible = array('is_ok' => true, 'not_eligible' => 'the following data is missing or not valid');

		set_error_handler(function(){
			//faz com que não lance um 'notice' caso um indice de $data esteja vazio
		});

		if (mb_strlen($data['login'], 'utf8') < 6 || mb_strlen($data['login'], 'utf8') > 31
		){
			//tamaho máximo para nome = 6 - 30 caracteres

			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {login min 6 max 30 chars}';
		}
		if (mb_strlen($data['password'], 'utf8') < 6 || mb_strlen($data['password'], 'utf8') > 31
		){
			//tamaho máximo para senha = 6 - 30 caracteres

			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {password min 6 max 30 chars}';
		}
		if (preg_match('/^[~^,\.<>´`=¹²³"£\'¢¬+§!ª#$%¨&*\/{|:};°º()@[]]*$/', $data['name']) ||
			mb_strlen($data['name'], 'utf8') < 3 || mb_strlen($data['name'], 'utf8') > 31
		){
			//tamaho máximo para nome = 3 - 30 caracteres

			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {name min 3 max 30 chars}';
		}
		if (preg_match('/^[a-z~^,\.<>´`=¹²³"£\'¢¬+§!ª#$%¨&*\/{|:};°º()@[]]*$/i', $data['age']) ||
			$data['age'] < 1 || $data['age'] > 99
		){
			//idade minima = 1 máxima = 99

			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {age min 1 max 99}';
		}
		if (!preg_match('/^[a-z0-9.\-\_]+@[a-z0-9.\-]+\.[a-z]+$/i', $data['email'])){

			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {email}';
		}
		//tamanho máximo para info = 300 caracteres
		if (mb_strlen($data['info']) > 300) {
			
			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {info max 300 chars}';
		}
		//tamanho máximo para twitch, discord etc = 65 caracteres
		if (mb_strlen($data['discord']) > 55) {
			
			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {discord max 65 chars}';
		}
		if (mb_strlen($data['steam']) > 55) {
			
			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {steam max 65 chars}';
		}
		if (mb_strlen($data['origin']) > 55) {
			
			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {origin max 65 chars}';
		}
		if (mb_strlen($data['twitch']) > 55) {
			
			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {twitch max 65 chars}';
		}
		if (mb_strlen($data['psn']) > 55) {
			
			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {psn max 65 chars}';
		}
		if (mb_strlen($data['xbox']) > 55) {
			
			$is_eligible['is_ok'] = false;
			$is_eligible['not_eligible'] .= ', {xbox max 65 chars}';
		}

		restore_error_handler();

		return $is_eligible;
	}
?>