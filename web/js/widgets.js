function getMemo()
{
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange  = function() 
	{
		if(xhr.readyState  == 4)
		{
			if(xhr.status  == 200)
			{
				var re = check(xhr.responseText, true, false);
				if(re !== false)
				{
					document.getElementById("notesarea").innerHTML = re.memo["content"];
					document.getElementById("idmemo").value = re.memo["id"];
				}
			}
		}
	}; 
	xhr.open( "GET", "/ajax/widget/memo/get",  true);
	xhr.send(null);
}

function saveMemo()
{
	document.getElementById("savebutton").disabled = "disabled";
	jQuery.ajax({
		type: 'POST',
		url: '/ajax/widget/memo/save',
		data:
			{
			idmemo: document.getElementById("idmemo").value,
			notesarea: document.getElementById("notesarea").value,
			},
		success: function(data, textStatus, jqXHR)
		{
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			alert("Erreur lors de l'enregistrement de la fiche mémo");
		}
	});
}

function showBtnSave()
{
	document.getElementById("savebutton").disabled = "";
}
