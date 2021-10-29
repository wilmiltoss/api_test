<?php
function pr($data){
    print "<pre>\n";
    print_r($data);
    print "</pre>\n";
}

function Error($message){
	clearBuffer();
	print '<html><head><title>' . $message . '</title></head><body style="background-color:#FFC;">';
	print '<pre><h1>'. sysName .' (' . sysVersion . ')</h1></pre>';
	pr('<span style="color:#f00; font-size:16px;"><strong>' . $message . '</strong></span>');
	pr('<em>' . $_SERVER['REQUEST_METHOD'] . " ($_SERVER[SERVER_PROTOCOL]) : " . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '</em>');
	print '</body></html>';
	exit;
}

function token($key){	
	return Encryption::Encrypt($key);
}

function redirect($uri){
	session_write_close();
	clearBuffer();
	header("Location: $uri");
	exit;
}

function login(){
return Login::status();
}

function access($table){
	return Admins::permission($table);
}

function permissionInsert($table){
	return Admins::permission($table,'insert');
}

function permissionUpdate($table){
	return Admins::permission($table,'update');
}

function permissionDelete($table){
	return Admins::permission($table,'delete');
}

/*genera codigo aleatorio*/
/*Longitud variable entre (int)@minLen y (int)@maxLen*/
function uniqcode($minLen=5,$maxLen=9,$sym=true){
	
	// [1] Letras Mayusculas de la A a la Z (65-90)
	// [2] Letras Minusculas de la A a la Z (97-122)
	// [3] Numeros del 0 al 9 (48-57)
	// [4] Caracterees ()*+ (40-43)
	// [5] Caracteres -. ()(45-46)
	$ret = "";
	$totalLen = ($minLen == $maxLen) ? $maxLen : rand($minLen, $maxLen);
	$maxblock = $sym ? 5 : 3;
	
	
	for($len = 1; $len <= $totalLen; $len++): 
		$block = rand(1,$maxblock);
		switch($block):
			case 1:
				$char = chr(rand(65,90));
				break;
			case 2:
				$char = chr(rand(97,122));
				break;
			case 3:
				$char = chr(rand(48,57));
				break;
			case 4:
				$char = chr(rand(40,43));
				break;
			case 5:
				$char = chr(rand(45,46));
				break;
		endswitch;
		
		$ret .= $char;
		
	endfor;
	
	return $ret;
}


function randomnumbers($minLen=5,$maxLen=11,$zeroFill=true){

	$totalLen = ($minLen == $maxLen) ? $maxLen : rand($minLen, $maxLen);
	$ret = "";
	for($len = 1; $len <= $totalLen; $len++):
		$ret .= rand(0,9);
	endfor;
	
	return strlen($ret) == $maxLen ? $ret : ( $zeroFill ? str_pad($ret, $maxLen, "0", STR_PAD_LEFT) : $ret );
	
}



function dateDifference($start, $end="NOW"){ 

	 $sdate = strtotime($start);
     $edate = strtotime($end);

        $time = $edate - $sdate;
        if($time>=0 && $time<=59) {
                // segundos
                //$timeshift = $time.' segundos';
				$timeshift = array("sec" => $time);

        } elseif($time>=60 && $time<=3599) {
                // Minutos + Segundos
                $pmin = ($edate - $sdate) / 60;
                $premin = explode('.', $pmin);
                
                $presec = $pmin-$premin[0];
                $sec = $presec*60;
                
                //$timeshift = $premin[0].' min '.round($sec,0).' seg ';
				$timeshift = array("min" => $premin[0], "sec" => round($sec,0));

        } elseif($time>=3600 && $time<=86399) {
                // Horas + Minutos
                $phour = ($edate - $sdate) / 3600;
                $prehour = explode('.',$phour);
                
                $premin = $phour-$prehour[0];
                $min = explode('.',$premin*60);
                
                $presec = '0.'.$min[1];
                $sec = $presec*60;

                //$timeshift = $prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
				$timeshift = array("hour" => $prehour[0], "min" => $min[0], "sec" => round($sec,0));

        } elseif($time>=86400) {
                // Dias + Horas + Minutos
                $pday = ($edate - $sdate) / 86400;
                $preday = explode('.',$pday);

                $phour = $pday-$preday[0];
                $prehour = explode('.',$phour*24); 

                $premin = ($phour*24)-$prehour[0];
                $min = explode('.',$premin*60);
                
                $presec = '0.'.$min[1];
                $sec = $presec*60;
                
                //$timeshift = $preday[0].' dias '.$prehour[0].' hs '.$min[0].' min '.round($sec,0).' seg ';
				$timeshift = array("days" => $preday[0], "hour" => $prehour[0], "min" => $min[0], "sec" => round($sec,0));

        }
        return $timeshift;
}

function setNotFound(){
	clearBuffer();
	header("HTTP/1.1 404 Not Found");
	exit;
}

function setUnauthorized($str="Unauthorized"){
	clearBuffer();
	header("HTTP/1.1 401 Unauthorized");
	Error($str);
}

function setLocked($str="Locked"){
	clearBuffer();
	header("HTTP/1.1 423 Locked");
	Error($str);
}

function setApplicationJavascript(){
	clearBuffer();
	header("Content-type: application/x-javascript");
}

function hule($str=NULL){
	die($str);
}

function getIpAddress() {

	if(empty($_SERVER['HTTP_CLIENT_IP'])):
		if(empty($_SERVER['HTTP_X_FORWARDED_FOR'])):
			$ip = $_SERVER['REMOTE_ADDR'] . "R";
		else:
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] . "F";
		endif;
	else:
		$ip = $_SERVER['HTTP_CLIENT_IP'] . "C";
	endif;
	return $ip;
}

function number($n){
	return is_numeric($n) ? number_format($n,0,"","") : 0;
}

function isValidEmail($email){
	$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
	if(!preg_match($regex, $email)):
		return false;
	else:
		return true;
	endif;
}

function pageNumber(){
	$_GET['page']	= isset($_GET['page']) 	? $_GET['page']		: 0;
	$_POST['page']	= isset($_POST['page'])	? $_POST['page']	: 0;
	return number($_GET['page']) == 0 ? number($_POST['page']) == 0 ? 1 : number($_POST['page']) : number($_GET['page']); 
}

function param($param){
	return isset($_GET[$param]) ? cleanParam($_GET[$param]) : (isset($_POST[$param]) ? cleanParam($_POST[$param]) : "");
}

function numParam($param){
	$value = number(param($param));
	if(isset($_POST[$param])):
		$_POST[$param] = $value;
	elseif(isset($_GET[$param])):
		$_GET[$param] = $value;
	endif;
	return $value;
}

function haveRows($arr){
	return is_array($arr) && count($arr) > 0 ? true : false;
}

function cropWords($string, $limit = 20){
		
	$words = explode(" ", trim($string));
	$w = array();
	$crop="";
	
	//limpia todos los espacios entre las palabras
	foreach($words as $word):
		if(strlen(trim($word)) > 0):
			$w[] = trim($word);
		endif;
	endforeach;
	
	//une las palabras
	$limit = count($words) < $limit ? count($words) : $limit;
	for($i = 0; $i < $limit; $i++):
		$crop .= $w[$i] . " ";
	endfor;
	
	$more = count($w) > $limit ? "..." : "";
	return substr($crop,0,-1) . $more;
}

function clearBuffer(){
	if(ob_get_length()):
		ob_end_clean();
	endif;
}

function monthName($n){
	$monthName = array(
		1 => "Enero",
		2 => "Febrero",
		3 => "Marzo",
		4 => "Abril",
		5 => "Mayo",
		6 => "Junio",
		7 => "Julio",
		8 => "Agosto",
		9 => "Setiembre",
		10 => "Octubre",
		11 => "Noviembre",
		12 => "Diciembre"
	);
	return $monthName[$n];
}

function dayName($d){
	$dayName = array(
		"Domingo",
		"Lunes",
		"Martes",
		"Miércoles",
		"Jueves",
		"Viernes",
		"Sábado",
	);
	
	return $dayName[$d];
}

function dateFormat($format,$time=null){

	$time = $time == null ? time() : $time;

	$formats = array(
		'{d}',
		'{dd}',
		'{ddd}',
		'{dddd}',
		'{m}',
		'{mm}',
		'{mmm}',
		'{mmmm}',
		'{yy}',
		'{yyyy}',
		'{h}',
		'{H}',
		'{i}',
		'{s}'
	);
	
	$replace = array(
		date("j", $time),
		date("d", $time),
		substr(dayName(date("w", $time)),0,3),
		dayName(date("w", $time)),
		date("n", $time),
		date("m", $time),
		substr(monthName(date("n",$time)),0,3),
		monthName(date("n",$time)),
		date("y", $time),
		date("Y", $time),
		date("h", $time),
		date("H", $time),
		date("i", $time),
		date("s", $time)
	);
	
	return str_replace($formats, $replace, $format);
	
}

function slugit($string){
	$string = mb_strtolower($string,'UTF-8');
	$chars  = array("á","ä","â","à","é","ë","ê","è","í","ï","î","ì","ó","ö","ô","ò","ú","ü","û","ù","ñ");
	$repla  = array("a","a","a","a","e","e","e","e","i","i","i","i","o","o","o","o","u","u","u","u","n");
	$string = str_replace($chars, $repla, $string);
	$string = preg_replace("/[^a-zA-Z0-9\s]/", "", $string);
	$string = explode(" ", $string);
	$rtn = "";
	foreach($string as $str):
		if(strlen(trim($str)) > 0):
			$rtn .= $str . "-";
		endif;
	endforeach;
	
	$rtn = substr($rtn,0,-1);
	
	return $rtn;
	
}

function file_get_contents_utf8($fn) { 
	$content = file_get_contents($fn); 
	return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)); 
}

function cleanParam($param){
	$search = array(
		'@<script[^>]*?>.*?</script>@si',   // Elimina javascript
    	'@<[\/\!]*?[^<>]*?>@si',            // Elimina las etiquetas HTML
    	'@<style[^>]*?>.*?</style>@siU',    // Elimina las etiquetas de estilo
    	'@<![\s\S]*?--[ \t\n\r]*>@'         // Elimina los comentarios multi-línea
 	);
    $param = preg_replace($search, '', $param);
	return htmlentities(stripslashes(strip_tags($param)));
}

function uninstall(){
	$pathToMapper 	= pathToLib . '/core/class.mapper.php';
	$pathToCore 	= pathToLib . '/core.lib.php';
	@unlink( $pathToMapper );
	if(file_exists($pathToMapper)):
		print 'El archivo class.mapper.php no se pudo eliminar verifique los permisos.'."\n";
		exit();
	else:
		print 'Archivo class.mapper.php elimminado o no existe.'."\n";
	endif;

	$openfile = @fopen($pathToCore,'r+');
	$content = @fread($openfile,filesize($pathToCore));
	@fclose($openfile);
	$content = str_replace("require_once( pathToLib . '/core/class.mapper.php' );", '', $content);
	$openfile = @fopen($pathToCore,'w');
	@fwrite($openfile,$content);
	@fclose($openfile);

	@unlink('install.php');
	if(file_exists('install.php')):
		print 'El archivo install.php no se pudo eliminar verifique los permisos.'."\n";
		exit();
	else:
		print 'Archivo install.php elimminado o no existe.'."\n";
	endif;
}

function checkConfig(){
	$config = null;
	if(sysName == "CMS"):
		$config .= " <strong>sysName</strong>, ";
	endif;
	if(domainName == "domain.com"):
		$config .= " <strong>domainName</strong>, ";
	endif;
	if(encryptionKey == "hbLfOiutjHSMzM5"):
		$config .= " <strong>encryptionKey</strong>, ";
	endif;
	if($config!=null):
		print '<div class="alert alert-error" style="position:fixed; top:0px; left:50%; margin-left:-400px; z-index:99; width:800px;">Debe cambiar los datos por defecto de: '.$config.' revise su archivo de configuraci&oacute;n.</div>';
	endif;
}

function objectToArray($d) {
	if (is_object($d)) {
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}

function convertir($num){
        $res=$num/60;
        $div=explode('.',$res);
        $hor=$div[0];
        $min=$num - (60*$hor);
        return str_pad($hor, 2, '0', STR_PAD_LEFT).":".str_pad($min, 2, '0', STR_PAD_LEFT);
}

?>