<?php
class Log{
	
	public static function write($data, $file=null, $omit_select=true){
		
		
		if(logEnabled):
			
			$file_name = $file == null ? date("Ymd").".dblog" : $file;
			$log_data = str_replace(array("\n","\t"), ' ', trim($data));
			
			if($omit_select):
			
				if(strlen(trim($data)) > 0 && strtoupper(substr($log_data,0,6) != "SELECT")):
						
					$log_file = @fopen(pathToLog . $file_name, "a+");
					
					if(is_resource($log_file)):
						$log_data = '['.date("Y-m-d H:i:s").']: ' . $log_data . "\n";
						@fwrite($log_file, $log_data);
						@fclose($log_file);
					endif;
					
				endif;
				
			else:
			
				$log_file = @fopen(pathToLog . $file_name, "a+");
					
				if(is_resource($log_file)):
					$log_data = '['.date("Y-m-d H:i:s").']: ' . $log_data . "\n";
					@fwrite($log_file, $log_data);
					@fclose($log_file);
				endif;
			
			endif;
			
		endif;
		
	}
}
?>