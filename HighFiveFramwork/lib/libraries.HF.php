<?php
	
/*! Usage $HF->libraries->bootstrap3()->fontawsome() */
class HFlibraries {
	
	/*! Bootstrap 3.3.2 AND jQuery 2.1.3 IF jQuery is Not Loaded!
		To load just one Bootstrap JS, place yours into the <head> tags!
	*/
	function bootstrap3(){
		
		echo "
		<script>
		    window.jQuery || document.write('<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js\"><\/script>');
		</script>";
		
		echo '
		
		<script>	
			var s = document.createElement("link");
			s.rel = "stylesheet";
			s.href = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css";
			$("head").append(s);
		</script>
		
		
		<script>	
			var s = document.createElement("link");
			s.rel = "stylesheet";
			s.href = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css";
			$("head").append(s);
		</script>
		
		
		<script>	
		if (typeof($.fn.modal) === \'undefined\') {
			var boostrap = document.createElement("script");
			boostrap.type = "text/javascript";
			boostrap.src = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js";
			$("head").append(boostrap);
		}
		</script>
		';
		
		return $this;
	}
	
	/*! FontAwsome 4.3.0 */
	function fontawsome(){
		echo '
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		';
		
		return $this;
	}

	/*! Pass an array of functions that you want to see ajaxified
		It should be better to put it between the tag head because it includes files
		IMPORTANT! There must be the 2 phplivex files into the addon folder of the HighFiveFramework folder!
	*/
	function phplivex($functionsArray){
		
		if(!class_exists("PHPLiveX")){
			require_once(HF_FULL_ADDON_DIR."PHPLiveX.php");
		}

		$ajaxPhpLiveXNoMoreThanThat = new PHPLiveX($functionsArray); 
		$ajaxPhpLiveXNoMoreThanThat->Run("/".HF_ADDON_DIR.'phplivex.js');
	
	}

	

}