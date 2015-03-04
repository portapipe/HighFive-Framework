<?php

/*! Create Documentation with this class */
class HFdocumentation{

	private $text;
	private $array;
	
	private $commentsOpen = "(\/\*!.*)";
	private $commentsClose = "(\*\/)";
	private $classes = "(class .*)(?={)";
	private $functions = "(function .*)(?={)";
	private $files = "(#File# .*)(?=)";
	private $fileName = "(###File .*)(?=###)";
	private $scriptOpen = "(<script>)(?=)";
	private $scriptClose = "(<\/script>)(?=)";
		
	function fromText($text){
		
		$this->text = $text;
		$this->createData();
		return $this;
		
	}
	
	/*! Change file from files passed */
	function fromFileArray($files=array()){
		
		$array = array();
		
		$dir = "";
		
		if(count($files)==0){
			$files = scandir(HF_LIB_DIR);
			foreach($files as $k => $f){
				$files[$k] = HF_LIB_DIR.$f;
			}
		}
		
		
		foreach($files as $k => $f){
			if(is_dir($f)){
				$files2 = scandir($f);
				
				foreach($files2 as $k2 => $f2){
					$files2[$k2] = $f.$f2;
					unset($files2[$f]);
				}
				$files = array_merge($files,$files2);
				//unset($files[$k]);
			}
		}
		
		
		//print_r($files2);die;
		foreach($files as $f){
			if($f!="."&& $f!=".."){
				if(is_dir($f)) continue;			
				if(strpos($f, "documentation.HF.php") !==false) continue;
				$array[$f] = explode("\n",htmlentities(file_get_contents($f),ENT_QUOTES)); //."\n#@#@#ENDOFFILE#@#@#";
			}
		}
		
		$this->text = $array;
		$this->createData();
		return $this;
	}
	
	
	function createData(){
		
		$text = $this->text;
		//if(is_array($text)) $text = implode("\n",$text);
		
		//if(!$this->isClass($text)) $text = "#File# ".$text;
		
		if(!is_array($text)) $text = explode("\n",$text);
		
		$array = array();
				
		$c = 0;
		$f = 0;
		
		$fileNames = "";
		
		$comment = "";
		$commentOpen = false;
		
		$script = false;
		
		foreach($text as $file => $data){
			
			$flname = $file;
			
			foreach($data as $k => $v){
	
				if($commentOpen===true){
					$comment .= "<br />".$v;
					if($this->isCommentClose($v)===true){
						$commentOpen = false;
					}
					continue;
				}
				
				
				
				if($this->isCommentOpen($v)){
					$comment = "<br />".trim($v);
					if($this->isCommentClose($v)===false) $commentOpen = true;
					continue;
				}
				

				$v = trim($v);
				
				if($this->isScriptOpen($v) && !$this->isScriptClose($v)){
					$script = true;
				}
				
				
				if($this->isFileName($v)){
					$arrayText = str_ireplace("###","",trim(str_ireplace("\n","",$v)));
					continue;
				}
				
				if($this->isClass($v)){
					$c++;
					$f = 0;
					$array[$flname]['classe'] = '<strong>'.trim(substr($v, 0, strlen($v)-1)).'</strong>';
					
					if($comment != ""){
						$array[$flname]['classe'] =  $array[$flname]['classe'].$comment;
						$comment = "";
					}
					continue;
				}
				
				if($this->isFile($v)){
					$c++;
					$f = 0;
					//$array[$c]['file'] = '<strong>'.$v.'</strong>';
					$comment = "";
					continue;
				}

				if($this->isFunction($v)){
					$f++;
					$array[$flname][$f] = ($script?'[JS] ':'').'<strong>'.trim(substr($v, 0, strlen($v)-1)).'</strong>';
					if($comment != ""){
						$array[$flname][$f] = $array[$flname][$f].$comment;
						$comment = "";
					continue;
					}
				}
				
				if($this->isScriptClose($v)){
					$script = false;
				}
				
			}
		}
		
		$this->array = $array;
		return $this;
		
	}
	
	private function isFunction($text){
		if( preg_match('/'.$this->functions.'/i', $text)) return true;
		return false;
	}
	
	private function isClass($text){
		if( preg_match('/'.$this->classes.'/i', $text)) return true;
		return false;
	}
	
	private function isCommentOpen($text){
		if( preg_match('/'.$this->commentsOpen.'/i', $text)) return true;
		return false;
	}
	
	private function isCommentClose($text){
		if( preg_match('/'.$this->commentsClose.'/i', $text)) return true;
		return false;
	}
	
	private function isFile($text){
		if( preg_match('/'.$this->files.'/i', $text)) return true;
		return false;
	}
	
	private function isFileName($text){
		if( preg_match('/'.$this->fileName.'/i', $text)) return true;
		return false;
	}
	
	private function isScriptOpen($text){
		if( preg_match('/'.$this->scriptOpen.'/i', html_entity_decode($text))) return true;
		return false;
	}
	
	private function isScriptClose($text){
		if( preg_match('/'.$this->scriptClose.'/i', html_entity_decode($text))) return true;
		return false;
	}
	
	
	
	
	function render(){
		
		$text = $this->array;
		
		
		$c = 1;
		$f = 1;
				
		foreach($text as $k=>$v){
			if(!isset($v['classe'])){
				$v['classe'] = $k;
			}

			echo '
			<div class="list-group" style="width:90%; margin-left:auto; margin-right:auto;">
				<a href="#" class="list-group-item active">
					<span class="badge">'.$k.' | '.$c.'</span>
					'.$v['classe'].'
				</a>
				';
				
				unset($v['classe']);
				unset($v['file']);
				
				foreach($v as $v2){
					$copy = strip_tags(str_ireplace("function ","",trim($v2)));
					
					$comment = "";
					
					if($this->isCommentOpen($v2)){
						
						$copy = substr($copy, 0, strpos($copy, "/*!"));
						
						$comment = substr($v2, strpos($v2, "/*!")+3, strlen($v2));
						$comment = substr($comment, 0, strlen($comment)-2);
						$v2 = substr($v2, 0, strpos($v2, "/*!"));
						
						
					}
					
					echo '<a href="#" class="list-group-item copy" id="fn'.$f.$c.rand(0,100000).'" data-clipboard-text="'.trim($copy).'" onclick="return false;">
							<span class="badge">'.$f.'</span>
							'.$v2.'
							'.trim($comment).'
						</a>
						';
					$f++;
				}
				$c++;
				$f = 1; 
			
			echo '</div>';
		}

		return $this;
		
	}
	
	
	function clipboard(){
		
	echo '
	<script src="/'.HF_ADDON_DIR.'ZeroClipboard.min.js"></script>
	
	<div id="HFclipboardOK" style="display: none; position:fixed; left:40%; right:50%; top:45%; z-index:100000; font-size:200px;">
	  <span aria-hidden="true"><span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span></span>
	</div>
	
	<script>
		classClipboard();
		
		function classClipboard(){
			var clients = new ZeroClipboard( document.getElementsByClassName("copy") );
			
			clients.on( "ready", function( readyEvent ) {
			  // alert( "ZeroClipboard SWF is ready!" );
			
			  clients.on( "aftercopy", function( event ) {
				  window.setTimeout(function() {
					    $("#HFclipboardOK").show().fadeOut(1500, function(){
					        $(this).css("display:none"); 
					    });
					}, 2000);
				  
				  document.body.style.cursor = "copy";
				  setTimeout(function(){document.body.style.cursor = "auto";}, 2000);
			    // `this` === `client`
			    // `event.target` === the element that was clicked
			    //event.target.style.display = "none";
			    //alert("Copied text to clipboard: " + event.data["text/plain"] );
			  } );
			} );
		}
	
		function HFclipboard(thisOne){
			var client = new ZeroClipboard( thisOne );
		
			client.on( "ready", function( readyEvent ) {
			  // alert( "ZeroClipboard SWF is ready!" );
			
			  client.on( "aftercopy", function( event ) {
				  window.setTimeout(function() {
					    $("#HFclipboardOK").show().fadeOut(1500, function(){
					        $(this).css("display:none"); 
					    });
					}, 2000);
				  
				  document.body.style.cursor = "copy";
				  setTimeout(function(){document.body.style.cursor = "auto";}, 2000);
				  
			    // `this` === `client`
			    // `event.target` === the element that was clicked
			    //event.target.style.display = "none";
			    //alert("Copied text to clipboard: " + event.data["text/plain"] );
			  } );
			} );
		}
	
	</script>
	
	
	';
	
	return $this;
	}
	
	
	function addClipboard($stringToCopy){
		return ' id="'.rand(0,1000000).'" class="copy" data-clipboard-text="'.$stringToCopy.'" title="'.$stringToCopy.'"';
	}
	
}
