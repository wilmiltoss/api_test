<?php
class Admins extends Mysql{
	protected $tableName	= "admins";
	protected $primaryKey = "admin_id";
	protected $fields	= array(
		"admin_names"	=> array("type" => "varchar",	"length"=> 128, "required" => "1", "validation" => "none"),
		"admin_user"	=> array("type" => "varchar",	"length"=> 128, "required" => "1", "validation" => "none"),
		"admin_email"	=> array("type" => "varchar",	"length"=> 25, "required" => "0", "validation" => "email,unique"),
		"admin_telefono"	=> array("type" => "varchar",	"length"=> 40, "required" => "0", "validation" => "none"),
		"admin_password"	=> array("type" => "varchar",	"length"=> 32, "required" => "1", "validation" => "none"),
		"admin_permission"	=> array("type" => "text",	"length"=> 65535, "required" => "0", "validation" => "none"),
		"rol_id"	=> array("type" => "int", "required" => "0", "validation" => "none"),
		"local_id"	=> array("type" => "int", "required" => "0", "validation" => "none"),
		"funcionario_id"	=> array("type" => "int", "required" => "0", "validation" => "none"),
		"admin_isroot"	=> array("type" => "tinyint", "required" => "0", "validation" => "none"),
		"admin_status"	=> array("type" => "tinyint", "required" => "0", "validation" => "none"),
		"admin_hidden"	=> array("type" => "tinyint", "required" => "0", "validation" => "none"),
		"admin_timestamp"	=> array("type" => "timestamp", "required" => "0", "validation" => "none"),
		"admin_last_login"	=> array("type" => "timestamp", "required" => "0", "validation" => "none"),
	);

	public function __construct(){
	}

	//-- inserta o modifica un registro

	public static function save($id){
	
		$obj = new self($id);

		if($id > 0){
			$actual =  $obj->select($id);

			$funcionario = Funcionario::get("concat(funcionario_nombre, ' ', funcionario_apellido) = '{$actual[0]['admin_names']}'");

			$_POST['admin_email'] = $actual[0]['admin_email'];
			$_POST['admin_names'] = $actual[0]['admin_names'];
			
			// $_POST['admin_telefono'] = $actual[0]['admin_telefono'];
			//$_POST['local_id'] = $actual[0]['local_id'];

			$_POST['admin_user'] = $actual[0]['admin_user'];
			$_POST['funcionario_id'] = $funcionario[0]['funcionario_id'];
			$_POST['admin_isroot'] = $actual[0]['admin_isroot'];
			$_POST['admin_status'] = 1;
			
			if(strlen($_POST['admin_actual']) > 0 || strlen($_POST['admin_password']) > 0):
				$clave_actual = $actual[0]['admin_password'];
				$clave_nueva = md5($_POST['admin_actual'] . "_" . strtoupper(strrev($_POST['admin_email'])));

				if(login::get('rol_id') != 1 && $clave_actual != $clave_nueva){
					Message::set("Por favor complete correctamente el formulario para continuar", MESSAGE_ERROR);
					$obj->error['admin_actual'] = "La contraseña actual no es válida";
					return $obj->error;
				}else{
					$admin_random_seed = Encryption::Encrypt($clave_nueva, strrev(md5($clave_nueva)));
					$admin_password	= md5($_POST['admin_password'] . "_" . strtoupper(strrev($_POST['admin_email'])));
					Admins::set("admin_random_seed",$admin_random_seed, "admin_id = '{$id}'");
					Admins::set("admin_password",$admin_password, "admin_id = '{$id}'");
					return $id;
				}

			endif;

			if(Login::get('rol_id') == 1){
				$_POST['rol_id'];
			}else{
				$_POST['rol_id'] = $actual[0]['rol_id'];
			}
		}

		if(isset($_POST['admin_actual']) && $id > 0){
			$actual =  $obj->select($id);
			$_POST['local_id'] = $actual[0]['local_id'];
		}

		$obj->fields['admin_names']['value']	= $_POST['admin_names'];
		$obj->fields['admin_telefono']['value']	= $_POST['admin_telefono'];
		$obj->fields['local_id']['value']	=  $_POST['local_id'] ;
		$obj->fields['admin_user']['value']	= $_POST['admin_user'];
		$obj->fields['admin_email']['value']	= strtolower($_POST['admin_email']);
		$obj->fields['rol_id']['value']	= $_POST['rol_id'];
		$obj->fields['funcionario_id']['value']	= $_POST['funcionario_id'];
		$obj->fields['admin_status']['value']	= $_POST['admin_status'] == 1 ? 1 : 0;
		$obj->fields['admin_hidden']['value']	= '0';
		$obj->fields['admin_isroot']['value']	= $_POST['rol_id'] == 1 ? 1 : 0;
		$obj->fields['admin_timestamp']['value']	= date('Y-m-d H:i:s');

		$local = Cadena_local::select($_POST["local_id"]);
		$local_id = $local[0]['local_codigo'];

		#actualizo local_id funcionario
		if(!isset($_POST['admin_actual'])){
			DB::execute("update funcionario set funcionario_descripcioncargo = 'AUXILIAR DE PREVENCION DE RIESGOS', sucursal_id = '{$local_id}' where funcionario_id = '{$_POST["funcionario_id"]}'");
		}
		
		$datos_rol = Admin_rol::select(numParam('rol_id'));

		$obj->fields['admin_permission']['value'] = $datos_rol[0]['rol_permisos'];
		
		$pass = $_POST['admin_password'];
		
		if($id > 0):
		
			$res = self::select($id);
			$obj->fields['admin_isroot']['value'] = $res[0]['admin_isroot'];

			$obj->fields['admin_user']['value']	= $res[0]['admin_user'];
			
			if(count($res) > 0 && is_array($res)):
			
				$obj->fields['admin_random_seed']['value'] = $res[0]['admin_random_seed'];
				
				if(strlen($pass) == 0):
					$obj->fields['admin_password']['value'] = $res[0]['admin_password'];
				else:
					$obj->fields['admin_password']['value']	= md5($_POST['admin_password'] . "_" . strtoupper(strrev($_POST['admin_email'])));
				endif;
				
				if($_POST['admin_email'] != $res[0]['admin_email']):
					if(strlen($pass) == 0):
						$old_password = Encryption::Decrypt($res[0]['admin_random_seed'], strrev(md5($res[0]['admin_random_key'])));
						$obj->fields['admin_password']['value']	= md5("{$old_password}_" . strtoupper(strrev($_POST['admin_email'])));
					else:
						$obj->fields['admin_random_seed']['value'] = Encryption::Encrypt($pass, strrev(md5($res[0]['admin_random_key'])));
						$obj->fields['admin_password']['value']	= md5($_POST['admin_password'] . "_" . strtoupper(strrev($_POST['admin_email'])));
					endif;
				endif;

			endif;
		else:

			$random_key = uniqcode(16,16);
			if(strlen($pass) > 0):
				$obj->fields['admin_random_key']['value']	= $random_key;
				$obj->fields['admin_random_seed']['value']	= Encryption::Encrypt($pass, strrev(md5($random_key)));
				$obj->fields['admin_password']['value']		= md5($_POST['admin_password'] . "_" . strtoupper(strrev($_POST['admin_email'])));
			endif;
			
		endif;

		
		if($obj->validate($obj,$id)):

			if($id == 0){
				$from = array("noresponder@bitacora-ajvierci.com.py" => "Bitacora Prevención");
                $subject = "Datos de acceso Sistema Bitacora Retail";
                $template = "alert_template.html";
                $data['logo'] = baseURL."images/bitacora_small.jpg";
                $data['title'] = "Nuevo usuario de sistema Retail Bitacora";

                 // para administrador
                 $data["content"] = '<p class="parrafo center">
                                       Hola '.$_POST["admin_names"].'
                                      </p>
                                      <p>Notificación de creación de usuario de sistema Bitácora de Prevención Div. Retail</p>
                               
                                      <p><strong>DATOS DE ACCESO</strong></p>
                                      <p>Usuario: <strong>'.$_POST["admin_user"].'</strong></p>
                                      <p>Clave temporal: <strong>'.$_POST["admin_password"].'</strong></p>
                                      <p><a style="padding: 5px 20px; border-radius: 10px; background: #5bc0de; color: white;" href="'.baseURL.'">Ingresar al sistema</a></p>
                                      <hr>';
                 $to = array($_POST['admin_email'] => "Sistema Bitacora Prevención");
                // Mail::send($from, $to, $subject, $template, $data); //mail para el que se esta suscribiendo
			}
			$obj->update($obj, $id);
		else:
			Message::set("Por favor complete correctamente el formulario para continuar", MESSAGE_ERROR);
			return $obj->error;
		endif;
	}
	
	//-- oculta o elimina un registro
	public static function delete($id){
		$obj = new self();
		
		$admin = self::select($id);
		
		if(count($admin) > 0):
			//$root = $admin[0]['admin_isroot'] == 1 ? true : false;
			$root = $id == 1 ? true : false;
		else:
			$root = false;
		endif;
		
		if(!$root):
			$delete = "UPDATE admins SET admin_hidden = 1 WHERE " . $obj->primaryKey . " = {$id}";
			$obj->Execute($delete);
		else:
			setApplicationJavascript();
			print "alert('El usuario es ROOT y no se puede eliminar');";
			exit;
		endif;
	}
	
	public static function get($where=null,$order=null){
		$obj = new self();
		$whr = $where == null ? "" : "{$where} AND ";
		$ord = $order == null ? "" : " ORDER BY {$order}";
		$sql = "SELECT * FROM " . $obj->tableName . " WHERE {$whr}admin_status = 1 AND admin_hidden = 0{$ord}";
		return $obj->execute($sql);
	}
	
	public static function set($field, $value, $where=null){
		$obj = new self();
		$obj->change($obj->tableName, $field, $value, $where);
	}
	
	public static function select($id){
		$obj = new self();
		return $obj->find($obj->tableName, $obj->primaryKey, $id);
	}
	
	public static function resetpassword($email, $newpassword){
		$obj = new self();
		
		$res = $obj->find($obj->tableName, "admin_email", $email);
		
	}
	
	public static function bulk($action, $ids){
		$obj = new self();
		$ids = json_decode($ids);
		
		switch($action):
			//activar
			case "1":
				foreach($ids as $id):
					$obj->change($obj->tableName, "admin_status", 1, $obj->primaryKey . " = {$id}");
				endforeach;
				break;
			//desactivar
			case "2":
				foreach($ids as $id):
					$obj->change($obj->tableName, "admin_status", 0, $obj->primaryKey . " = {$id}");
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
	
	public static function permission($table,$action=null){
		if(login()):
			if(Login::get("rol_id") == 1):
				return true;
			else:
				
				//$data = self::select(Login::get("admin_id"));
				$data = Admin_rol::select(Login::get("rol_id"));
				
				if(haveRows($data)):
				
					$data = $data[0];
					$permission = @json_decode($data['rol_permisos']);

					$roles = objectToArray(json_decode($data['rol_permisos']));
					$rol_admin = array("admins", "admin_rol", "evento_tipo", "tipo", "policia", "lista_persona");
					$rol_gerente = array("gerente_ingreso", "gerente_egreso");
					$rol_trabajos = array("proveedor" , "trabajo_tipo" , "proveedor_evento");
					$rol_locales = array("cadena", "local_apertura", "local_cierre", "local_evento","local_aperturacierre");
					$rol_ingresos = array("persona", "persona_egreso", "persona_reporte");
					$rol_estacionamiento = array("estacionamiento_ingreso", "estacionamiento_evento", "estacionamiento_reporte");
					$rol_marcacion = array("local_punto", "local_marcacion");
					$rol_ayuda = array("como_empezar");
					
					/**/
					foreach ($roles as $k => $v) {
			            if(!isset($permiso['admin']) || $permiso['admin'] == false):
			                $permiso['admin'] = in_array($k, $rol_admin) ? true : false;
			            endif;
			            if(!isset($permiso['gerente']) || $permiso['gerente'] == false):
			                $permiso['gerente'] = in_array($k, $rol_gerente) ? true : false;
			            endif;
			            if(!isset($permiso['trabajos']) || $permiso['trabajos'] == false):
			                $permiso['trabajos'] = in_array($k, $rol_trabajos) ? true : false;
			            endif;
			            if(!isset($permiso['locales']) || $permiso['locales'] == false):
			                $permiso['locales'] = in_array($k, $rol_locales) ? true : false;
			            endif;
			            if(!isset($permiso['ingresos']) || $permiso['ingresos'] == false):
			                $permiso['ingresos'] = in_array($k, $rol_ingresos) ? true : false;
			            endif;
			            if(!isset($permiso['estacionamiento']) || $permiso['estacionamiento'] == false):
			                $permiso['estacionamiento'] = in_array($k, $rol_estacionamiento) ? true : false;
			            endif;
			            if(!isset($permiso['ayuda']) || $permiso['ayuda'] == false):
			                $permiso['ayuda'] = in_array($k, $rol_ayuda) ? true : false;
			            endif;
			            if(!isset($permiso['local_punto']) || $permiso['local_punto'] == false):
			                $permiso['local_punto'] = in_array($k, $rol_marcacion) ? true : false;
			            endif;
			        }

			        if($table == 'config' && $permiso['admin'] == true){
			        	return true;
			        }
			        if($table == 'gerente_horario' && $permiso['gerente'] == true){
			        	return true;
			        }
			        if($table == 'trabajos' && $permiso['trabajos'] == true){
			        	return true;
			        }
			        if($table == 'locales' && $permiso['locales'] == true){
			        	return true;
			        }
			        if($table == 'personas' && $permiso['ingresos'] == true){
			        	return true;
			        }
			        if($table == 'estacionamiento' && $permiso['estacionamiento'] == true){
			        	return true;
			        }
			        if($table == 'help' && $permiso['ayuda'] == true){
			        	return true;
			        }

			        if($table == 'local_aperturacierre' && $permiso['locales'] == true){
			        	return true;
			        }
					/**/
					
					if($permission instanceof stdClass):
						
						if($action == null):
							eval('$allow_insert = $permission->' . $table . '->insert == 1 ? true : false;');
							eval('$allow_view = $permission->' . $table . '->view == 1 ? true : false;');
							eval('$allow_update = $permission->' . $table . '->update == 1 ? true : false;');
							eval('$allow_export = $permission->' . $table . '->export == 1 ? true : false;');
							eval('$allow_delete = $permission->' . $table . '->delete == 1 ? true : false;');
							return $allow_insert || $allow_view || $allow_export || $allow_update || $allow_delete ? true : false;
						else:

							foreach ($roles as $k => $v) {
					            if(!isset($permiso['admin']) || $permiso['admin'] == false):
					                $permiso['admin'] = in_array($k, $rol_admin) ? true : false;
					            endif;
					            if(!isset($permiso['gerente']) || $permiso['gerente'] == false):
					                $permiso['gerente'] = in_array($k, $rol_gerente) ? true : false;
					            endif;
					            if(!isset($permiso['trabajos']) || $permiso['trabajos'] == false):
					                $permiso['trabajos'] = in_array($k, $rol_trabajos) ? true : false;
					            endif;
					            if(!isset($permiso['locales']) || $permiso['locales'] == false):
					                $permiso['locales'] = in_array($k, $rol_locales) ? true : false;
					            endif;
					            if(!isset($permiso['ingresos']) || $permiso['ingresos'] == false):
					                $permiso['ingresos'] = in_array($k, $rol_ingresos) ? true : false;
					            endif;
					            if(!isset($permiso['estacionamiento']) || $permiso['estacionamiento'] == false):
					                $permiso['estacionamiento'] = in_array($k, $rol_estacionamiento) ? true : false;
					            endif;
					            if(!isset($permiso['ayuda']) || $permiso['ayuda'] == false):
					                $permiso['ayuda'] = in_array($k, $rol_ayuda) ? true : false;
					            endif;

					            if(!isset($permiso['punto']) || $permiso['punto'] == false):
					                $permiso['punto'] = in_array($k, $rol_marcacion) ? true : false;
					            endif;
					        }

							if($table == 'config' && $permiso['admin'] == true){
								eval('$allow =  true');
							}else{
								eval('$allow = $permission->' . $table . '->' . strtolower($action) . ' == 1 ? true : false;');
							}
							return $allow;
						endif;

					else:
						return false;
					endif;
					
				else:
					return false;
				endif;
				
			endif;
		else:
			return false;
		endif;
	}
	
	
	public static function getfields(){
		$obj = new self();
		return $obj->fields;
	}

	public static function combobox($selected=null,$onchange=null,$class=null){
		$obj = new self();
		$fsel = ($selected == null || $selected == 0) ? ' selected="selected"' : '';
		//$list = "SELECT admin_id, admin_names FROM admins WHERE admin_status = 1 AND admin_id > 1 AND admin_hidden = 0 ORDER BY admin_names ASC";
		$list = "SELECT admin_id, admin_names FROM admins WHERE admin_status = 1 AND admin_hidden = 0 ORDER BY admin_names ASC";
		$list = $obj->exec($list);
		print '<select name="admin_id" id="admin_combo" class="form-control '.$class.'" style="color:#000;">';
			print '<option value=""'.$fsel.'>Seleccionar</option>';
			if(is_array($list) && count($list) > 0):
				foreach($list as $dat):
					$select = $dat['admin_id'] == $selected ? ' selected="selected"' : "";
					print '<option value="'.$dat['admin_id'].'"'.$select.'>'.htmlspecialchars($dat['admin_names']).'</option>';
				endforeach;
			endif;
		print '</select>';
	}
	
}
?>