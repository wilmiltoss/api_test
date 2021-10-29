<?php
class Admin_rol extends Mysql{
	protected $tableName	= "admin_rol";
	protected $primaryKey = "rol_id";
	protected $fields	= array(
		"rol_titulo"	=> array("type" => "varchar",	"length"=> 80, "required" => "0", "validation" => "none"),
		"rol_permisos"	=> array("type" => "text",	"length"=> 65535, "required" => "0", "validation" => "none"),
		"rol_status"	=> array("type" => "tinyint", "required" => "0", "validation" => "none"),
		"rol_timestamp"	=> array("type" => "timestamp", "required" => "0", "validation" => "none"),
	);

	/*inserta o modifica un registro*/

	public static function save($id){

		$obj = new self($id);
		$obj->fields['rol_titulo']['value']	=	$_POST['rol_titulo'];
		foreach($_POST as $pk => $pv):
			if(strpos($pk, "_permission_") !== false):
				$key = explode("|", str_replace("_permission_","|",$pk));	
				$permission[$key[0]][$key[1]] = true;
			endif;
		endforeach;




		/* config */
		if($_POST['catalogo_permission_insert'] == 1){
			$permission["catalogo_categoria"]["insert"] = true;
			$permission["catalogo_imagen"]["insert"] = true;
		}
		if($_POST['catalogo_permission_view'] == 1){
			$permission["catalogo_categoria"]["view"] = true;
			$permission["catalogo_imagen"]["view"] = true;
		}
		if($_POST['catalogo_permission_update'] == 1){
			$permission["catalogo_categoria"]["update"] = true;
			$permission["catalogo_imagen"]["update"] = true;
		}
		if($_POST['catalogo_permission_export'] == 1){
			$permission["catalogo_categoria"]["export"] = true;
			$permission["catalogo_imagen"]["export"] = true;
		}
		if($_POST['catalogo_permission_delete'] == 1){
			$permission["catalogo_categoria"]["delete"] = true;
			$permission["catalogo_imagen"]["delete"] = true;
		}

		/* Categoria */
		if($_POST['categoria_permission_insert'] == 1){
			$permission["categoria_categoria"]["insert"] = true;
			$permission["categoria_imagen"]["insert"] = true;
		}

		if($_POST['categoria_permission_view'] == 1){
			$permission["categoria_categoria"]["view"] = true;
			$permission["categoria_imagen"]["view"] = true;
		}
		if($_POST['categoria_permission_export'] == 1){
			$permission["categoria_categoria"]["export"] = true;
			$permission["categoria_imagen"]["export"] = true;
		}
		if($_POST['categoria_permission_update'] == 1){
			$permission["categoria_categoria"]["update"] = true;
			$permission["categoria_imagen"]["update"] = true;
		}
		if($_POST['categoria_permission_delete'] == 1){
			$permission["categoria_categoria"]["delete"] = true;
			$permission["categoria_imagen"]["delete"] = true;
		}

		/* Cuentas */
		if($_POST['cuenta_permission_insert'] == 1){
			$permission["cuenta_movimiento"]["insert"] = true;
		}
		if($_POST['cuenta_permission_view'] == 1){
			$permission["cuenta_movimiento"]["view"] = true;
		}
		if($_POST['cuenta_permission_update'] == 1){
			$permission["cuenta_movimiento"]["update"] = true;
		}
		if($_POST['cuenta_permission_export'] == 1){
			$permission["cuenta_movimiento"]["export"] = true;
		}
		if($_POST['cuenta_permission_delete'] == 1){
			$permission["cuenta_movimiento"]["delete"] = true;
		}

		/* departamentos */
		if($_POST['departamento_permission_insert'] == 1){
			$permission["ciudad"]["insert"] = true;
		}
		if($_POST['departamento_permission_view'] == 1){
			$permission["ciudad"]["view"] = true;
		}
		if($_POST['departamento_permission_update'] == 1){
			$permission["ciudad"]["update"] = true;
		}
		if($_POST['departamento_permission_export'] == 1){
			$permission["ciudad"]["export"] = true;
		}
		if($_POST['departamento_permission_delete'] == 1){
			$permission["ciudad"]["delete"] = true;
		}


		/* bitacoras */
		if($_POST['bitacora_evento_permission_insert'] == 1){
			$permission["bitacora"]["insert"] = true;
			$permission["local_evento"]["insert"] = true;
		}
		if($_POST['bitacora_evento_permission_view'] == 1){
			$permission["bitacora"]["view"] = true;
			$permission["local_evento"]["view"] = true;
		}
		if($_POST['bitacora_evento_permission_update'] == 1){
			$permission["bitacora"]["update"] = true;
			$permission["local_evento"]["update"] = true;
		}
		if($_POST['bitacora_evento_permission_export'] == 1){
			$permission["bitacora"]["export"] = true;
			$permission["local_evento"]["export"] = true;
		}
		if($_POST['bitacora_evento_permission_delete'] == 1){
			$permission["bitacora"]["delete"] = true;
			$permission["local_evento"]["delete"] = true;
		}

		/* productos */
		if($_POST['producto_permission_insert'] == 1){
			$permission["producto_relacionado"]["insert"] = true;
			$permission["producto_variante"]["insert"] = true;
			$permission["producto_imagen"]["insert"] = true;
		}
		if($_POST['producto_permission_view'] == 1){
			$permission["producto_relacionado"]["view"] = true;
			$permission["producto_variante"]["view"] = true;
			$permission["producto_imagen"]["view"] = true;
		}
		if($_POST['producto_permission_export'] == 1){
			$permission["producto_relacionado"]["export"] = true;
			$permission["producto_variante"]["export"] = true;
			$permission["producto_imagen"]["export"] = true;
		}
		if($_POST['producto_permission_update'] == 1){
			$permission["producto_relacionado"]["update"] = true;
			$permission["producto_variante"]["update"] = true;
			$permission["producto_imagen"]["update"] = true;
		}
		if($_POST['producto_permission_delete'] == 1){
			$permission["producto_relacionado"]["delete"] = true;
			$permission["producto_variante"]["delete"] = true;
			$permission["producto_imagen"]["delete"] = true;
		}

		/* proveedor */
		if($_POST['proveedor_permission_insert'] == 1){
			$permission["proveedor_detalle"]["insert"] = true;
		}
		if($_POST['proveedor_permission_view'] == 1){
			$permission["proveedor_detalle"]["view"] = true;
		}
		if($_POST['proveedor_permission_update'] == 1){
			$permission["proveedor_detalle"]["update"] = true;
		}
		if($_POST['proveedor_permission_export'] == 1){
			$permission["proveedor_detalle"]["export"] = true;
		}
		if($_POST['proveedor_permission_delete'] == 1){
			$permission["proveedor_detalle"]["delete"] = true;
		}

		/* promociones */	
		if($_POST['como_empezar_permission_insert'] == 1 ||$_POST['como_empezar_permission_view'] == 1 ||$_POST['como_empezar_permission_update'] == 1 ||$_POST['como_empezar_permission_delete'] == 1){
			$permission["documentacion"]["insert"] = true;
		}

		/* promotores */
		if($_POST['promotor_notifica_insert'] == 1 || $_POST['promotor_permission_insert'] == 1 || $_POST['promotor_ingreso_permission_insert'] == 1 || $_POST['promotor_salida_permission_insert'] == 1){
			$permission["promotores"]["insert"] = true;
		}

		$obj->fields['rol_permisos']['value'] = json_encode($permission);
		$obj->fields['rol_timestamp']['value'] = date('Y-m-d H:i:s');
		$obj->fields['rol_status']['value']	=	isset($_POST['rol_status']) ? number($_POST['rol_status']) : 0;

		if($obj->validate($obj,$id)):
			Admins::set('admin_permission',json_encode($permission),"rol_id = '{$id}'");
			return $obj->update($obj, $id);
		else:
			
			Message::set("Por favor complete correctamente el formulario para continuar", MESSAGE_ERROR);
			return $obj->error;
		endif;
	}

	/*oculta o elimina un registro*/
	public static function delete($id){
		$obj = new self();
		$obj->change($obj->tableName, "rol_hidden", 1, $obj->primaryKey . " = {$id}");
	}

	public static function select($id){
		$obj = new self();
		return $obj->find($obj->tableName, $obj->primaryKey, $id, "rol_hidden = 0");
	}

	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}rol_status = 1 AND rol_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function getFirst($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}rol_status = 1 AND rol_hidden = 0 ORDER BY " . $obj->primaryKey . " ASC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getLast($where=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}rol_status = 1 AND rol_hidden = 0 ORDER BY " . $obj->primaryKey . " DESC LIMIT 0,1";
		return $obj->execute($sql);
	}

	public static function getAll($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}rol_hidden = 0{$ord}";
		return $obj->execute($sql);
	}

	public static function listing($limit=10, $page=1, $fields=null, $where=null){
		$obj = new self();
		$listing = new Listing();
		$where = strlen($where) > 0 ? " AND {$where}" : "";
		return $listing->get($obj->tableName, $limit, $fields, $page, "WHERE rol_status = 1 AND rol_hidden = 0{$where}");
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
					self::set("rol_status", 1, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//desactivar
			case "2":
				foreach($ids as $id):
					self::set("rol_status", 0, $obj->primaryKey . " = {$id}");
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
		$list = "SELECT rol_id, rol_titulo FROM admin_rol WHERE rol_status = 1 AND rol_hidden = 0 ORDER BY rol_titulo ASC";
		$list = $obj->exec($list);
		print '<select name="rol_id" class="form-control" id="admin_rol_combo" style="color:#000;" onchange="">';
			print '<option value=""'.$fsel.'>Seleccionar</option>';
			if(is_array($list) && count($list) > 0):
				foreach($list as $dat):
					$select = $dat['rol_id'] == $selected ? ' selected="selected"' : "";
					print '<option value="'.$dat['rol_id'].'"'.$select.'>'.htmlspecialchars($dat['rol_titulo']).'</option>';
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