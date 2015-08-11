<?php
	
class HFrest{
	
	private $AUTH_KEY = null;
		
	function setAuthKey($key){
		$this->AUTH_KEY = $key;
	}
	
	function select($tableName,$fields="*",$where="",$limit=""){
		global $HF;
		if(!$this->authReturn()) die;

		try{
			$result = $HF->db->select($tableName,$fields,$where,$limit);
		}
		catch(Exception $e){
			http_response_code(401);
			echo '{"error":{"text":'. $e->getMessage() .'}}';
			return false;
		}
		
		$i=0;
		$array = array();
		
		while ($row = mysql_fetch_assoc($result)) {
			foreach($row as $key=>$value){
			    $array[$i][$key] = $value;
			}
			$i++;
		}

		echo json_encode($array);
		http_response_code(200);

	}
	
	
	function selectSql($sql){
		global $HF;
		if(!$this->authReturn()) die;
		
		try{
			$array = $HF->db->sqlToArray($sql);
		}
		catch(Exception $e){
			http_response_code(401);
			echo '{"error":{"text":'. $e->getMessage() .'}}';
			return false;
		}
		echo json_encode($array);
		http_response_code(200);
	}
	
	
	function create($tableName,$values){
		global $HF;
		if(!$this->authReturn()) die;
		
		try{
			$HF->db->insert($tableName, $values);
		}
		catch(Exception $e){
			http_response_code(401);
			echo '{"error":{"text":"'. $e->getMessage() .'"}}';
			return false;
		}
		http_response_code(200);
	}
	
	
	function update($id, $tableName, $values){
		global $HF;
		if(!$this->authReturn()) die;
		
		try{
			$HF->db->update($id,$tableName, $values);
		}
		catch(Exception $e){
			http_response_code(401);
			echo '{"error":{"text":'. $e->getMessage() .'}}';
			return false;
		}
		http_response_code(200);
	}
	
	
	function delete($id, $tableName){
		global $HF;
		if(!$this->authReturn()) die;
		
		try{
			$HF->db->delete($id, $tableName);
		}
		catch(Exception $e){
			http_response_code(401);
			echo '{"error":{"text":'. $e->getMessage() .'}}';
			return false;
		}
		http_response_code(200);
	}
	
	//Return boolean if authorized or not
	function auth($key=null){
		if($key == null){
			if($this->AUTH_KEY!=""){
				$key = $this->AUTH_KEY;
			}
		}
		$auth = getallheaders()['Authorization'];
		if($auth == $key){
			return true;
		}
		return false;
	}
	
	//Return 401 if not authorized, 200 if you are
	function authReturn($key=null){
		if($key == null){
			if($this->AUTH_KEY != null){
				$key = $this->AUTH_KEY;
			}else{
				http_response_code(200);
				return true;
			}
		}
		$auth = getallheaders();
		$auth = $auth['Authorization'];
		if($auth === $key){
			http_response_code(200);
			return true;
		}
		http_response_code(401);
		return false;
	}
}
