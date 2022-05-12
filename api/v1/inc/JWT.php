<?php
	// require_once('vendor/autoload.php');
	use Lcobucci\JWT\Builder;
	use Lcobucci\JWT\Signer\Key;
	use Lcobucci\JWT\Signer\Hmac\Sha256;
	use Lcobucci\JWT\ValidationData;
	use Lcobucci\JWT\Parser;

	Class JWT
	{
		public function setToken(string $jti, int $time ,int $uid, string $uname, string $uemail, string $uimage){
		$signer = new Sha256();
		$token = (new Builder())->issuedBy(Config::$app_url) // Configures the issuer (iss claim)
		                        ->permittedFor(Config::$app_url) // Configures the audience (aud claim)
		                        ->identifiedBy($jti, true) // Configures the id (jti claim), replicating as a header item
		                        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
		                        ->canOnlyBeUsedAfter($time + 0) // Configures the time that the token can be used (nbf claim)
		                        ->expiresAt($time + 28800) // Configures the expiration time of the token (exp claim) #segundos
								// ->expiresAt($time + 2) // Configures the expiration time of the token (exp claim) #segundos
		                        ->withClaim('uid', $uid) // Configures a new claim, called "uid"
		                        ->withClaim('uname', $uname) // Configures a new claim, called "uname"
		                        ->withClaim('uemail', $uemail) // Configures a new claim, called "uemail"
								->withClaim('uimage', $uimage) // Configures a new claim, called "ueimage"
		                        ->getToken($signer, new Key(Config::$signingKey)); // Retrieves the generated token
			return $token;                    
		}

		public function isValid(object $token, string $jti){
			$data = new ValidationData();
			$data->setIssuer(Config::$app_url);
			$data->setAudience(Config::$app_url);
			$data->setId($jti);

			$data->setCurrentTime(time());
			if(!$token->validate($data)){
				return false;
			}
			if(!$token->verify((new Sha256()), Config::$signingKey)){
				return false;
			}
			return true;
		}

		public function getToken(string $string){
			$token = (new Parser())->parse($string);

			return $token;
		}
	}
?>