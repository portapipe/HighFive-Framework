<?
//include the config file for HighFive
require("HF.config.php");


function dependencies(){
	if(count(func_get_args())>0){
		$stop = "";
		foreach (func_get_args() as $param) {
		    if(!file_exists(HF_LIB_DIR.$param.".HF.php")){
			    $stop .= "<li class=\"list-group-item\">$param.HF.php 
			    			<a href=\"https://github.com/portapipe/HighFive-Framework/blob/master/HighFiveFramwork/lib/$param.HF.php\" target=\"_blank\">
			    				<button type=\"button\" class=\"btn btn-default btn-sm\">
									<span class=\"glyphicon glyphicon-screenshot\" aria-hidden=\"true\"></span>
								</button>
							</a>
						</li>";
			}
		}
		if($stop!=""){
			echo '
				<script>
				    window.jQuery || document.write(\'<script src="https:\/\/ajax.googleapis.com\/ajax\/libs\/jquery\/2.1.3\/jquery.min.js"><\/script>\');
				</script>
				<!-- Latest compiled and minified CSS -->
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
				<!-- Optional theme -->
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
				<!-- Latest compiled and minified JavaScript -->
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
			';
			echo '<div class="jumbotron text-center">
					<div class="page-header">
						<h3>Dependencies are missing in HighFive Framework<br/><small>Check the \'lib/\' folder into the HF directory</small></h3>
						<a href="https://github.com/portapipe/HighFive-Framework/tree/master/HighFiveFramwork/lib" target="_blank">
							<button class="btn btn-warning">Libs Download List</button>
						</a>
						<br/><small><i>If you\'re using a custom class it should not be in our repository</i></small>
					</div>
					
					<ul class="list-group">';
			echo $stop;
			echo "</ul></div>";	
			die;
		}
	}
}


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
