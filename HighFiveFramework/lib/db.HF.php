<?php dependencies();

/*! Contains every class, even CRUD, to manage a MySQL database */	
class HFdb {
	
	public $idFieldName = "id";
	
	/*! Set idFieldName if you want to use something different from standard 'id' */
	function setIdFieldName($name){
		$this->idFieldName = $name;
		return $this;
	}
	
	
	/*! Connect to the database with the passed data
		If you want just to connect to the database without choosing a table just leave the $tableName var empty		
	*/
	function connect($ip="localhost",$user="root",$password="root",$dbName=""){
		$link = mysql_connect($ip, $user, $password);
		if (!$link) {
			die('Error connecting to MySQL database on ip '.$ip.' : ' . mysql_error());
		}
		if($dbName!=""){
			$db_selected = mysql_select_db($dbName, $link);
			if (!$db_selected) {
				
				$error = 'Db connection is fine, but error connecting to the table '.$dbName.' : ' . mysql_error();
				
				throw new Exception($error);
				
				die ($error);
			}
		}
	}
	
	
	/*! Convert any Select Query into an assoc-array - return array */
	function sqlToArray($query){
		$result = mysql_query($query);
		//ERROR HANDLER
		if($result===false){
			$error = "Error HFdb::sqlToArray '$query' :<br/>".mysql_error();
			throw new Exception($error);
			die ($error);
		}
		
		$i=0;
		$array = array();
		while ($row = mysql_fetch_assoc($result)) {
			foreach($row as $key=>$value){
			    $array[$i][$key] = $value;
			}
			$i++;
		}
		return $array;
	}

	/*! Insert values into a tableName (param1) with array's content.
		Keys are the table db fields name and the values are the values to be put in
		This will work like a charm if you set all the fields into a form and pass $_POST as second param!
		C of CRUD
	*/
	//aliases insert
	function create($tableName,$array){$this->insert($tableName, $array);}
	
	function insert($tableName, $array){
		
		if(!is_array($array)){ echo "Array is needed in HFdb::insert() function! The second parameter must be an assoc array. Read the docs!"; die; }
		$keys = null;
		$values = null;
		foreach($array as $k=>$v){
			
			if($keys!=null){ $keys.=","; $values.=","; }
			$keys .= $k;
			if(is_numeric($v)){
				$values .= $v;
			}else{
				$values .= "'".$v."'";
			}
			
		}
		$sql  = "INSERT INTO $tableName ($keys) VALUES ($values)";
				
		
		//ERROR HANDLER
		if(!mysql_query($sql)){
			$error = "Error with HFdb::insert \"$sql\":

" .mysql_error();
			throw new Exception($error);
			die ($error);
		}
	
	
		return $this;
	}

	/*! Select fromTable (param1) the fields * (all) ex."id,name,email"
		Where a field is something like ex."WHERE name = '".$name."'" will output "WHERE name = 'nameIntoVar'" (strings need the single quotes)
		Limit will limit the output ex."LIMIT 0,20" will take 20 fields from index 0
		R of CRUD
	*/
	function select($fromTable,$theFields="*",$where="",$limit=""){
		
		if($where!="") $where = " WHERE ".$where;
		if($limit!="") $limit = " LIMIT ".$limit;
		
		$sql  = "SELECT $theFields FROM $fromTable$where$limit";
		
		//ERROR HANDLER
		$return = mysql_query($sql);
		if($return === false ){
			$error = "Error with HFdb::select '$sql': " .mysql_error();
			throw new Exception($error);
			die ($error);
		}
		return $return;
	}

	/*! Update values of id (param1) into a tableName (param2) with array's content (param3).
		This will work like a charm if you set all the fields into a form and pass $_POST as third param!
		U of CRUD
	*/
	function update($id, $tableName, $values){
		
		if(!is_array($values)) return false;
		$val = null;
		foreach($values as $k=>$v){
			
			if($val!=null){ $val.=","; }
			$val .= $k."=";
			if(is_numeric($v)){
				$val .= $v;
			}else{
				$val .= "'".$v."'";
			}
			
		}
		if(!is_numeric($id)) $id = "\"".$id."\"";
		$sql  = "UPDATE $tableName SET $val WHERE ".$this->idFieldName." =$id";
		
		$return = mysql_query($sql);
		
		if($return === false ){
			$error = "Error with HFdb::update '$sql': " .mysql_error();
			throw new Exception($error);
			die ($error);
		}
		
		
		return $this;
	}

	/*! Delete ID fromTable
		D of CRUD
	*/
	function delete($id,$fromTable){
		if(!is_numeric($id)) $id = "\"".$id."\"";
		$sql  = "DELETE FROM $fromTable WHERE ".$this->idFieldName." =$id";
		$return = mysql_query($sql);
		
		if($return === false ){
			$error = "Error with HFdb::delete '$sql': " .mysql_error();
			throw new Exception($error);
			die ($error);
		}
		return $this;
	}
	
 	
	/*! This will create a perfect array key=>value for any select/radio of crud or anything you need! */
	function makeSelectArray($tableName,$keyFieldName,$valueFieldName,$orderBy="id ASC"){
		global $HF;
		$array = $HF->db->sqlToArray("SELECT $keyFieldName,$valueFieldName FROM $tableName ORDER BY $orderBy");
		$return = array();
		foreach($array as $v){
			$return[$v[$keyFieldName]]=$v[$valueFieldName];
		}
		return $return;
	}

	function increment($id,$tableName,$tableField,$howMuch){
		$idup = $id;
		if(!is_numeric($id)) $id = '"'.$id.'"';
		$select = $this->sqlToArray("SELECT $tableField FROM $tableName WHERE ".$this->idFieldName."=$id");
		$number = $select[0][$tableField] + $howMuch;
		$this->update($idup,$tableName,array($tableField=>$number));
		return $this;
	}
	
	function decrement($id,$tableName,$tableField,$howMuch){
		$idup = $id;
		if(!is_numeric($id)) $id = "\"".$id."\"";
		$select = $this->sqlToArray("SELECT $tableField FROM $tableName WHERE ".$this->idFieldName."=$id");
		$number = $select[0][$tableField] - $howMuch;
		$this->update($idup,$tableName,array($tableField=>$number));
		return $this;
	}
	
	
	
	
}
