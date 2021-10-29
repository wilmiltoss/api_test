<?php
class Local_marcacion extends Mysql{
	protected $tableName	= "local_marcacion";
	protected $primaryKey = "marca_id";
	protected $fields	= array(
		"local_id"	=> array("type" => "int", "required" => "1", "validation" => "none"),
		"admin_id"	=> array("type" => "int", "required" => "1", "validation" => "none"),
		"punto_orden"	=> array("type" => "int", "required" => "1", "validation" => "none"),
		"marca_observacion"	=> array("type" => "text",	"length"=> 65535, "required" => "0", "validation" => "none"),
		"marca_url"	=> array("type" => "text",	"length"=> 65535, "required" => "0", "validation" => "none"),
		"marca_ubicacion"	=> array("type" => "text",	"length"=> 65535, "required" => "1", "validation" => "none"),
		"marca_status"	=> array("type" => "tinyint", "required" => "1", "validation" => "none"),
		"marca_timestamp"	=> array("type" => "timestamp", "required" => "0", "validation" => "none"),
	);

	/*inserta o modifica un registro*/

	public static function save($id){

		$obj = new self($id);

		/*
			Array
			(
			    [local_id] => 13
			    [admin_id] => 7
			    [punto_orden] => 1
			    [marca_ubicacion] => test
			    [marca_observacion] => 
			    [marca_url] => http://www.retail.com.py
			    [marca_status] => 1
			)
		*/

		$obj->fields['local_id']['value']	=	$_POST['local_id'];
		$obj->fields['admin_id']['value']	=	$_POST['admin_id'];
		$obj->fields['punto_orden']['value']	=	$_POST['punto_orden'];
		$obj->fields['marca_ubicacion']['value']	=	$_POST['marca_ubicacion'];
		$obj->fields['marca_observacion']['value']	=	$_POST['marca_observacion'];
		$obj->fields['marca_url']['value']	=	$_POST['marca_url'];
		$obj->fields['marca_status']['value']	=	isset($_POST['marca_status']) ? number($_POST['marca_status']) : 0;

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
		$obj->change($obj->tableName, "marca_hidden", 1, $obj->primaryKey . " = {$id}");
	}

	public static function select($id){
		$obj = new self();
		return $obj->find($obj->tableName, $obj->primaryKey, $id, "marca_hidden = 0");
	}

	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}marca_status = 1 AND marca_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function getFirst($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}marca_status = 1 AND marca_hidden = 0 ORDER BY " . $obj->primaryKey . " ASC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getLast($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}marca_status = 1 AND marca_hidden = 0 ORDER BY " . $obj->primaryKey . " DESC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getAll($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}marca_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function listing($limit=10, $page=1, $fields=null, $where=null){
		$obj = new self();
		$listing = new Listing();
		$where = strlen($where) > 0 ? " AND {$where}" : "";
		return $listing->get($obj->tableName, $limit, $fields, $page, "WHERE marca_status = 1 AND marca_hidden = 0{$where}");
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
					self::set("marca_status", 1, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//desactivar
			case "2":
				foreach($ids as $id):
					self::set("marca_status", 0, $obj->primaryKey . " = {$id}");
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
		$list = "SELECT marca_id, local_id FROM local_marcacion WHERE marca_status = 1 AND marca_hidden = 0 ORDER BY local_id ASC";
		$list = $obj->exec($list);
		print '<select name="marca_id" id="local_marcacion_combo" class="form-control" style="color:#000;">';
			print '<option value=""'.$fsel.'>Seleccionar</option>';
			if(is_array($list) && count($list) > 0):
				foreach($list as $dat):
					$select = $dat['marca_id'] == $selected ? ' selected="selected"' : "";
					print '<option value="'.$dat['marca_id'].'"'.$select.'>'.htmlspecialchars($dat['local_id']).'</option>';
				endforeach;
			endif;
		print '</select>';
	}

	public static function total($local_id = null){
		$obj = new self;
		$sql = "SELECT COUNT(punto_id) AS total FROM local_punto WHERE local_id = '{$local_id}' AND punto_status = 1 AND punto_hidden = 0";
		$total = DB::execute($sql);
		return $total[0]['total'];
	}

	public static function getfields(){
		$obj = new self();
		return $obj->fields;
	}
	
}
?>