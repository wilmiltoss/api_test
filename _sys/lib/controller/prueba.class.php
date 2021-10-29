<?php
class Locales extends Mysql{
	protected $tableName	= "cadena_local";
	protected $primaryKey = "local_id";
	protected $fields	= array(
		"cadena_id"	=> array("type" => "int", "required" => "0", "validation" => "none"),
		"local_nombre"	=> array("type" => "varchar", "length"=> 120, "required" => "0", "validation" => "none"),
		"local_codigo"	=> array("type" => "varchar", "length"=> 10, "required" => "0", "validation" => "none"),
		"local_ip"	=> array("type" => "varchar",	"length"=> 120, "required" => "0", "validation" => "none"),
		"local_status"	=> array("type" => "tinyint", "required" => "0", "validation" => "none"),
		"local_timestamp"	=> array("type" => "timestamp", "required" => "0", "validation" => "none"),
	);

	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}local_status = 1 AND local_hidden = 0{$ord}";
		return $obj->execute($sql);
	}



	
}
?>