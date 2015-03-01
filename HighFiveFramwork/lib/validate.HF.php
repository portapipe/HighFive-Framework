<?php
	
class HFvalidate{
	
	private $ip = "/^(\[0-9]{1,3})\.(\[0-9]{1,3})\.(\[0-9]{1,3})\.(\[0-9]{1,3})$/";
	
	function email($string){
		if(!filter_var($string, FILTER_VALIDATE_EMAIL)){
			return false;
		}
		return true;
	}
	
	function url($string){
		if(!filter_var($string, FILTER_VALIDATE_URL)){
			return false;
		}
		return true;
	}
	
	function ip($string){
		if(!filter_var($string, FILTER_VALIDATE_IP)){
			return false;
		}
		return true;
	}
	
	function int($number){
		if(!filter_var($number, FILTER_VALIDATE_INT)){
			return false;
		}
		return true;
	}
	
	function float($number){
		if(!filter_var($number, FILTER_VALIDATE_FLOAT)){
			return false;
		}
		return true;
	}
	
	function boolean($boolean){
		if(!filter_var($boolean, FILTER_VALIDATE_BOOLEAN)){
			return false;
		}
		return true;
	}
	
	
	
}