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
	
	/*! This function will print a <script> with a JS equivalent function of changeUrl(),
		needed for async url change */
	function changeUrlJs(){
		return "<script>function changeUrl(newUrl,actionOnUrlChange,historyTitle,passData){
			actionOnUrlChange = typeof actionOnUrlChange !== 'undefined' ? actionOnUrlChange : null;
			historyTitle = typeof historyTitle !== 'undefined' ? historyTitle : null;
			passData = typeof passData !== 'undefined' ? passData : null;
			window.history.pushState(passData, historyTitle, newUrl);
			window.onpopstate = function(event) {
				actionOnUrlChange
			}
		}</script>";
	}
	function redirect($link){
	
		return '
		<script language="javascript" type="text/javascript">
				window.location = "'.$link.'";
		</script>
		';
	}

}
