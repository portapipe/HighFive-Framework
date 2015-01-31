<?
	
define("HF_LIB_DIR", __DIR__.'/lib/');

class HF {
	
	public function __construct(){
		if(count(func_get_args())>0){
			foreach (func_get_args() as $param) {
		        $files[] = $param.".HF.php";
		    }
		}else{
			$files = scandir(HF_LIB_DIR);
		}

		foreach($files as $file) {
	   		if($file!="."&&$file!=".."){
		   		$file2 = "HF".str_replace(".HF.php", "", $file);
		   		$file3 = str_replace("HF", "", $file2);
		   		require(HF_LIB_DIR.$file);
		   		$this->{$file3} = new $file2();
		   	}	
	   	}
	}	
}

//remove this to load only the var you care about!
$GLOBALS["HF"] = new HF();
