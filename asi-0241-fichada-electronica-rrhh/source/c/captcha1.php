<?php
if(!isset($_SESSION))session_start();
//$caracteres=array('a','b','c','d','e','f','g','h','y','j','k','n','m','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','J','K','N','M','P','Q','R','S','T','U','V','W','X','Y','Z');
if(isset($_GET['prueba']) && $_GET['prueba']=1 && $_SERVER['REMOTE_ADDR']=='10.78.13.220'){
	$prueba=true;
}
else $prueba=false;
$_SESSION['ctp']='';
$cp="";
$fuentes=array();
$colores=array();
$fn="";
for($i=0;$i<5;$i++){
	$n=$i*2+1;
	$cp.= rand(1,9);//rand(0,1)?($caracteres[rand(0,45)]):
	$x=rand($n,$n+1);
	$fuentes[]=$x;
}
$_SESSION['ctp']=sha1(strtoupper($cp)."-1");
$im = @imagecreatetruecolor(225, ($prueba?140:90)) or die('No se puede Iniciar el nuevo flujo a la imagen GD');
$fondo = imagecolorallocate($im, 255, 255, 255);//blanco
$texto = imagecolorallocate($im, 0, 0, 0);//negro;
$colores[] = imagecolorallocate($im, 0, 0, 0);//negro
$colores[] = imagecolorallocate($im, 233, 5, 91);//magenta
$colores[] = imagecolorallocate($im, 5, 150, 91);//verde
$colores[] = imagecolorallocate($im, 50, 91, 233);//azul
$colores[] = imagecolorallocate($im, 150, 91, 233);//violeta
$colores[] = imagecolorallocate($im, 200, 0, 200);//violeta
shuffle($fuentes);
shuffle($colores);
imagefill($im,0,0,$fondo);
if($prueba)imagestring($im, 5, 5, 0, "Texto: ".$cp, $texto);
if($prueba)imagestring($im, 5, 5, 20, "Fuente:".implode(',',$fuentes), $texto);
for($i=0;$i<strlen($cp);$i++){
	if($fuentes[$i]==4 || $fuentes[$i]==6)$f=40;
	else $f=45;
	imagettftext($im, $f, rand(-15	,15), $i*42+10, rand(55,80)+($prueba?40:0), $colores[$i], "../l/".$fuentes[$i].".ttf", substr($cp,$i,1));
}
header ('Content-Type: image/png');
imagepng($im) or die("2");
imagedestroy($im);

?>