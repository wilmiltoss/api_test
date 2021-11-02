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

	//consultar registros
	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}estatus = 1{$ord}";
		return $obj->execute($sql);
	}

  //insertar registro cliente
		public static function save($id){

		$obj = new self($id);

		$obj->fields['idcliente']['value']	=	$_POST['idcliente'];
		$obj->fields['nit']['value']	=	$_POST['nit'];
		$obj->fields['nombre']['value']	=	$_POST['nombre'];
		$obj->fields['telefono']['value']	=	$_POST['telefono'];
		$obj->fields['direccion']['value']	=	$_POST['direccion'];
		$obj->fields['dateadd']['value']	=	date('Y-m-d H:i:s');
		$obj->fields['usuario_id']['value']	=	$_POST['usuario_id'];
		$obj->fields['estatus']['value']	=	$_POST['estatus'];

		if($obj->validate($obj,$id)):
			return $obj->update($obj, $id);
		else:
			
			Message::set("Por favor complete correctamente el formulario para continuar", MESSAGE_ERROR);
			return $obj->error;
		endif;
	}

 // modificar un cliente 
	
}
?>