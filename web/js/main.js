function removeElement(parent, child)
{
	child = document.getElementById(child);
	parent = document.getElementById(parent);
	
	parent.removeChild(child);
}
	
