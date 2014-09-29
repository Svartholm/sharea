function lire(id) {
	document.getElementById("playercontainer").style.display = 'block';
	var player = document.getElementById('player');
	player.setAttribute("data", "/dewplayer-rect.swf?mp3=/download/"+id+"&autoplay=1");
}

function initPlayer() {
	var player = document.getElementById('player');
	player.setAttribute("data", "/dewplayer-rect.swf?mp3=/download/0&autoplay=1");
}

function showAudioOrNot() {
	playercontainer = document.getElementById("playercontainer");
	if(playercontainer.style.display == 'none' || playercontainer.style.display == '') {
		playercontainer.style.display = 'block';
	}
	else {
		playercontainer.style.display = 'none';
	}
}

function showNotesOrNot() {
	notes = document.getElementById("notes");
	if(notes.style.display == 'none' || notes.style.display == '') {
		notes.style.display = 'block';
	}
	else {
		notes.style.display = 'none';
	}
}

function drag() {
	var container = document.getElementById("playercontainer");
	var px = parseInt(container.style.marginLeft);
	var py = parseInt(container.style.marginTop);
	var dpx, dpy, upx, upy;

	function down(e) {
		dpx = e.clientX;
		dpy = e.clientY;
		document.onmousemove = bouge;
		e.preventDefault();
	}
 	function bouge(e) {
		container.style.marginLeft = px + (e.clientX - dpx) + "px";
		if(py + (e.clientY - dpy) > 0)
			container.style.marginTop = py + (e.clientY - dpy) + "px";
		container.style.zIndex = 1;
	}
	function up(e) {
		document.onmousemove = '';
		px = parseInt(container.style.marginLeft);
		py = parseInt(container.style.marginTop);
	}
	container.addEventListener('mousedown', down, false);
	container.addEventListener('mouseup', up, false);
}

