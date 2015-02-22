<?
	
class HFdebug {
	
	/*! Print all Variables set in All the methods! */
	function allVars(){
		if(!isset(HFconfig::$DEBUGMODE)){
			echo "[Error] debug->allVars - DEBUGMODE NOT SET! Class Not Reachable";
		}else{
			if(HFconfig::$DEBUGMODE===true){
				echo print_r($GLOBALS, true);
			}else{
				echo "[Error] debug->allVars - DEBUGMODE DISABLED!";
			}
		}
	}
	
	
	
}