<?php
class Local_punto extends Mysql{
	protected $tableName	= "local_punto";
	protected $primaryKey = "punto_id";
	protected $fields	= array(
		"admin_id"	=> array("type" => "int", "required" => "0", "validation" => "none"),
		"local_id"	=> array("type" => "int", "required" => "1", "validation" => "none"),
		"punto_nombre"	=> array("type" => "varchar",	"length"=> 40, "required" => "1", "validation" => "none"),
		"punto_orden"	=> array("type" => "int", "required" => "1", "validation" => "none"),
		"punto_ubicacion"	=> array("type" => "text",	"length"=> 65535, "required" => "1", "validation" => "none"),
		"punto_latlong"	=> array("type" => "text",	"length"=> 65535, "required" => "1", "validation" => "none"),
		"punto_tiempo"	=> array("type" => "time", "required" => "0", "validation" => "none"),
		"punto_status"	=> array("type" => "tinyint", "required" => "1", "validation" => "none"),
		"punto_timestamp"	=> array("type" => "timestamp", "required" => "0", "validation" => "none"),
	);

	/*inserta o modifica un registro*/

	public static function save($id){

		$obj = new self($id);
		$obj->fields['admin_id']['value']	=	$_POST['admin_id'];
		$obj->fields['local_id']['value']	=	$_POST['local_id'];
		$obj->fields['punto_nombre']['value']	=	$_POST['punto_nombre'];
		$obj->fields['punto_orden']['value']	=	$_POST['punto_orden'];
		$obj->fields['punto_ubicacion']['value']	=	$_POST['punto_ubicacion'];
		$obj->fields['punto_latlong']['value']	=	$_POST['punto_latlong'];
		$obj->fields['punto_tiempo']['value']	=	$_POST['punto_tiempo'];
		$obj->fields['punto_status']['value']	=	isset($_POST['punto_status']) ? number($_POST['punto_status']) : 0;

		if($obj->validate($obj,$id)):
			return $obj->update($obj, $id);
		else:
			
			Message::set("Por favor complete correctamente el formulario para continuar", MESSAGE_ERROR);
			return $obj->error;
		endif;
	}

	/*oculta o elimina un registro*/
	public static function delete($id){
		$obj = new self();
		$obj->change($obj->tableName, "punto_hidden", 1, $obj->primaryKey . " = {$id}");
	}

	public static function select($id){
		$obj = new self();
		return $obj->find($obj->tableName, $obj->primaryKey, $id, "punto_hidden = 0");
	}

	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}punto_status = 1 AND punto_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function getFirst($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}punto_status = 1 AND punto_hidden = 0 ORDER BY " . $obj->primaryKey . " ASC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getLast($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}punto_status = 1 AND punto_hidden = 0 ORDER BY " . $obj->primaryKey . " DESC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getAll($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}punto_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function listing($limit=10, $page=1, $fields=null, $where=null){
		$obj = new self();
		$listing = new Listing();
		$where = strlen($where) > 0 ? " AND {$where}" : "";
		return $listing->get($obj->tableName, $limit, $fields, $page, "WHERE punto_status = 1 AND punto_hidden = 0{$where}");
	}

	public static function set($field, $value, $where=null){
		$obj = new self();
		$obj->change($obj->tableName, $field, $value, $where);
	}

	public static function bulk($action, $ids){

		$obj = new self();
		$ids = json_decode($ids);

		switch($action):
			//activar
			case "1":
				foreach($ids as $id):
					self::set("punto_status", 1, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//desactivar
			case "2":
				foreach($ids as $id):
					self::set("punto_status", 0, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//eliminar
			case "3":
				foreach($ids as $id):
					self::delete($id);
				endforeach;
				break;
		endswitch;

	}

	public static function combobox($selected=null,$onchange=null){
		$obj = new self();
		$fsel = ($selected == null || $selected == 0) ? ' selected="selected"' : '';
		$list = "SELECT punto_id, admin_id FROM local_punto WHERE punto_status = 1 AND punto_hidden = 0 ORDER BY admin_id ASC";
		$list = $obj->exec($list);
		print '<select name="punto_id" id="local_punto_combo" class="form-control" style="color:#000;">';
			print '<option value=""'.$fsel.'>Seleccionar</option>';
			if(is_array($list) && count($list) > 0):
				foreach($list as $dat):
					$select = $dat['punto_id'] == $selected ? ' selected="selected"' : "";
					print '<option value="'.$dat['punto_id'].'"'.$select.'>'.htmlspecialchars($dat['admin_id']).'</option>';
				endforeach;
			endif;
		print '</select>';
	}

	public static function getfields(){
		$obj = new self();
		return $obj->fields;
	}
	
}
?>