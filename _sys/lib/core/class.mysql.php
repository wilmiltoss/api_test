<?php
class Mysql {

    private $Conn;
    private $RowCount = 0;
	private $lastID = 0;
	public  $error = array();

    public function __construct() {
		$this->connect();
    }
	
	public static function exec($sql){
		$obj = new self;
		return $obj->Execute($sql);
	}

    public function Execute($sql) {
		
		if(!is_resource($this->Conn)):
			$this->connect();
		endif;
		
        if (!empty($sql)):
			
            $res = mysqli_query($this->Conn, $sql) or Message::set(mysqli_error($this->Conn), MESSAGE_ERROR);
            $errtx = mysqli_error($this->Conn);
        	$error_number = mysqli_errno($this->Conn);
			
			Log::write($sql);
            
			if (!empty($errtx)):
				Log::write($errtx);
                $this->error = array($errtx);
			else:
				$this->error = '';
            endif;

            if (!is_bool($res)):
                
                $this->RowCount = mysqli_num_rows($res);
                $data = array();
                while ($filas = mysqli_fetch_assoc($res)) {
                    $data[] = $filas;
                }
                mysqli_free_result($res);
				mysqli_close($this->Conn);
                return $data;
            else:
				$this->lastID = mysqli_insert_id($this->Conn);
				mysqli_close($this->Conn);
                return $res;
            endif;
        else:
			Log::write('Query was empty');
			mysqli_close($this->Conn);
            $this->error = array('Query was empty');
			return $this->error;
        endif;
    }
	
	public function last_id(){
		return  $this->lastID;
	}
	
	public function query($type, $tableName, $primaryKey, $fields, $primaryKeyValue){
		switch(strtoupper($type)):
			case "INSERT":
			
				$insert_fields = NULL;
				$insert_values = NULL;
				
				foreach($fields as $fieldName => $field):
				
					$field['value'] = isset($field['value']) ? $field['value'] : "";
					$value = trim($field['value']);
					
					if(empty($value)):
						if(array_key_exists("default", $field)):
							$value = "'" . addslashes($field['default']) . "'";
						else:
							if($value === '0'):
								$value = "'0'";
							else:
								$value = "NULL";
							endif;
						endif;
					else:
						$value = "'" . addslashes($value) . "'";
					endif;
					
					$insert_fields .= $fieldName . ",";
					$insert_values .= $value . ",";
				endforeach;
				
				$insert_fields = substr($insert_fields,0,-1);
				$insert_values = substr($insert_values,0,-1);
				
				$sql = "INSERT INTO {$tableName}({$insert_fields}) VALUES({$insert_values})";
			
				break;
			case "UPDATE":
				
				$update_values = "";
				
				foreach($fields as $fieldName => $field):
				
					$field['value'] = isset($field['value']) ? $field['value'] : "";
					$value = trim($field['value']);
					
					if(!isset($field['update']) || $field['update'] == true):
					
						if(empty($value)):
							if(array_key_exists("default", $field)):
								$value = "'" . addslashes($field['default']) . "'";
							else:
								if($value === '0'):
									$value = "'0'";
								else:
									$value = "NULL";
								endif;
							endif;
						else:
							$value = "'" . addslashes($value) . "'";
						endif;
					
						$update_values .= $fieldName . " = ". $value . ",";
						
					endif;
					
				endforeach;
				
				$update_values = substr($update_values,0,-1);
				$sql = "UPDATE {$tableName} SET {$update_values} WHERE {$primaryKey} = '{$primaryKeyValue}'";
				
				break;
			case "DELETE":
			
				$sql = "DELETE FROM {$tableName} WHERE {$primaryKey} = '{$primaryKeyValue}'";
				break;
				
		endswitch;
		
		return $sql;
	}
	
	public function update($ob, $id){
		$sql = number($id) == 0 ? "INSERT" : "UPDATE";
		$qry = $this->query($sql, $ob->tableName, $ob->primaryKey, $ob->fields, $id);
		$this->execute($qry);
		return number($id) > 0 ? $id : $this->last_id();
	}
	
	public function change($table, $field, $value, $where=NULL){
		$table = addslashes($table);
		$field = addslashes($field);
		$value = addslashes($value);
		$where = $where == NULL ? "" : " WHERE {$where}";
		$sql = "UPDATE {$table} SET {$field} = '{$value}' {$where}";
		$this->execute($sql);
	}
	
	public function commit(){
		if(!is_resource($this->Conn)):
			$obj = new self();
			$obj->execute("COMMIT");
		else:
			$this->execute("COMMIT");
		endif;
	}
	
	public function remove($ob, $id){
		
		$sql = $this->query("DELETE", $ob->tableName, $ob->primaryKey, NULL, $id);
		$this->execute($sql);
	}
	
	public function find($table, $field, $value, $options=""){
		
		if(strlen($options) > 0):
			$opt = "AND {$options}";
		else:
			$opt = "";
		endif;
		
		$sql = "SELECT * FROM {$table} WHERE {$field} = '{$value}' ".$opt;
		$res = $this->execute($sql);
		return $res;
	}
	
	public function validate($ob, $id){
	
		
		foreach($ob->fields as $fieldName => $field):
		
			$value = isset($field['value']) ? trim($field['value']) : "";
			
			
			if(strlen($value) == 0):	
				if(array_key_exists("required", $field)):
					if($field['required'] === "1"):
						$error[$fieldName] = "Este dato es requerido";
					endif;
				endif;
			else:
			
				if(array_key_exists("validation", $field) && $field['validation'] != "none"):
				
					$validations = explode(",", $field['validation']);
					
					foreach($validations as $validation):

						switch($validation):
							case "email":
								$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
								if(!preg_match($regex, $field['value'])):
									$error[$fieldName] = "El email {$field[value]} tiene formato incorrecto";
								endif;
								
								break;
							case "number":
								if(!is_numeric($field['value'])):
									$error[$fieldName] = "Este dato debe ser un número";
								endif;
								break;
							case "unique":
								$sql = "SELECT {$fieldName} FROM " . $ob->tableName . " WHERE {$fieldName} = '{$field['value']}' AND " . $ob->primaryKey . " != {$id}";
								$res = $this->execute($sql);
								if(is_array($res) && count($res) > 0):
									$error[$fieldName] = $value. " ya está registrado";
								endif;
								break;
							case "maxlen":
								if(array_key_exists("maxlen", $field)):
									if(strlen(trim($field['value'])) > $field['maxlen']):
										$error[$fieldName] = "Este dato no puede superar los " . $field['maxlen'] . " caracteres de longitud.";
									endif;
								endif;
								break;
							case "minlen":
								if(array_key_exists("minlen", $field)):
									if(strlen(trim($field['value'])) < $field['minlen']):
										$error[$fieldName] = "Este dato debe tener por lo menos " . $field['minlen'] . " caracteres de longitud.";
									endif;
								endif;
								break;
						endswitch;
				
					endforeach;
				
					
				endif;
				
			endif;
			
		endforeach;
		
		$error = isset($error) ? $error : NULL;
		
		$this->error = $error;
		
		return haveRows($this->error) ? false : true;
	}	
	
	public function connect(){
		$this->Conn = new mysqli(DBHost, DBUser, DBPass, DBName);
		if(mysqli_connect_errno()):
			Error("Error en la conexión a la base de datos: " . mysqli_connect_error());
			return;
		endif;
	}
}
?>
