function geoloc(){
	if ("geolocation" in navigator) {
		navigator.geolocation.getCurrentPosition(function(position){
			document.getElementById('f_loc').value=position.coords.latitude+","+position.coords.longitude+","+position.coords.accuracy;
		},function(e){console.log("Error al consultar la localización");},
		{enableHighAccuracy: true});
	}
}
function input_ch(tar){
	info=document.getElementById('info');
	if(typeof document.getElementById('log_info')==='object'){
		document.getElementById('log_info').innerHTML='';
	}
	if(tar.id=='uss'){
		if(tar.value.length<11){
			info.innerHTML='<div class="centrado rojo">El usuario es incorrecto</div>';
		}else info.innerHTML='';
	} else if(tar.id=='pas'){
		console.log(tar.value.length);
		if(tar.value.length<4){
			info.innerHTML='<div class="centrado rojo">Debe completar la contrase\u00F1a</div>';
		}else info.innerHTML='';
	} else if(tar.id=='cap'){
		if(tar.value.length!=5){
			info.innerHTML='<div class="centrado rojo">Debe completar el captcha</div>';
		}else info.innerHTML='';
	}
}
function form_ch(ev,tar){
	ev.preventDefault();
	info=document.getElementById('info');
	if(document.getElementById('uss').value.length>10){
		if(document.getElementById('pas').value.length>3){
			if(document.getElementById('cap').value.length==5){
				return tar.submit();
			}else{
				info.innerHTML='<div class="centrado rojo">Debe completar el captcha</div>';
			}
		}else{
			info.innerHTML='<div class="centrado rojo">Debe completar la contrase\u00F1a</div>';
		}	
	}else{
		info.innerHTML='<div class="centrado rojo">El usuario es incorrecto</div>';
	}
	return false;
}
function update(){
	panel=document.getElementById('main_cont');
	document.getElementById('info').innerHTML="";
	panel.innerHTML="Cargando...";
	var req = new XMLHttpRequest();
	req.open('GET', '?listado', true);
	req.onreadystatechange = function (aEvt) {
		if (req.readyState == 4) {
			if(req.status == 200)
				panel.innerHTML=req.responseText;
			else
				panel.innerHTML="Datos no encontrados: "+req.status;
		}
	};
	req.send(null);
	return true;
}