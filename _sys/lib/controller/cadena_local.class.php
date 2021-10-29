<?php
class Cadena_local extends Mysql{
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

	/*inserta o modifica un registro*/

	public static function save($id){

		$obj = new self($id);

		$obj->fields['cadena_id']['value']	=	$_POST['cadena_id'];
		$obj->fields['local_nombre']['value']	=	$_POST['local_nombre'];
		$obj->fields['local_codigo']['value']	=	$_POST['local_codigo'];
		$obj->fields['local_ip']['value']	=	$_POST['local_ip'];
		$obj->fields['local_status']['value']	=	isset($_POST['local_status']) ? number($_POST['local_status']) : 0;
		$obj->fields['local_timestamp']['value']	=	$_POST['local_timestamp'];

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
		$obj->change($obj->tableName, "local_hidden", 1, $obj->primaryKey . " = {$id}");
	}

	public static function select($id){
		$obj = new self();
		return $obj->find($obj->tableName, $obj->primaryKey, $id, "local_hidden = 0");
	}

	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}local_status = 1 AND local_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function getFirst($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}local_status = 1 AND local_hidden = 0 ORDER BY " . $obj->primaryKey . " ASC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getLast($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}local_status = 1 AND local_hidden = 0 ORDER BY " . $obj->primaryKey . " DESC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getAll($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}local_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function listing($limit=10, $page=1, $fields=null, $where=null){
		$obj = new self();
		$listing = new Listing();
		$where = strlen($where) > 0 ? " AND {$where}" : "";
		return $listing->get($obj->tableName, $limit, $fields, $page, "WHERE local_status = 1 AND local_hidden = 0{$where}");
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
					self::set("local_status", 1, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//desactivar
			case "2":
				foreach($ids as $id):
					self::set("local_status", 0, $obj->primaryKey . " = {$id}");
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

	public static function combobox($selected=null,$onchange=null, $class=null){
		$obj = new self();
		$fsel = ($selected == null || $selected == 0) ? ' selected="selected"' : '';
		$list = "
				SELECT 
					l.local_id, l.cadena_id, l.local_nombre, c.cadena_nombre 
				FROM 
					cadena_local l
				INNER JOIN cadena c ON c.cadena_id = l.cadena_id
				WHERE l.local_status = 1 AND l.local_hidden = 0 
				ORDER BY l.cadena_id ASC
			";
		$list = $obj->exec($list);
		print '<select name="local_id" id="cadena_local_combo" class="form-control '.$class.'" style="color:#000;">';
			print '<option value=""'.$fsel.'>Seleccionar</option>';
			if(is_array($list) && count($list) > 0):
				foreach($list as $dat):
					$select = $dat['local_id'] == $selected ? ' selected="selected"' : "";
					print '<option value="'.$dat['local_id'].'"'.$select.'>'.htmlspecialchars($dat["cadena_nombre"].' - '.$dat['local_nombre']).'</option>';
				endforeach;
			endif;
		print '</select>';
	}


	public static function comboboxRRHH($selected=null,$onchange=null, $class=null){
		$obj = new self();
		$fsel = ($selected == null || $selected == 0) ? ' selected="selected"' : '';
		$list = "
				SELECT 
					l.local_id, l.cadena_id, l.local_codigo, l.local_nombre, c.cadena_nombre 
				FROM 
					cadena_local l
				INNER JOIN cadena c ON c.cadena_id = l.cadena_id
				WHERE l.local_status = 1 AND l.local_hidden = 0 
				ORDER BY l.cadena_id ASC
			";
		$list = $obj->exec($list);
		print '<select name="local_id" id="cadena_local_combo" class="form-control '.$class.'" style="color:#000;">';
			print '<option value=""'.$fsel.'>Seleccionar</option>';
			if(is_array($list) && count($list) > 0):
				foreach($list as $dat):
					$select = $dat['local_codigo'] == $selected ? ' selected="selected"' : "";
					print '<option value="'.$dat['local_codigo'].'"'.$select.'>'.htmlspecialchars($dat["cadena_nombre"].' - '.$dat['local_nombre']).'</option>';
				endforeach;
			endif;
		print '</select>';
	}

	public static function getfields(){
		$obj = new self();
		return $obj->fields;
	}

	public static function total($cadena_id = null){
		$obj = new self;
		$total = DB::execute("SELECT COUNT(local_id) AS total FROM cadena_local WHERE local_status = 1 AND local_hidden = 0 AND cadena_id = '{$cadena_id}'");
		return $total[0]['total'];
	}
	
}
?>