<? dependencies("string");

/*! All about arrays */
class HFarray{
	
	
	/*! Convert a string into an array
		Can be found in HFarray::stringToArray() too
		RETURN ARRAY */
	function stringToArray($string,$delimiter=","){
		return HFstring::stringToArray($delimiter, $string);
	}
	
	function arrayToString($array,$delimiter=","){
		return HFstring::arrayToString($delimiter, $array);
	}
	
	function arrayToJson($array){
		return HFstring::arrayToJson($array);
	}
	
	function jsonToArray($string){
		return HFstring::jsonToArray($string,true);
	}
	
	
	
}