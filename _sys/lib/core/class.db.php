<?php
class db{

	public static function execute($sql){
		return Mysql::exec($sql);
	}
	
	public static function commit(){
		return Mysql::commit();
	}

}
?>
