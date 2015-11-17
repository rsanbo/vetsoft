function CustomAlert(){
	this.render = function(title, dialog, dir){
		var winW = window.innerWidth;
	    var winH = window.innerHeight;
		var dialogoverlay = document.getElementById('dialogoverlay');
	    var dialogbox = document.getElementById('dialogbox');
		dialogoverlay.style.display = "block";
	    dialogoverlay.style.height = winH+"px";
		dialogbox.style.left = (winW/2) - (550 * .5)+"px";
	    dialogbox.style.top = "100px";
	    dialogbox.style.display = "block";
		document.getElementById('dialogboxhead').innerHTML = title;
	    document.getElementById('dialogboxbody').innerHTML = dialog;
	  	if(dir == ""){
	  		document.getElementById('dialogboxfoot').innerHTML = '<a onclick="Alert.ok()" class="btn btn-default opcion" role="button">Aceptar</a>';
		}else{
			document.getElementById('dialogboxfoot').innerHTML = '<a href='+dir+' class="btn btn-default opcion" role="button">Aceptar</a>';
		}
	}
	this.ok = function(){
		document.getElementById('dialogbox').style.display = "none";
		document.getElementById('dialogoverlay').style.display = "none";
	}
}
var Alert = new CustomAlert();