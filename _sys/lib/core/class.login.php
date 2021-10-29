<?php
class Login extends Mysql {
	
	public static function set($email, $passw){
		
		$obj = new self();

		$result = $obj->execute("SELECT * FROM admins WHERE admin_status = 1 AND (admin_user = '{$email}' OR admin_email = '{$email}') AND admin_hidden = 0");
		
		if(haveRows($result)):
		
			$result = $result[0];

			$email 	= addslashes(strtolower(trim($result['admin_email'])));
			$passw	= md5($passw . "_" . strrev(strtoupper($email)));
			


			$_POST['admin_id'] = $result['admin_id'];
			$_POST['admin_login_ip_address'] = getIpAddress();



			if($result['admin_password'] != $passw || $result['admin_status'] != 1):
				$_POST['admin_login_response'] = "FAILED";
				Admin_login_attempts::save(0);
				return false;
			else:
				foreach($result as $k => $v):
					$data[Encryption::Encrypt($k)] = Encryption::Encrypt($v);
				endforeach;


				$_SESSION[Encryption::Encrypt(adminLogin)]	= $data;
				$_POST['admin_login_response'] = "SUCCESSFUL";
				$_POST['admin_authToken'] = authToken;



				
		
				Admin_login_attempts::save(0);

				$last_login = "UPDATE admins SET admin_last_login = NOW() WHERE admin_id = {$result['admin_id']}";
				$obj->execute($last_login);
				return true;
			endif;
			
		else:
			return false;
		endif;	
		
	}

	public static function status(){
		if(isset($_SESSION[Encryption::Encrypt(adminLogin)])):
			$status = $_SESSION[Encryption::Encrypt(adminLogin)];
			$status = count($status);
			return $status > 0 ? true : false;
		else:
			return false;
		endif;
	}

    public static function get($var){
		
		$data = $_SESSION[Encryption::Encrypt(adminLogin)][Encryption::Encrypt($var)];
		return Encryption::Decrypt($data);
		
	}
	
	public static function access($section){
	
		if(self::status()):
			
			$permission = self::get("admin_permission");
			
			if($permission == "full"):
				return true;
			else:
				if(is_array($permission->access) && count($permission) > 0):
					return in_array($section, $permission->access) ? true : false;
				else:
					return false;
				endif;
			endif;
		else:
			return false;
		endif;
		
	}
	
    //-- crea instancia de la clase
	static private function getInstance($id=null) {
        return new self($id);
    }

}

?>