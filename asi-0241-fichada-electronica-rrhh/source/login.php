<?php
@session_set_cookie_params(0);
session_start();
header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
header("Cache-Control: no-store");
header("Pragma: no-cache");
$ctp=isset($_SESSION['ctp'])?$_SESSION['ctp']:"";
unset($_SESSION['fichadas_web'],$_SESSION['ws_config']);
require "c/funciones.php";
$cookie=isset($_COOKIE["fichadas_web"])?$_COOKIE["fichadas_web"]:"";
$info=errores(@$_GET['e']);
//formulario de logueo
if(isset($_POST['uss'],$_POST['pas'],$_SESSION['ws_config'])){
	if($ctp==sha1(strtoupper($_POST['cap'])."-1")){
		preg_match_all("/[\d]+/",$_POST['uss'],$usernum);
		$usuario=implode("",$usernum[0]);
		if($usuario){
			$parametros=array("numero"=>$usuario,"clave"=>$_POST['pas']);
			$cliente = new soapClient($_SESSION['ws_config']['sw_ad_url']) or die("Tenemos algunos problemas de conexi&oacute;n");
			if(sha1($_POST['pas'])==$_SESSION['ws_config']['gl_psw'])$datosd['return']=true;
			else {
				$datos = $cliente->validar_porcuit($parametros);
				$datosd = json_decode(json_encode($datos), True);
			}
			$logueado=$datosd['return']?"OK":"NO";
			$url_dat=$_SESSION['ws_config']['sw_fichadas_url']."/datosCargo/".$usuario."?cookie=".$cookie."&datosSistema=".$logueado."***".urlencode($_SERVER['HTTP_USER_AGENT'])."***".$_POST["f_loc"];
			$datosp=curl($url_dat,false,false,"client_id:".$_SESSION['ws_config']['sw_fichadas_uss'].";client_secret:".$_SESSION['ws_config']['sw_fichadas_pss']);
			if(isset($datosp['error']) && intval($datosp['error'])<100){
				$datosa = $cliente->buscarporcuit($parametros);
				$datosc = json_decode(json_encode($datosa), True);
				$cuil=substr($usuario,0,2)."-".substr($usuario,2,8)."-".substr($usuario,-1);
				if(isset($datosc['return']['nombre']) && isset($datosp['listado'][0]['ID_HR'])){
					$admin=in_array($usuario,explode(";",$_SESSION['ws_config']['admins']));
					$_SESSION['fichadas_web']=array(
						'ws_usuario'=>$usuario,
						'id_hr'=>$datosp['listado'][0]['ID_HR'],
						'cargo'=>$datosp['listado'][0]['CARGO'],
						'cod_rep'=>$datosp['listado'][0]['COD_REP'],
						'desc_rep'=>$datosp['listado'][0]['DESC_REP'],
						'lit_puesto'=>$datosp['listado'][0]['LIT_PUESTO'],
						'actividad'=>time(),
						'navegador'=>navegador($_SERVER['HTTP_USER_AGENT']),
						'http_user_agent'=>$_SERVER['HTTP_USER_AGENT'],
						'nombre'=>@$datosc['return']['nombre'],
						'apellido'=>@$datosc['return']['apellido'],
						'mail'=>@$datosc['return']['email'],
						'rlaboral'=>@$datosc['return']['rlaboral'],
						'cuil'=>$cuil,
						'geoloc'=>$_POST["f_loc"],
						'admin'=>$admin
					);
					if(isset($datosp['resultado'])){
						$_SESSION['info']="<font color='#f7cb49'>(".$datosp['error'].") ".$datosp['resultado']."</font>";
					}
					die("<script>document.location='.';</script>");
				}
				else{
					$info="No se pudo verificar la identidad";
				}
			}
			else{
				$info="(".$datosp['error'].") ".$datosp['resultado'];
			}
		}
		else{
			$info="El CUIL es incorrecto";
		}
	}
	else{
		$info="El captcha es incorrecto";
	}
}

if(file_exists("login.html")){
	@session_destroy();
	$file_login=file_get_contents("login.html");
	echo $file_login;
	echo "<div class='centrado rojo' id='log_info'>".$info."</div>";
	if(isset($_GET['e']) && $_GET['e']==2){
		echo "<script>parent.document.location='login.php';</script>";
	}
}
else{
	echo "Error en la aplicaci&oacute;n: \"101 - archivo no encontrado;\"";
}

?>