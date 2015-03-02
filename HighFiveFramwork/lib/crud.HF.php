<?php dependencies("libraries","db","file","pagination","url");

# ==================== #
# ==== AJAX PART! ==== #
# ==================== #
if(isset($_POST["_action"])){
	//echo '{"result":"ok","tableID":"'.$_POST['_tableID'].'","test":"yep"}';die; 
	//echo "HSJSHASAHJSD";die;
	//print_r($_FILES);die;
	//print_r($_POST);die;
	
	$data = array();

	foreach($_POST as $k=>$v){
		if($k[0]!="_"){
			$data[$k] = htmlspecialchars($v,ENT_QUOTES);
		}
	}
	
	foreach($_FILES as $k=>$v){
		if($k[0]!="_"){
			if($v['name']!="") $data[$k] = $v;
		}
	}
	if(count($data)==0){
		echo "No data passed on CRUD!";
	}
	
	
	$class = unserialize(base64_decode($_SESSION["_class".$_POST['_tableID']]));
	//print_r($data);die;
	//pirnt_r($class->fieldType);die;
	switch($_POST['_action']){
		case "edit":
			include_once(HF_LIB_DIR."db.HF.php");
			
			
			//looping data
			foreach($data as $chiave=>$valore){
				//Check for a particular field type
				if(isset($class->fieldType[$chiave])){
					foreach($class->fieldType[$chiave] as $k=>$v){
						if($k=='fileSelect'){
							
							include_once(HF_LIB_DIR."file.HF.php");
							$data[$chiave] = "";
							
							$hf_file = new HFfile();
							
							//delete the old file in the database
							if($_POST['_old'.$chiave]!="") $hf_file->deleteOldFile($_POST['_old'.$chiave]);
							
							$json = $hf_file->uploadFile($chiave,$v['uploadFolder'],$v['maxFileSize'],$v['allowedFiles'],$v['rewriteIfExists']);
							if(!is_array($json)){
								echo "Error edit file: ". $json;
								$stop=1;
							}else{
								$data[$chiave] = $json['fileName'];
							}
						}
						if($k=='imageSelect'){
							
							include_once(HF_LIB_DIR."file.HF.php");
							$data[$chiave] = "";
							
							$hf_file = new HFfile();
							
							//delete the old file in the database
							if($_POST['_old'.$chiave]!="") $hf_file->deleteOldFile($_POST['_old'.$chiave]);
							
							if($v['resizePixel']!=false) $hf_file->setResizeSize($v['resizePixel']);
							$json = $hf_file->uploadImage($chiave,$v['uploadFolder'],$v['maxFileSize'],$v['allowedFiles'],$v['rewriteIfExists']);
							if(!is_array($json)){
								echo "Error edit image: ". $json;
								$stop=1;
							}else{
								$data[$chiave] = $json['imageName'];
								
							}
						}
						if($k=='checkbox'){
							$data[$chiave] = implode(',', $valore);
							if($valore[0]=='"') $data[$chiave] = substr($valore, 1,strlen($valore)-1);
						}
						if($k=='password'){
							if($_POST['_old'.$chiave]!=$valore)	$data[$chiave] = md5($valore);
						}
					}
				}
			}
			
			HFdb::update($_POST['_idValue'], $_POST['_tableName'], $data);
		break;
		
		
		case "add":
						
			include_once(HF_LIB_DIR."db.HF.php");
			//looping data
			foreach($data as $chiave=>$valore){
				//Check for a particular field type
				if(isset($class->fieldType[$chiave])){
					foreach($class->fieldType[$chiave] as $k=>$v){
						if($k=='fileSelect'){
							
							include_once(HF_LIB_DIR."file.HF.php");
							$data[$chiave] = "";
							$json = HFfile::uploadFile($chiave,$v['uploadFolder'],$v['maxFileSize'],$v['allowedFiles'],$v['rewriteIfExists']);
							if(!is_array($json)){
								echo "Error edit file: ". $json;
								$stop=1;
							}else{
								$data[$chiave] = $json['fileName'];
							}
						}
						if($k=='imageSelect'){
							
							include_once(HF_LIB_DIR."file.HF.php");
							$data[$chiave] = "";
							if($v['resizePixel']!=false) HFfile::setResizeSize($v['resizePixel']);
							$json = HFfile::uploadImage($chiave,$v['uploadFolder'],$v['maxFileSize'],$v['allowedFiles'],$v['rewriteIfExists']);
							if(!is_array($json)){
								echo "Error edit image: ". $json;
								$stop=1;
							}else{
								$data[$chiave] = $json['imageName'];
								
							}
						}
						if($k=='checkbox'){
							$data[$chiave] = implode(',', $valore);
							if($valore[0]=='"') $data[$chiave] = substr($valore, 1,strlen($valore)-1);
						}
						if($k=='password'){
							$data[$chiave] = md5($valore);
						}
					}
				}
			}
			HFdb::insert($_POST['_tableName'], $data);
		break;
		
		case "remove":
			//not used but can be useful to someone
			HFdb::delete($_POST['_idValue'], $_POST['_tableName']);
		break;
		
		
		default:
			echo "default called, no good actions here: ".$_POST['_action'];
			$stop=1;
	}
	
	if(!isset($stop)) echo '{"result":"ok","tableID":"'.$_POST['_tableID'].'"}';
	die;
}




/*! THIS. IS. AWESOME!
	With just few settings you can have a complete CRUD table!
	What's CRUD? Create,Read,Update,Delete. All you need for any admin panel.
	This will use lots of HF libraries but it will save you hours of coding (or more!)
*/
class HFcrud{
	
	public $css = false;
	public $tableName = "";
	public $data = array();
	public $titles = array();
	public $sql = "";
	
	public $add = false;
	public $view = false;
	public $edit = false;
	public $delete = false;
	
	public $addPage = 'add.php';
	public $viewPage = 'view.php?id=$id';
	public $editPage = 'edit.php?id=$id';
	public $deletePage = 'delete.php?id=$id';
	
	public $php = "";
	public $fieldType = array();
	public $hide = array();
	public $hideAll = array();
	public $id = "id";
	public $disabled = array();
	
	public $orderByField = "id";
	public $orderDirection = "DESC";
	
	public $ajaxCall = false;
	public $ajaxify = false;
	
	public $genID = 1;
	public $page = 1;
	public $resultsPerPage = 10;
	
	public $justReturn = false;
	
	/*! Needed to have a clear settings, instead you can have a setting you don't want so use it at the first beginning to inizialize the settings */
	function init(){
		$this->tableName = "";
		
		$this->data = array();
		$this->titles = array();
		$this->sql = "";
	
		$this->add = false;
		$this->view = false;
		$this->edit = false;
		$this->delete = false;
	
		$this->viewPage = 'view.php?id=$id';
		$this->editPage = 'edit.php?id=$id';
		$this->deletePage = 'delete.php?id=$id';
	
		$this->php = "";
		$this->fieldType = array();
		$this->hide = array();
		$this->hideAll = array();
		$this->id = "id";
	
		$this->orderByField = "id";
		$this->orderDirection = "DESC";
	
		$this->ajaxify = false;
		
		$this->page = 1;
		$this->resultsPerPage = 10;
		
		$this->justReturn = false;
		
		return $this;
	}
	
	
	/*! Set the table name for the operation you're going to do */
	function setTable($tableName){
		$this->tableName = $tableName;
		return $this;
	}
	
	/*! THIS WILL NOT! MAKE THE ASYNC TABLE TO BE UPDATED PROPERLY AFTER adding/editing/deleting stuff! USE setSqlData instead!
		Set the data of the table, an assoc array will do the trick.
		Want a sure result from db? Use the sqlToArray() and pass it as param 1
		data MUST be an array with an index for each row, so the system knows what is what
		ex. array([0]=>array([id]=>1,[name]=>"Awsome me"),[1]=>array(...));
	*/
	function setData($data,$rewriteTitles=true){
		if(!is_array($data)){ echo "The HFcrud::setData() parameter MUST be an assoc array with indexes for each row! Read the docs!"; die; }
		$this->data = $data;
		if($rewriteTitles){
			//first layer
			foreach($data as $k=>$v){
				//The data layer
				foreach($v as $kt=>$vt){
					//check for titles stored
					$titles[$kt] = $kt;
					if(isset($this->titles[$kt])) $titles[$kt] = $this->titles[$kt];
				}
			}
		}
		$this->titles = $titles;
		return $this;		
	}
	
	/*! Set the data of the table with a simple SELECT query.
		THIS IS NEEDED FOR ASYNC STUFF! Or the table will not refresh properly after adding/editing/deleting stuff!
	*/
	function setSqlData($sql){
		$this->sql = $sql;
		$data = HFdb::sqlToArray($sql);
		$this->data = $data;
		foreach($data as $v){
			foreach($v as $k=>$v){
				$titles[$k] = $k;
				if(isset($this->titles[$k])) $titles[$k] = $this->titles[$k];
			}
		}
		$this->titles = $titles;
		return $this;		
	}
	
	/*! This will be used instead of table row names.
		Titles passed must be an array of titles from the first to the last you're gonna pass as data or it will mess all up!
	*/
	function setTitle($fieldNameOrArray,$newTitle=""){
		$titles = $this->titles;
		if(!is_array($fieldNameOrArray)){
			$titles[$fieldNameOrArray] = $newTitle;
		}else{
			foreach($fieldNameOrArray as $k=>$v){
				$titles[$k] = $v;
			}
		}
		$this->titles = $titles;
		return $this;	
	}
	
	/*! Enable the add icon on every row */
	function add(){
		$this->add = true;
		return $this;
	}
	/*! Enable the view icon on every row */
	function view(){
		$this->view = true;
		return $this;
	}
	/*! Enable the edit icon on every row */
	function edit(){
		$this->edit = true;
		return $this;
	}
	/*! Enable the delete icon on every row */
	function delete(){
		$this->delete = true;
		return $this;
	}
	
	/*! Pass the page name WITH the $id field! (it will be replaced by the setId() field you've choosen, "id" by default)
	*/
	function setAddPage($page){
		$this->addPage = $page;
		return $this;
	}
	/*! Pass the page name WITH the $id field! (it will be replaced by the setId() field you've choosen, "id" by default)
	*/
	function setViewPage($page){
		$this->viewPage = $page;
		return $this;
	}
	/*! Pass the page name WITH the $id field! (it will be replaced by the setId() field you've choosen, "id" by default)
	*/
	function setEditPage($page){
		$this->editPage = $page;
		return $this;
	}
	/*! Pass the page name WITH the $id field! (it will be replaced by the setId() field you've choosen, "id" by default)
	*/
	function setDeletePage($page){
		$this->deletePage = $page;
		return $this;
	}
	
	
	/*! Set a PHP operation to be done for the $var of the field passed as first parameter.
		Write the code into a single quote as you write it in a php file. It will be parsed as regula php code.
		The value of the field will be catch into the code if "$value" is used!
		ex. setPhp('active','if($value==1){$value = "Active";}else{$value="Non Active";}'); //look at the single quotes! The double quote will mess with the $value!
	 */
	function setPhp($field,$code=""){
		$array=$this->php;
		if(is_array($field)){
			foreach($field as $k=>$v){
				$array[$k] = $v;
			}
		}else{
			$array[$field] = $code;
		}
		$this->php = $array;
		return $this;
	}
	
	function justReturn(){
		$this->justReturn = true;
		return $this;
	}
	
	/*! Just pass the field you want to hide from the crud, veeeery simple!
		Array is ok too :) */
	function hide($field){
		$array=$this->hide;
		if(!is_array($field)){
			$array[$field] = $field;
		}else{
			foreach($field as $v){
				$array[$v] = $v;
			}
		}
		
		$this->hide = $array;
		return $this;
	}
	
	/*! Just pass the field you want to hide from All the part of the crud, veeeery simple!
		Array is ok too :) */
	function hideAll($field){
		$array=$this->hideAll;
		if(!is_array($field)){
			$array[$field] = $field;
		}else{
			foreach($field as $v){
				$array[$v] = $v;
			}
		}
		
		$this->hideAll = $array;
		return $this;
	}
	
	/*! Just pass the field you want to disable from the crud, veeeery simple!
		Array is ok too :) */
	function disabled($field){
		$array=$this->disabled;
		if(!is_array($field)){
			$array[$field] = $field;
		}else{
			foreach($field as $v){
				$array[$v] = $v;
			}
		}
		
		$this->disabled = $array;
		return $this;
	}
	
	
	/*!---------------------------------------FIELDS TYPE */
	
	function boolean($fieldName){
		$array = $this->fieldType;
		if(is_array($fieldName)){
			foreach($fieldName as $v) $array[$v]['boolean'] = $v;
		}else{
			$array[$fieldName]['boolean'] = $fieldName;
		}
		$this->fieldType = $array;
		return $this;
	}
	function textArea($fieldName){
		$array = $this->fieldType;
		if(is_array($fieldName)){
			foreach($fieldName as $v) $array[$v]['textArea'] = $v;
		}else{
			$array[$fieldName]['textArea'] = $fieldName;
		}
		$this->fieldType = $array;
		return $this;
	}
	function password($fieldName){
		$array = $this->fieldType;
		if(is_array($fieldName)){
			foreach($fieldName as $v) $array[$v]['password'] = $v;
		}else{
			$array[$fieldName]['password'] = $fieldName;
		}
		$this->fieldType = $array;
		return $this;
	}
	/*! Dropdown with options, key will be values passed to the database, values the one you'll see in the select's options */
	function select($fieldName,$values=array()){
		$array = $this->fieldType;
		$array[$fieldName]['select'] = $values;
		$this->fieldType = $array;
		return $this;
	}
	/*! Multiple Checkbox (don't use it for boolean!), key will be values passed to the database, values the one you'll see as text */
	function checkbox($fieldName,$values=array()){
		$array = $this->fieldType;
		$array[$fieldName]['checkbox'] = $values;
		$this->fieldType = $array;
		return $this;
	}
	/*! Multiple Radio button, key will be values passed to the database, values the one you'll see as text */
	function radio($fieldName,$values=array()){
		$array = $this->fieldType;
		$array[$fieldName]['radio'] = $values;
		$this->fieldType = $array;
		return $this;
	}
	function fileSelect($fieldName,$uploadFolder="upload",$maxFileSize="2000",$allowedFiles=array("pdf","doc","txt"),$overwrite=false){
		$array = $this->fieldType;
		$array[$fieldName]['fileSelect'] = array("uploadFolder"=>$uploadFolder,
												 "maxFileSize"=>$maxFileSize,
												 "allowedFiles"=>$allowedFiles,
												 "rewriteIfExists"=>$overwrite);
		$this->fieldType = $array;
		return $this;
	}
	function imageSelect($fieldName,$uploadFolder="upload",$maxFileSize="5000",$allowedFiles=array("jpg","jpeg","gif","png"),$resizeInPixel=false,$overwrite=false){
		$array = $this->fieldType;
		$array[$fieldName]['imageSelect'] = array("uploadFolder"=>$uploadFolder,
												 "maxFileSize"=>$maxFileSize,
												 "allowedFiles"=>$allowedFiles,
												 "resizePixel"=>$resizeInPixel,
												 "rewriteIfExists"=>$overwrite);
		$this->fieldType = $array;
		return $this;
	}
	
	/* !-------------------------------FIELD TYPE END */
	
	
	/*! Set the id field, necessary for the view/edit/delete buttons.
		The system MUST know which is your unique id field!
		Default field name is "id" . CASE INSENSITIVE! */
	function idField($idFieldName){
		$this->id = strtolower($idFieldName);
		return $this;
	}
	
	
	function orderBy($field,$ascOrDesc="DESC"){
		$this->orderByField = $field;
		$this->orderDirection = $ascOrDesc;
		return $this;
	}
	
	/*! Set the pagination actual page */
	function setPage($num){
		$this->page = $num;
		return $this;
	}
	
	function setResultsPerPage($num){
		$this->resultsPerPage = $num;
		return $this;
	}
	
	function ajaxify(){
		if(!is_file(HF_FULL_ADDON_DIR."phplivex.js")){ echo "You choose to use an async method BUT you need PHPLiveX on the addon folder into the HighFiveFramework directory.<br>
								   Add 'phplivex.js' AND 'PHPLiveX.php' files into '".HF_ADDON_DIR."' that you can find <a href=\"http://www.phplivex.com/downloads.php\" target=\"_blank\">HERE</a>";die;}
		$this->ajaxify = true;
		return $this;
	}
	
	
	function css(){
		
		global $HF;
		
		$this->css = true;
		
		$HF->libraries->bootstrap3();
		
		
		$output ="
		    <style type=\"text/css\">
		    @media only screen and (max-width: 800px) {
		    
		    /* Force table to not be like tables anymore */
			.no-more-tables table, 
			.no-more-tables thead, 
			.no-more-tables tbody, 
			.no-more-tables th, 
			.no-more-tables td, 
			.no-more-tables tr { 
				display: block; 
			}
		 
			/* Hide table headers (but not display: none;, for accessibility) */
			.no-more-tables thead tr { 
				position: absolute;
				top: -9999px;
				left: -9999px;
			}
		 
			.no-more-tables tr { border: 1px solid #ccc; }
		 
			.no-more-tables td { 
			
				border: none;
				border-bottom: 1px solid #eee; 
				position: relative;
				padding-left: 50%; 
				white-space: normal;
				text-align:left;
			}
			
			.no-more-tables td { 
				padding-left: 50% !important;
			}
		 
			.no-more-tables td:before { 
				/* Now like a table header */
				position: absolute;
				/* Top/left values mimic padding */
				top: 6px;
				left: 6px;
				width: 45%; 
				padding-right: 10px; 
				white-space: nowrap;
				text-align:left;
				font-weight: bold;
			}
		 
			/*
			Label the data
			*/
			.no-more-tables td:before { content: attr(data-title); }
		}
		    </style>
		    
		    
		    
			<script type=\"text/javascript\">  
			function sendHFForm(form){  
			    return PLX.Submit(form, { 
			        \"preloader\":\"pr\",  
			        \"onFinish\": function(response){
			        	//alert(response);
				        if(response[0] == '{'){
					        var jsons = jQuery.parseJSON(response);
					        if(jsons.result == 'ok'){
						        reload(jsons.tableID,{target:'table'+jsons.tableID,preloader:'pr'});
						    }
						}else{
							if(response==''){
								//alert('Something was wrong with the async upload');
								//location.reload();
							}else{
								alert(response);
							}
						}
			        }  
			    });  
			}  
			</script>  
		    ";
		    
		    echo $output;
		    if($this->ajaxify === true && !$this->ajaxCall) $this->async();
			return $this;
	}
	
	
	function async(){

		if(!$this->ajaxCall){
			$this->ajaxCall = true;
			$this->ajaxify = true;
			
			
			
			
			function reload($tableID,$classThis=""){
				$class = unserialize(base64_decode($_SESSION["_class".$tableID]));
				return $class->generate(false);
			}
			function setPage($tableID,$page){
				$class = unserialize(base64_decode($_SESSION["_class".$tableID]));
				$class->page = $page;
				return $class->setPage($page)->generate(false);
			}
			function deleteRow($id,$tableName,$tableID){
				$class = unserialize(base64_decode($_SESSION["_class".$tableID]));
				HFdb::delete($id,$tableName);
				//Deleting files and images if loaded
				foreach($class->titles as $k=>$v){
					if(isset($class->fieldType[$k]['fileSelect']) || isset($class->fieldType[$k]['imageSelect'])){
						$dir = $class->fieldType[$k]['fileSelect']['uploadFolder'];
						if($dir=="") $dir = $class->fieldType[$k]['imageSelect']['uploadFolder'];

						foreach($class->data as $fk=>$fv){
							//Actual delete
							if($fv[$class->id]==$id){
								if($fk==$k) HFfile::deleteFile($fv[$k],$dir);
							}
						}
					}
				}
				
				
				return $class->generate(false);
			}
			function orderBy($column,$tableID){
				$class = unserialize(base64_decode($_SESSION["_class".$tableID]));
				$order = "ASC";
				if($class->orderDirection=="ASC"&&$class->orderByField==$column){
					$order = "DESC";
				}
				return $class->orderBy($column,$order)->generate(false);
			}
			
			$functionToBeAjaxified = array(
				'reload',
				'setPage',
				'deleteRow',
				'orderBy'
			);
			
			if(!class_exists("PHPLiveX")){
				require_once(HF_FULL_ADDON_DIR."PHPLiveX.php");
			}

			$ajaxHF = new PHPLiveX($functionToBeAjaxified); 
			$ajaxHF->Run("/".HF_ADDON_DIR.'phplivex.js');
			
			return $this;
		}
	
	}
	
	
	/*! Generate (echo and return both) the table.
		Pass false as argument to not echo it, just return */
	function generate($newTable = true,$class = ""){

		if($this->css===false) $this->css();
	    if($this->ajaxify === true && !$this->ajaxCall) $this->async();
		if($this->ajaxify === true && $this->tableName == ""){ echo "YOU MUST TO SET THE setTable() TABLE NAME! The system must know where to update/delete/create the new data using async stuff!"; die;}
		
		# ==================== #
		# ==== PAGINATION ==== #
		# ==================== #
		$pagination = new HFpagination();
		$pag = $pagination->setPage($this->page)
						  ->setResultsPerPage($this->resultsPerPage)
						  ->setSql($this->sql)->setPageLink('onclick="setPage('.$this->genID.',$page,{target:\'table'.$this->genID.'\',preloader:\'pr\'}); return false;"');
		// /pagination

		if(!$newTable){
			if($this->sql == ""){
				if($this->tableName==""){
					echo "HEY! THE ASYNC TABLE CAN'T UPDATE PROPERLY BECAUSE YOU DIDN'T USE setSqlData() SO USE IT!";
					return "HEY! THE ASYNC TABLE CAN'T UPDATE PROPERLY BECAUSE YOU DIDN'T USE setSqlData() SO USE IT!";
					die;
				}else{
					$this->data = array();
				}
			}else{
				$this->setSqlData($this->sql);
			}
		}
		
		$HFdb = new HFdb();

		if(count($this->data)==0){
			if($this->tableName!=""){
				$array = $HFdb->sqlToArray("SELECT * FROM ".$this->tableName);
				$this->sql = "SELECT * FROM ".$this->tableName;
				$pag->setSql($this->sql);
				if(count($array)>0){
					$this->setData($array);
				}else{
					$array2 = $HFdb->sqlToArray("SHOW COLUMNS FROM ".$this->tableName);
					foreach($array2 as $v){
						$titles[$v['Field']] = $v['Field'];
						$this->setTitle($titles);
					}
					
				}
			}else{
				echo "HEY! You want to make an HFcrud table without passing data or choosing a database table!<br>Choose a table with HFcrud::tableName() or pass an array of data indexed with HFcrud::setData()";
				die;
			}
		}
		
		$array = $this->data;
		$titles = $this->titles;
		$phpcode = $this->php;
		$hide = $this->hide;
		$disabled = $this->disabled;

		/* Service Vars */
		$modalID = $this->genID;
		$ktr = "";
		$tits = '';
		$dats = '';

		$fieldType = $this->fieldType;

		$ogTitles = $titles;
		
		$idField = $this->id;
		
		//HideAll part, unset all that will be hidden everywhere
		foreach($array as $k1=>$v1){
			foreach($v1 as $k2=>$v2){
				if(in_array($k2, $this->hideAll)){
					unset($array[$k1][$k2]);
					unset($titles[$k2]);
					unset($ogArray[$k1][$k2]);
					unset($ogTitles[$k2]);
					unset($fieldType[$k2]);
				}
			}
		}
		
		//Starting check for order and recreate an ordered array
		foreach($array as $k=>$v){
			
			if(isset($phpcode[$this->orderByField])){
				$value = $v[$this->orderByField];
				eval($phpcode[$this->orderByField]);
				$orderValue[$k] = $value;
			}else{
				$orderValue[$k] = $v[$this->orderByField];
			}
			
		}
		
		//Ordering the array from the user's indication
		if(count($array)>0) array_multisort($orderValue,($this->orderDirection=="ASC"?SORT_ASC:SORT_DESC),$array);
		
		//saving the original array BUT ordered right
		$ogArray = $array;
		
		
		//Set Custom Title
		foreach($titles as $k=>$t){
			if(isset($hide[$k])) continue;
			if(strtolower($k)=="tools") continue;
			//Stuff to order the colums based on the column you click
			$tits.='<th onclick="orderBy(\''.$k.'\','.$this->genID.',{target:\'table'.$this->genID.'\',preloader:\'pr\'})" style="cursor:row-resize;">';
			//Write the value
			$tits.="$t";
			//check if this is the order field and print a simbol
			if($this->orderByField==$k){
				$tits.=' <span class="glyphicon glyphicon-chevron-'.($this->orderDirection=="ASC"?'down':'up').' pull-right" aria-hidden="true"></span>';
			}
			//close the table head
			$tits.="</th>";
			
			
		}
		
		if($this->view||$this->edit||$this->delete){
			if(isset($titles['tools'])){
				$tits.="<th>".$titles['tools']."</th>";
			}else{
				$tits.='<th><center><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span></center></th>';
			}
			$tools = true;
		}else{
			unset($titles['tools']);
		}
		
		
		//CUTTING DATA BASED ON PAGINATION LIMITS
		$array = array_splice($array,$pag->getLimit(),$pag->resultsPerPage);
		$ogArray = array_splice($ogArray,$pag->getLimit(),$pag->resultsPerPage);
		
		foreach($array as $ktr=>$tr){
			$dats.= "<tr>";
			$id = 0;
			$modalID = $this->genID.$ktr;
			
			foreach($tr as $k=>$v){
				
				//check if a user want to hide the field
				if(isset($hide[$k])) continue;
				if($idField == strtolower($k)) $id = $v;
				
				$dats.= "<td data-title=\"".$titles[$k]."\">";
				
				//Check if the value to be written in the table has setPhp() set to it
				if(isset($phpcode[$k])){
					$value = $v;
					eval($phpcode[$k]);
					$dats .= nl2br($value);
				}else{
					//Check if passed value is an image or a file, if not is printed as is
					if(isset($fieldType[$k]["imageSelect"])&&$v!=""){
						if(HFurl::isLocal($v)){
							$dats.='<img src="'.$fieldType[$k]['imageSelect']['uploadFolder'].($fieldType[$k]['imageSelect']['uploadFolder'][strlen($fieldType[$k]['imageSelect']['uploadFolder'])-1]!="/"?"/":"").$v.'"
									 alt="" style="max-height:200px">';
						}else{
							$dats.='<img src="'.$v.'" alt="" style="max-height:200px">';
						}
					}elseif(isset($fieldType[$k]["fileSelect"])&&$v!=""){
						if(HFurl::isLocal($v)){
							$dats.='<a href="http://'.$_SERVER['HTTP_HOST'].'/'.$fieldType[$k]['fileSelect']['uploadFolder'].($fieldType[$k]['fileSelect']['uploadFolder'][strlen($fieldType[$k]['fileSelect']['uploadFolder'])-1]!="/"?"/":"").$v.'">'.$v.'</a>';
						}else{
							$dats.='<a href="'.$v.'">'.(strlen($v)>20? substr($v, 0,18)."...".substr($v,strlen($v)-15,strlen($v)):$v).'</a>';
						}
					}else{
						$dats.=nl2br($v);
					}
				}
				
				$dats .= "</td>";	
			}
			
			//Creating the tool stuff
			if($this->ajaxify === false){
				if($tools) $dats.='<td data-title="'.(isset($titles['tools'])?$titles['tools']:"Tools").'" class="text-center"><div class="btn-group" role="group" aria-label="...">';
				if($this->view) $dats.='<a href="'.eval('return "'.$this->viewPage.'";').'"><button type="button" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button></a>';
				if($this->edit) $dats.='<a href="'.eval('return "'.$this->editPage.'";').'"><button type="button" class="btn btn-default"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a>';
				if($this->delete) $dats.='<a href="'.eval('return "'.$this->deletePage.'";').'"><button type="button" class="btn btn-default"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></a>';
			}else{
				if($tools) $dats.='<td data-title="'.(isset($titles['tools'])?$titles['tools']:"Tools").'" class="text-center"><div class="btn-group" role="group" aria-label="...">';
				if($this->view) $dats.='<button type="button" class="btn btn-default" data-toggle="modal" data-target="#view'.$modalID.'"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>';
				if($this->edit) $dats.='<button type="button" class="btn btn-default" data-toggle="modal" data-target="#edit'.$modalID.'"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>';
				if($this->delete) $dats.='<button type="button" class="btn btn-default" onclick="if(confirm(\''.LANG_DELETE_CONFIRM.'\'))deleteRow('.$tr[$idField].',\''.$this->tableName.'\','.$this->genID.',{target:\'table'.$this->genID.'\',preloader:\'pr\'});"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
			}
			if($tools) $dats.="</div></td>";	


			$dats.= '</tr>';
			
			
			
			
			
			
		if($this->ajaxify === true){
			# ==================== #
			# ==== VIEW MODAL ==== #
			# ==================== #

			
			$dats.= '
			
			<!-- Modal View -->
			<div class="modal fade" id="view'.$modalID.'" tabindex="-1" role="dialog" aria-labelledby="viewModal'.$modalID.'" aria-hidden="true">
			  <div class="modal-dialog modal-lg">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="viewModal'.$modalID.'">'.LANG_VIEW.'</h4>
			      </div>
			      <div class="modal-body">
			      
			        <div class="panel panel-default">
					  <!-- Default panel contents -->
					  
					  ';
							
				foreach($ogTitles as $k=>$v){
					if($k=="tools") continue;
					if(in_array($k, $this->hideAll)) continue;
					
					$dats.='<div class="panel-heading">'.(isset($titles[$k])?$titles[$k]:$k).'</div>';
					
					if(isset($phpcode[$k])){
						$value = $ogArray[$ktr][$k];
						eval($phpcode[$k]);
						$value = $value;
					}else{
						$value =$ogArray[$ktr][$k];
					}
					if(!isset($fieldType[$k])){
							$dats.='<div class="panel-body"><p>'.nl2br($value).'</p></div>';
					}else{
						foreach($fieldType[$k] as $kf=>$vf){
							$dats.='<div class="panel-body">';
							switch($kf){
								case "imageSelect":
									if($value!="") $dats.='<img src="'.$vf['uploadFolder'].($vf['uploadFolder'][strlen($vf['uploadFolder'])-1]!="/"?"/":"").$value.'"
									 alt="" style="max-height:300px">';
								break;
								case "fileSelect":
									if($value!="") $dats.='<a href="http://'.$_SERVER['HTTP_HOST'].'/'.$fieldType[$k]['fileSelect']['uploadFolder'].($fieldType[$k]['fileSelect']['uploadFolder'][strlen($fieldType[$k]['fileSelect']['uploadFolder'])-1]!="/"?"/":"").$value.'">'.$value.'</a>';
								break;
								default:
									$dats.='<div class="panel-body"><p>'.nl2br($value).'</p></div>';
							}
							$dats.='</div>';
						}
					}
				}
			
				
			$dats .= '		
					</div>
					
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">'.LANG_CLOSE.'</button>
			      </div>
			    </div>
			  </div>
			</div>
			';
			
			
			# ======================= #
			# ==== 	 EDIT MODAL  ==== #
			# ======================= #

			$dats.= '
			
			<!-- Modal Edit -->
			<div class="modal fade" id="edit'.$modalID.'" tabindex="-1" role="dialog" aria-labelledby="editModal'.$modalID.'" aria-hidden="true">
			  <div class="modal-dialog modal-lg">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="editModal'.$modalID.'">'.LANG_EDIT.'</h4>
			      </div>
			      <div class="modal-body">
			      
			        <div class="panel panel-default">
					  <!-- Default panel contents -->
					  <form id="formEdit'.$modalID.'" action="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" method="POST" onsubmit="return sendHFForm(this);" enctype="multipart/form-data">
					  <input type="hidden" name="_action" value="edit">
					  <input type="hidden" name="_class" value="'.$dataToPost.'">
					  <input type="hidden" name="_tableID" value="'.$this->genID.'">
					  <input type="hidden" name="_tableName" value="'.$this->tableName.'">
					  <input type="hidden" name="_idField" value="'.$this->id.'">
					  <input type="hidden" name="_idValue" value="'.$ogArray[$ktr][$this->id].'">

					  ';
				
				
				/* !FieldType Edit */	 
				
				foreach($ogTitles as $k=>$v){
					if($k=="tools") continue;
					if(in_array($k, $this->hideAll)) continue;
					
					$dats.='<div class="panel-heading">'.(isset($titles[$k])?$titles[$k]:$k).'</div>
					';
					
					if(isset($phpcode[$k])){
						$value = $ogArray[$ktr][$k];
						eval($phpcode[$k]);
						$value = $value;
					}else{
						$value =$ogArray[$ktr][$k];
					}
					
					if($k == $this->id){
						$dats.='<div class="panel-body"><p>'.$value.'</p></div>';
					}else{
					
						$dats.='<div class="form-group">
									<div class="panel-body">
									';
							
						if(!isset($fieldType[$k])){
							$dats .= '<p><input type="text" class="form-control" name="'.$k.'" value="'.$value.'" style="width:100%" '.($disabled[$k]?'readonly':'').'></p>';
						}else{
							
							foreach($fieldType[$k] as $kf=>$vf){
								switch($kf){
									case "boolean":
										$dats .= '
										<div class="radio" '.($disabled[$k]?'disabled="disabled"':'').'>
											<label>
												<input type="radio" name="'.$k.'" value="1" '.($ogArray[$ktr][$k]==1?'checked':'').'> '.LANG_YES.'
											</label>
											<br>
											<label>
												<input type="radio" name="'.$k.'" value="0" '.($ogArray[$ktr][$k]==0?'checked':'').'> '.LANG_NO.'
											</label>
										</div>
										';
									break;
									case "textArea":
										$dats .= '
											<textarea class="form-control" rows="10" name="'.$k.'" '.($disabled[$k]?'readonly':'').'>'
												.stripslashes( $ogArray[$ktr][$k] ).
											'</textarea>
											';
									break;
									case "password":
										$dats .= '
											<input type="password" class="form-control" name="'.$k.'" value="'.$ogArray[$ktr][$k].'" style="width:100%" '.($disabled[$k]?'disabled':'').'>
											<input type="hidden" name="_old'.$k.'" value="'.$ogArray[$ktr][$k].'">
											';
									break;
									case "select":
										$dats .= '<select class="form-control" name="'.$k.'" '.($disabled[$k]?'disabled':'').'>';
										
										foreach($vf as $optk=>$optv){
											$dats .= '<option class="form-control" value="'.$optk.'" '.($ogArray[$ktr][$k] == $optk?'selected':'').'>'.$optv.'</option>';
										}
										$dats .= '</select>';
									break;
									case "checkbox":
										$data = explode(",", $ogArray[$ktr][$k]);
										foreach($vf as $optk=>$optv){
											$dats .= '<div class="checkbox '.($disabled[$k]?'disabled':'').'"><label><input type="checkbox" name="'.$k.'[]" value="'.$optk.'" '.(in_array($optk,$data)?'checked="checked"':'').'> '.$optv.'</label></div>';
										}
									break;
									case "radio":
										foreach($vf as $optk=>$optv){
											$dats .= '<div class="radio"><label><input type="radio" name="'.$k.'" id="radio'.$optk.$optv.$k.'radio" value="'.$optk.'" '.($ogArray[$ktr][$k] == $optk?'checked="checked"':'').' '.($disabled[$k]?'disabled':'').'> '.$optv.'</label></div>';
										}
									break;
									case "fileSelect":
										$dats .= '
											<input type="file" class="form-control" name="'.$k.'" style="width:100%" '.($disabled[$k]?'readonly':'').'/>
											<input type="hidden" name="_old'.$k.'" value="'.$ogArray[$ktr][$k].'" >
											';
									break;
									case "imageSelect":
										$dats .= '
											<input type="file" class="form-control" name="'.$k.'" style="width:100%" '.($disabled[$k]?'readonly':'').'/>
											<input type="hidden" name="_old'.$k.'" value="'.$ogArray[$ktr][$k].'" >
											';
									break;
									default:
										echo "";
								}
							}
							
						}
						
						$dats .= '</div></div>';
					}
				}
			
				
			$dats .= '		
					</div>
					
			      </div>
			      <div class="modal-footer">
			        <!--
			        	<button type="submit" class="btn btn-warning">'.LANG_CLOSE.'</button>
			        	<button type="button" class="btn btn-success" data-dismiss="modal" onclick="return sendHFForm(document.getElementById(\'formEdit'.$modalID.'\'));">'.LANG_SAVE.'</button>
			        -->
			        <button type="button" class="btn btn-default" data-dismiss="modal">'.LANG_CLOSE.'</button>
			        <button type="submit" class="btn btn-success">'.LANG_SAVE.'</button>
			      </div>
			      
				 </form>
			    </div>
			  </div>
			</div>
			';
			
			}
		}
		# =================== #
		# ==== ADD MODAL ==== #
		# =================== #

		$add = '';
		if($this->add===true){
			
			if($this->ajaxify===true){

				$add = '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#add'.$modalID.'">'.LANG_ADD.'</button>';
				$dats.= '
				
				<!-- Modal Add -->
				<div class="modal fade" id="add'.$modalID.'" tabindex="-1" role="dialog" aria-labelledby="addModal'.$modalID.'" aria-hidden="true">
				  <div class="modal-dialog modal-lg">
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title" id="addModal'.$modalID.'">'.LANG_ADD.'</h4>
				      </div>
				      <div class="modal-body">
			      
			        <div class="panel panel-default">
					  <!-- Default panel contents -->
					  <form id="formAdd'.$modalID.'" action="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" method="post" onsubmit="return sendHFForm(this);" enctype="multipart/form-data">
					  <input type="hidden" name="_action" value="add">
					  <input type="hidden" name="_tableID" value="'.$this->genID.'">
					  <input type="hidden" name="_tableName" value="'.$this->tableName.'">
					  <input type="hidden" name="_idField" value="'.$this->id.'">
					  
					  ';
				
				
				/* !FieldType Add */	 
				
				foreach($ogTitles as $k=>$v){
					if($k=="tools") continue;
					if($k==$this->id) continue;
					if(in_array($k, $this->hideAll)) continue;
					
					$dats.='<div class="panel-heading">'.(isset($titles[$k])?$titles[$k]:$k).'</div>
					';
					
					if(isset($phpcode[$k])){
						$value = $ogArray[$ktr][$k];
						eval($phpcode[$k]);
						$value = $value;
					}else{
						$value =$ogArray[$ktr][$k];
					}
					
					if($k == $this->id){
						$dats.='<div class="panel-body"><p>'.$value.'</p></div>';
					}else{
					
						$dats.='<div class="form-group">
									<div class="panel-body">
									';
							
						if(!isset($fieldType[$k])){
							$dats .= '<p><input type="text" class="form-control" name="'.$k.'" value="" style="width:100%" '.($disabled[$k]?'readonly':'').'></p>';
						}else{
							
							foreach($fieldType[$k] as $kf=>$vf){
								switch($kf){
									case "boolean":
										$dats .= '
										<div class="radio" '.($disabled[$k]?'disabled="disabled"':'').'>
											<label>
												<input type="radio" name="'.$k.'" value="1"> '.LANG_YES.'
												<br>
												<input type="radio" name="'.$k.'" value="0"> '.LANG_NO.'
											</label>
										</div>
										';
									break;
									case "textArea":
										$dats .= '
											<textarea class="form-control" rows="10" name="'.$k.'" '.($disabled[$k]?'readonly':'').'></textarea>
											';
									break;
									case "password":
										$dats .= '
											<input type="password" class="form-control" name="'.$k.'" style="width:100%" '.($disabled[$k]?'readonly':'').'>
											<input type="hidden" name="_old'.$k.'" value="'.$ogArray[$ktr][$k].'">
											';
									break;
									case "select":
										$dats .= '<select class="form-control" name="'.$k.'" '.($disabled[$k]?'disabled':'').'>';
										
										foreach($vf as $optk=>$optv){
											$dats .= '<option class="form-control" value="'.$optk.'">'.$optv.'</option>';
										}
										$dats .= '</select>';
									break;
									case "checkbox":
										$data = explode(",", $ogArray[$ktr][$k]);
										foreach($vf as $optk=>$optv){
											$dats .= '<div class="checkbox '.($disabled[$k]?'disabled':'').'"><label><input type="checkbox" name="'.$k.'[]" value="'.$optk.'"> '.$optv.'</label></div>';
										}
									break;
									case "radio":
										foreach($vf as $optk=>$optv){
											$dats .= '<div class="radio"><label><input type="radio" name="'.$k.'" id="'.$optk.$optv.$k.'radio" value="'.$optk.'" '.($disabled[$k]?'disabled':'').'> '.$optv.'</label></div>';
										}
									break;
									case "fileSelect":
										$dats .= '
											<input type="file" class="form-control" name="'.$k.'" style="width:100%" '.($disabled[$k]?'readonly':'').'>
											';
									break;
									case "imageSelect":
										$dats .= '
											<input type="file" class="form-control" name="'.$k.'" style="width:100%" '.($disabled[$k]?'readonly':'').'>
											';
									break;
									default:
										echo "";
								}
							}
							
						}
						
						$dats .= '</div></div>';
					}
				}
			
				
			$dats .= '		
						</div>
					
					  </div>

				      <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">'.LANG_CLOSE.'</button>
						<button type="submit" class="btn btn-success">'.LANG_ADD.'</button>
				      </div>
				      </form>
				    </div>
				  </div>
				</div>
				';
			}else{
				$add = '<a href="'.$this->addPage.'"><button type="button" class="btn btn-success">'.LANG_ADD.'</button></a>';
			}
			
		}
		# ================ #
		# ==== OUTPUT ==== #
		# ================ #
		$pag = $pag->generate();
		$output = <<<HTML
		    
			<div class="container">
			    <div class="row">
			        <div id="no-more-tables" class="no-more-tables">
			        	<div class="btn-group btn-group-justified" role="group" aria-label="..."><div class="btn-group" role="group">
			        		$add
			        	</div></div>
			            <table class="col-md-12 col-sm-12 table-bordered table-striped table-condensed cf">
			        		<thead class="cf">
			        			<tr>
			        				$tits
			        			</tr>
			        		</thead>
			        		<tbody>
			        			$dats
			        		</tbody>
			        	</table>
			        </div>
			    </div>
			</div>
			$pag
			
HTML;
		//Setting Table ID for the refresh thing
		if($this->justReturn != true){
			echo '<span id="table'.$this->genID.'">'.$output.'</span>';
		}else{
			return '<span id="table'.$this->genID.'">'.$output.'</span>';
		}
		
		$_SESSION["_class".$this->genID] = base64_encode(serialize($this));
		//echo '<div id="preloader">SIEDITI E </div><button onclick="reload(\''.$this->genID.'\',{target:\'table'.$this->genID.'\',preloader:\'preloader\'})">CLICCA QUI</button><span id="hey"></span>';


		
		if($newTable===true) $this->genID++;
		return $output;

		
	}
	
	
}
