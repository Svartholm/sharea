function isEmail(valeur)
{
	var reg = new RegExp("^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$");
	if(valeur.match(reg))
	{
		return true;
	}
	else
	{
		return false;
	}
}	

function isName(valeur)
{
	var reg = new RegExp("^[a-zA-Z-àâäùüûéèëôö' ]{2,32}$");
	if(valeur.match(reg))
	{
		return true;
	}
	else
	{
		return false;
	}
}	

function isPseudo(valeur)
{
	var reg = new RegExp("^[a-zA-Z0-9._-]{3,15}$");
	if(valeur.match(reg))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function isFolderName(valeur)
{
	var reg = new RegExp("^[a-zA-Z0-9 ._-]{1,128}$");
	if(valeur.match(reg))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function isFileName(valeur)
{
	var reg = new RegExp("^[a-zA-Z0-9 ._-]{1,128}$");
	if(valeur.match(reg))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function setWrong(id)
{
	document.getElementById(id).className = 'control-group error';
}

function setRight(id)
{
	document.getElementById(id).className = 'control-group success';
}
