<?php
require "config.php";

//Funciones
function curl($dir,$tok,$post=false,$cabecera=false,$body=false,$decoded=false){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$dir);
	$cabeceras = array();
	if($cabecera){
      $cabeceras = explode(';',$cabecera);
    }
	if($tok){
		$cabeceras[] = 'Authorization: Bearer '.$tok;
	}
	curl_setopt($curl, CURLOPT_HTTPHEADER, $cabeceras);
	if($post){
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	if($decoded)$salida = curl_exec($curl);
	else $salida = json_decode(curl_exec($curl),true);
	curl_close($curl);
	return $salida;
}
function navegador($dato){
	if(preg_match('/MSIE/i',$dato) && !preg_match('/Opera/i',$dato))return "MSIE";
    elseif(preg_match('/Firefox/i',$dato))return "Firefox";
    elseif(preg_match('/Chrome/i',$dato))return "Chrome";
    elseif(preg_match('/Safari/i',$dato))return "Safari";
    elseif(preg_match('/Opera/i',$dato))return "Opera";
    elseif(preg_match('/Netscape/i',$dato))return "Netscape";
}
function fecha_invertir($dato){
	$e=explode("/",$dato);
	return $e[2]."/".$e[1]."/".$e[0];
}
function pedir_token(){
	global $config;
	$tk=curl($config['ws_dotaciones_url']."/autorizar",false,"username=".$config['ws_dotacion_uss']."&password=".$config['ws_dotacion_pss']);
	$_SESSION['token']=$tk['token'];
	$_SESSION['token_vencimiento']=$tk['token_exp'];
}
if(!isset($_SESSION['token']) || @$_SESSION['token_vencimiento']<(time()+800))pedir_token();//800 segundos, delta del vencimiento del token
if(!isset($_SESSION['ws_config'])){
	$curconf=curl($config['ws_dotaciones_url']."/config_ini",$_SESSION['token']);
	if(isset($curconf['p_cur_config'])){
		foreach($curconf['p_cur_config'] as $row){
			if($row['APP']=="fichadas"){
				$_SESSION['ws_config'][$row['CAMPO']]=$row['VALOR'];
			}
		}
	}
}
function errores($e){
	switch($e){
	case 1:
		return "Por seguridad se ha cerrado la sesión.";
		break;
	default:
		return "";
		break;
	}
}

?>