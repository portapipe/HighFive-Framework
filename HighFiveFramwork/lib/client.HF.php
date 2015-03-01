<?php dependencies("string");
	
/*! All stuff about the client browser (ip,browser name, device type...) */
class HFclient{
	
	/*! Return the actual IP of the user, even if forwarded */
	function getIp() {
	    $ipaddress = '';
	    if ($_SERVER['HTTP_CLIENT_IP'])
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if($_SERVER['HTTP_X_FORWARDED'])
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if($_SERVER['HTTP_FORWARDED_FOR'])
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if($_SERVER['HTTP_FORWARDED'])
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if($_SERVER['REMOTE_ADDR'])
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
	
	function isLogged(){
		if(isset($_SESSION['HF_user'])) return true;
		return false;
	}
	
	function login($id,$data=""){
		if(!$this->isLogged()){
			$_SESSION['HF_user'] = HFstring::encrypt(json_encode(array('id'=>$id,'data'=>$data)));
		}
	}
	
	function logout(){
		unset($_SESSION['HF_user']);
	}

	function getLoggedId(){
		if(!$this->isLogged()) return false;
		if(!isset($_SESSION['HF_user'])) return false;
		$data = json_decode(HFstring::decrypt($_SESSION['HF_user']));
		return $data->id;
		
	}
	
	function getLoggedData(){
		if(!$this->isLogged()) return false;
		if(!isset($_SESSION['HF_user'])) return false;
		$data = json_decode(HFstring::decrypt($_SESSION['HF_user']));
		return $data->data;
	}
	
		
	
	
}