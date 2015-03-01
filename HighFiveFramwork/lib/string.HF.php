<?php
	
/*! All about strings */
class HFstring{
	
	/*! Convert a string into an array
		Can be found in HFarray::stringToArray() too
		RETURN ARRAY */
	function stringToArray($string,$delimiter=","){
		return explode($delimiter, $string);
	}
	
	function arrayToString($array,$delimiter=","){
		return implode($delimiter, $array);
	}
	
	function arrayToJson($array){
		return json_encode($array);
	}
	
	function jsonToArray($string){
		return json_decode($string,true);
	}
	
	
	function encrypt($string) {
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, ENCRYPTION_KEY, $string, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	}
	
	/*! Returns decrypted original string */
	function decrypt($string) {
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, ENCRYPTION_KEY, base64_decode($string), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}

	
}


// DA FARE!!!! TUTTO CIO CHE E' ANCHE ARRAY VA MESSO NELLA CLASSE DEGLI ARRAY!!!