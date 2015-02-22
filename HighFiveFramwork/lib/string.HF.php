<?
	
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
	
	
	
}


// DA FARE!!!! TUTTO CIO CHE E' ANCHE ARRAY VA MESSO NELLA CLASSE DEGLI ARRAY!!!