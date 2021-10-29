<?php
class Clientes extends Mysql{
	protected $tableName	= "cliente";
	protected $primaryKey = "idcliente";
	protected $fields	= array(
		"nit"	=> array("type" => "int", "length"=> 11, "required" => "0", "validation" => "none"),
		"nombre"	=> array("type" => "varchar", "length"=> 80, "required" => "0", "validation" => "none"),
		"telefono"	=> array("type" => "int",	"length"=> 11, "required" => "0", "validation" => "none"),
		"direccion"	=> array("type" => "text", "length"=> 0, "required" => "0", "validation" => "none"),
	);

	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}estatus = 1{$ord}";
		return $obj->execute($sql);
	}

	
}
?>