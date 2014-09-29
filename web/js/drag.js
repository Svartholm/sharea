(function() {
	function FileDragHover(e)
	{
		/* Drag detection */
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == "dragover" ? "hover" : "");
	}


	function FileSelectHandler(e)
	{
		/* File selection */
		FileDragHover(e);
		if(navigator.appName != "Microsoft Internet Explorer")
			var files = e.target.files || e.dataTransfer.files;
		else
			var files = document.getElementById('fileselect[]').files;
		print_table(files);
		var submit_button = document.getElementById('submitbutton');
		submit_button.onclick = function()
		{
			if(document.getElementById('formfile').style.display == 'none')
				{
					this.disabled = 'disabled';
					this.innerHTML = "Envoi en cours...";
					UploadFile(files, 0, files.length, 0);
				}
		};
	}
	
	function print_table(files)
		{
			var i;
			var table = document.getElementById('body_table');
			
			document.getElementById('formfile').style.display = 'none';
			document.getElementById('pre_upload').style.display = 'block';
			document.getElementById('remove_list').style.display = 'block';
			
			table.innerHTML = "";
			
			for(i = 0; i < files.length; i++)
				{
					file = files[i];
					table.innerHTML = table.innerHTML + "<tr><td>"+(i+1)+"</td><td style=\"width: 210px;\">"+file.name+"</td><td>~"+(file.size/1024/1024).toFixed(2)+"Mo</td><td><i id=\""+i+"\" class=\"icon-refresh\" style=\"visibility: hidden\"></i></td></tr>";
				}
		}

	function UploadFile(files, index, size, failed)
	{
		if(index >= size)
			{
				if(failed == 0) {
					document.getElementById('submitbutton').disabled='';
				}
				else if(failed < size && failed > 0) {
					alert(size-failed+" fichiers sur "+size+" ont été envoyés");
					document.getElementById('submitbutton').disabled='';
				}

                document.getElementById('formfile').style.display = 'block';
                document.getElementById('pre_upload').style.display = 'none';
                document.getElementById('remove_list').style.display = 'none';

                if (document.cookie == null || readCookie("display") == 0) {
                    displayList();
                }
                else {
                    displayIcon();
                }
				return true;
			}
			
		var file = files[index];
		var xhr = new XMLHttpRequest();
		
		xhr.onreadystatechange = function(e)
		{
			if (xhr.readyState == 4)
				{
					if(xhr.status == 200)
						{
							var json_obj = JSON.parse(xhr.responseText);
							if(!json_obj.json_error)
								{
									document.getElementById(index).setAttribute('class', 'icon-ok');
								}
							else
								{
									document.getElementById(index).setAttribute('class', 'icon-remove');
									failed++;
									//error_details[index] = json_obj.json_error;
								}
						}
					else
						{
							document.getElementById(index).setAttribute('class', 'icon-remove');
							failed++;
						}
					
					document.getElementById(index).style.visibility = 'visible';
						
					document.getElementById('current').innerHTML = '';
					document.getElementById('total').innerHTML = '';
					document.getElementById('progress').style.width = '0%';
					document.getElementById('percent_upload').innerHTML = '';
						
					if(index < size)
						{
							index++;
							UploadFile(files, index, size, failed);
						}
				}
		};

		if(index < size)
			{
				document.getElementById('nbr').innerHTML = '('+(index+1)+'/'+size+')';
			}
			
		document.getElementById('fileName').innerHTML = '<em>' + file.name + '</em>';
		document.getElementById(index).style.visibility = 'visible';
		
		xhr.upload.onprogress = function check(e)
		{
			var percent = e.loaded / e.total * 100;
			document.getElementById('current').innerHTML = ' ('+(e.loaded/1024/1024).toFixed(2)+'Mo sur ';
			document.getElementById('total').innerHTML = (e.total/1024/1024).toFixed(2)+'Mo)';
			document.getElementById('progress').style.width = Math.round(percent)+'%';
			document.getElementById('percent_upload').innerHTML = percent.toFixed(2)+'%';
		};
				
		xhr.open("POST", "/ajax/upload", true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.setRequestHeader('X-File-Type', file.type);
		xhr.setRequestHeader('X-File-Size', file.size);
		xhr.setRequestHeader('X-File-Name', file.name);
		xhr.setRequestHeader('X-Folder', document.getElementById('folder').value);
		xhr.send(file);
	}


	// Initialisation
	function Init()
	{
		var remove_button = document.getElementById('remove_list');
		remove_button.onclick = function()
		{
			document.getElementById('pre_upload').style.display = 'none';
			document.getElementById('remove_list').style.display = 'none';
			document.getElementById('formfile').style.display = 'block';
		};
		
		var fileselect = document.getElementById("fileselect[]"),
			filedrag = document.getElementById("filedrag");
	
		fileselect.addEventListener("change", FileSelectHandler, false);

		var xhr = new XMLHttpRequest();
		
		if (xhr.upload)
			{
				filedrag.addEventListener("dragover", FileDragHover, false);
				filedrag.addEventListener("dragleave", FileDragHover, false);
				filedrag.addEventListener("drop", FileSelectHandler, false);
				filedrag.style.display = "block";
			}
	}

	// Lancement de l'init
	if (window.File && window.FileList && window.FileReader)
		{
			Init();
		}


})();


var bardiv = document.getElementById('bardiv');
var formfile = document.getElementById('formfile');
var span_right = document.getElementById('span_right');
var submit_ie = document.getElementById('submit_ie');
var filedrag = document.getElementById('filedrag');

if (navigator.appName == "Microsoft Internet Explorer") {
    bardiv.style.display = 'none';
    span_right.style.display = 'none';
    submit_ie.style.display = 'block';
    filedrag.style.display = 'none';

    formfile.setAttribute('action', '/upload');
    formfile.setAttribute('method', 'POST');
    formfile.setAttribute('enctype', 'multipart/form-data');
}
