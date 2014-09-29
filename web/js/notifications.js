function addNotif(lien, image, message, date)
{
	var dropdown = document.getElementById('dropdown-notif');
	var notif = document.createElement('li');
	notif.innerHTML = '<a href="'+lien+'"><img src="/download/'+image+'/min" style="width:30px" />&nbsp;&nbsp;'+message+'</a>';
	dropdown.appendChild(notif);
}

function addDivider()
{
	var dropdown = document.getElementById('dropdown-notif');
	var divider = document.createElement('li');
	divider.className = 'divider';
	dropdown.appendChild(divider);
}

function display(notifications)
{
	var i;
	var nbr_notifs = document.getElementById('nbr_notifs');
	
	if(notifications.length > 0 )
	{
		nbr_notifs.innerHTML = '('+notifications.length+')';
		for(i = 0; i < notifications.length; i++)
			{
				notif = notifications[i];
				addNotif(notif.link, notif.thumbnail, notif.message, notif.date);
				if(notifications.length > 1 && i != notifications.length -1)
					addDivider();
			}
	}
}

function getNotifications()
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
		      			  if(re.notifications.length <= 0)
		      			    {
		      			      no_notif.style.display="block"
		      			    }
		      			   else
		      			    {
		      			      no_notif.style.display = 'none';
		      			    }
		      				display(re.notifications);
		          		}
			    }
			 }
    }; 

   xhr.open( "GET", "/ajax/notifications/get",  true); 
   xhr.send(null); 
}

function seen()
{
	var xhr = new XMLHttpRequest();
 
    xhr.onreadystatechange  = function() 
    { 
       if(xhr.readyState  == 4)
		     {
		     	if(xhr.status  == 200) 
		      	{
		      		var re = check(xhr.responseText, true, false);
		        }
			 }
    }; 

   xhr.open( "GET", "/ajax/notifications/seen",  true); 
   xhr.send(null); 
}

function Init()
{
	setTimeout(getNotifications, 1000);
	setInterval(getNotifications, 60000);
	
	var dropdown = document.getElementById('notifs_dd');
	dropdown.addEventListener('click', seen);
}

Init();