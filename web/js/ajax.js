function check(r, pop, ret)
{
	var json_obj = JSON.parse(r);
	if(json_obj.json_error)
		{
			if(pop == true)
				alert(json_obj.json_error);
			if(ret == false)
				return false;
			else
				return json_obj.json_error;
		}
	else
		{
			return json_obj;
		}
}
