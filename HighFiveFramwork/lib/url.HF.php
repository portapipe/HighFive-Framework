<?php
	
class HFurl {

	function baseName(){
		return $_SERVER['SERVER_NAME'];
	}


	/*! Return the actual complete URL */
	function currentURL(){
		return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}
	
	function isLocal($string){
		if(strpos($string,"://")) return false;
		return true;
	}
	
	function noDomain(){
		if($_SERVER['REQUEST_URI'][0]=="/") return substr($_SERVER['REQUEST_URI'], 1);
		return $_SERVER['REQUEST_URI'];
	}

	function changeUrl($newUrl,$actionOnUrlChange="",$historyTitle="",$passData=""){
		echo "<script>window.history.pushState(".($passData==""?'null':$passData).", ".($historyTitle==""?'null':$historyTitle).", '$newUrl');</script>";
		echo "<script>window.onpopstate = function(event) {
					  $actionOnUrlChange
					};
			  </script>";
		return $this;
	}
	

}
