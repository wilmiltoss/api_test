<?php
class Admin_login_attempts extends Mysql{
	protected $tableName	= "admin_login_attempts";
	protected $primaryKey = "admin_login_attempt_id";
	protected $fields	= array(
		"admin_id"	=> array("type" => "int", "required" => "1", "validation" => "none"),
		"admin_login_ip_address"	=> array("type" => "varchar",	"length"=> 16, "required" => "1", "validation" => "none"),
		"admin_login_response"	=> array("type" => "text",	"length"=> 65535, "required" => "1", "validation" => "none"),
		"admin_login_timestamp"	=> array("type" => "timestamp", "required" => "0", "validation" => "none"),
		"admin_authToken"	=> array("type" => "varchar", "length" => 120,  "required" => "0", "validation" => "none"),
	);

	public function __construct(){
	}

	//-- inserta o modifica un registro

	public static function save($id){
	
		$obj = self::getInstance($id);

		$obj->fields['admin_id']['value']	= $_POST['admin_id'];
		$obj->fields['admin_login_timestamp']['value']	= date('Y-m-d H:i:s');
		$obj->fields['admin_login_ip_address']['value']	= $_POST['admin_login_ip_address'];
		$obj->fields['admin_login_response']['value']	= $_POST['admin_login_response'];
		$obj->fields['admin_authToken']['value']	= $_POST['admin_authToken'];

		if($obj->validate($obj,$id)):
			$obj->update($obj, $id);
		else:
			Message::set("Por favor complete correctamente el formulario para continuar", "ERROR");
			return $obj->error;
		endif;
	}
	
	//-- oculta o elimina un registro
	public static function delete($id){
		$obj = self::getInstance($id);
		
	}
	
	public static function select($id){
		$obj = self::getInstance($id);
		return $obj->find($obj->tableName, $obj->primaryKey, $id);
	}
	
	public static function combobox($str=null){
		
		
	}
	
	public static function getfields(){
		$obj = self::getInstance();
		return $obj->fields;
	}
	
	//-- crea instancia de la clase
	static private function getInstance($id=null) {
        return new self($id);
    }
}
?>