<?php
session_set_cookie_params(0);
session_start();
header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
header("Cache-Control: no-store");
header("Pragma: no-cache");
require "c/funciones.php";
$cookie=isset($_COOKIE["fichadas_web"])?$_COOKIE["fichadas_web"]:"";
$logout="<script>parent.document.location='login.php?e=1';document.location='login.php';</script>";
$info="";
if(isset($_SESSION['fichadas_web'])){
	function get_fichadas(){
		$salida="";
		$datos_fichadas_list=curl($_SESSION['ws_config']['sw_fichadas_url']."/ConsultarRegistro/".$_SESSION['fichadas_web']['id_hr'],false,false,"client_id:".$_SESSION['ws_config']['sw_fichadas_uss'].";client_secret:".$_SESSION['ws_config']['sw_fichadas_pss']);
		if(isset($datos_fichadas_list['error'],$datos_fichadas_list['listado']) && intval($datos_fichadas_list['error'])<100){
			if(count($datos_fichadas_list['listado'])){
				$salida.="<div class='centrado'><div>Historial</div><table><tr><th>Fecha</th><th>Hora</th><th>Acci&oacute;n</th></tr>";
				foreach($datos_fichadas_list['listado'] as $linea=>$fichada_linea){
					$salida.="<tr class='tr".($linea%2)."'><td>".$fichada_linea['FECHA']."</td><td>".$fichada_linea['HORA']."</td><td>".$fichada_linea['TIPO_FICHADA']."</td></tr>";
				}
				$salida.="</table>";
			}
			else{
				$salida.="No hay fichadas registradas.";
			}
		}
		else{
			$salida.="<div class='rojo'>Error al consultar las fichadas.</div>";
			if($_SESSION['fichadas_web']['admin']){
				$salida.="Servicio: ".$_SESSION['ws_config']['sw_fichadas_url']."/ConsultarRegistro/".$_SESSION['fichadas_web']['id_hr']."<br>";
				$salida.= "Variables:<br>";
				$salida.= "datos_fichadas_list['error']: ".(isset($datos_fichadas_list['error'])?$datos_fichadas_list['error']:"no declarada")."<br>";
				$salida.= "datos_fichadas_list['listado']: ".(isset($datos_fichadas_list['listado'])?$datos_fichadas_list['listado']:"no declarada")."<br>";
				$salida.= "Respuesta del servicio:<br>";
			}
		}
		return $salida;
	}
	
	// Deslogueo por timeout
	if((@$_SESSION['fichadas_web']['actividad']<=(time()-intval(@$_SESSION['ws_config']['log_timeout'])))){
		$parent=isset($_POST['fichar'])?"parent.":"";
		die("<script>".$parent."document.location='login.php?e=1';</script>");
	}
	if($_SESSION['fichadas_web']['admin']){
		if(isset($_GET['phpinfo'])){
			die(phpinfo());
		}
	}
	
	// Listado fichada
	if(isset($_GET['listado'])){
		echo get_fichadas();
	}
	
	// Registrar fichada
	
	elseif(isset($_POST['fichar'])){
		$datosp=curl($_SESSION['ws_config']['sw_fichadas_url']."/registrar",false,'{
"id_hr": "'.$_SESSION['fichadas_web']['id_hr'].'",
"tipo_fichada": "'.$_POST['fichar'].'",
"cookie_in": "'.$cookie.'",
"datos_sistema": "'.str_replace('"','\\"',$_SERVER['HTTP_USER_AGENT']).'***'.str_replace('"','\\"',$_SERVER['REMOTE_ADDR']).'***'.$_SESSION['fichadas_web']['geoloc'].'"}',"Content-Type: application/json;client_id:".$_SESSION['ws_config']['sw_fichadas_uss'].";client_secret:".$_SESSION['ws_config']['sw_fichadas_pss']);
		$color='#cc0000';
		if(isset($datosp['error'])){
			if(intval($datosp['error'])<100)$color='#00cc00';
			if(isset($datosp['cookie_out']))setcookie("fichadas_web",$datosp['cookie_out'],$config['cookie_timeout']);
			die("<script>a=parent.document.getElementById('info');a.innerHTML='".$datosp['resultado']."<div class=\"centrado link_azul\" onClick=\"update();\">Actualizar</div>';a.style.color='".$color."';</script>");
		}
		else{
			die("<script>a=parent.document.getElementById('info');a.innerHTML='Error al procesar la fichada.';a.style.color='".$color."';</script>");
		}
	}
	
	// HTML listado de fichadas
	
	elseif(isset($_SERVER["HTTP_REFERER"]) && substr($_SERVER["HTTP_REFERER"],-9)=="login.php" && ($_SESSION['fichadas_web']['actividad']+$_SESSION['ws_config']['log_passthrough'])>$_SERVER["REQUEST_TIME"]){
		echo '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SSGRH - Fichadas</title>
<link href="c/estilos.css?1" rel="stylesheet" type="text/css" />
<script src="c/funciones.js?'.$_SESSION['ws_config']['sw_fichadas_pss'].'" type="text/javascript"></script>
<script>setTimeout(function(){document.location="login.php?e=1";},'.intval(@$_SESSION['ws_config']['log_timeout']).'000);</script>
</head>
<body>
<div id="usuario_frame" class="usuario">
<table><tbody>
<tr><td rowspan="3"><div class="usuario_img"></div></td>
<td>'.$_SESSION['fichadas_web']['apellido'].', '.$_SESSION['fichadas_web']['nombre'].'</td></tr>
<tr><td>'.$_SESSION['fichadas_web']['mail'].'</td></tr>
<tr><td>CUIL: '.$_SESSION['fichadas_web']['cuil'].' - <a href="login.php">Salir</a></td></tr></tbody>
</table>
</div>
<div id="main" class="login_img">
<div class="titulo_negro centrado">Fichada Electr&oacute;nica</div>
<div class="centrado">Registrar una nueva</div>
<div id="listado">
<div class="centrado"><form target="targets" action="" method="POST" style="margin:0px;"><input class="boton_reg" type="submit" name="fichar" value="Entrada"><input class="boton_reg" type="submit" name="fichar" value="Salida"></form></div>';
echo '<div id="info" class="centrado">'.@$_SESSION['info'].'</div><div class="centrado" id="main_cont">';

//------------- Logueado: listado de fichadas ---------------// onunload="logout();"
	
	echo get_fichadas();
	echo '</div></div>
</div>
<div id="pie">
<div id="pie_cont_i">
	<div class="bg_ba_t" id="logo_MHFGC"></div>
	<div class="bg_ba_t" id="logo_RH"></div>
	<div class="bg_ba_t" id="logo_DGPLYCO"></div>
</div>
</div>
<iframe name="targets" id="targets"></iframe>
</body>
</html>';
	}
	else{die($logout);}
}
else{die($logout);}


?>