function vietvbbSetCookie(c_name,value,expiredays)	{
	var exdate=new Date()
	exdate.setDate(exdate.getDate()+expiredays)
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

function vietvbbGetCookie(c_name)	{
	if (document.cookie.length>0)  {
		c_start=document.cookie.indexOf(c_name + "=")
		if (c_start!=-1)    { 
			c_start=c_start + c_name.length+1 
			c_end=document.cookie.indexOf(";",c_start)
			if (c_end==-1) c_end=document.cookie.length
			return unescape(document.cookie.substring(c_start,c_end))
		} 
	}
	return false;
}

function addLoadEventV(func)	{
	var oldload = window.onload;
	if (typeof window.onload != 'function')	{
		window.onload = func;
	}
	else	{
		window.onload = function()	{
			oldload();
			func();
			}
	}
}	
function addEventV() {
	var ul = document.getElementById('vietvbb_topstats_t');
	var li = ul.getElementsByTagName('li');
	for (i=0; i < li.length-1; i++)	{
		li[i].onmouseover = function()	{
			viewTabV(this);
			}
	}	
	var select = document.getElementById('vietvbb_topstats_s');
	select.onchange= function()	{
		viewMenuV(this.value);
		}	
	
	var topxtab = vietvbbGetCookie('topxtab');
	if(topxtab)		{
		var ul = document.getElementById('vietvbb_topstats_t');
		var li = ul.getElementsByTagName('li');
		for (i=0; i < li.length-1; i++)
			li[i].className='';
		document.getElementById(topxtab).className='current';
	}
	var topxmenu = vietvbbGetCookie('topxmenu');
	if(topxmenu)	{
		select.value = topxmenu;
	}	
	topXReload();
}
function viewTabV(a) {
	var id = a.id;
	var ul = document.getElementById('vietvbb_topstats_t');
	var li = ul.getElementsByTagName('li');
	for (i=0; i < li.length-1; i++)	{
		li[i].className='';
	}
	vietvbbSetCookie('topxtab',id);	
	a.className='current';	
	
	document.getElementById('vietvbb_topstats_t_loading').style.display = 'inline';
	//document.getElementById('vietvbb_topstats_t_content').innerHTML = "";
	top_requestT = new vB_AJAX_Handler(true);
	top_requestT.onreadystatechange(handleResponsesT);
	url = 'ajax.php?do=vietvbb_stats&type=tab&top=' + id;
	top_requestT.send(url);
}

function viewMenuV(a) {
	document.getElementById('vietvbb_topstats_s_loading').style.display = 'inline';
	//document.getElementById('vietvbb_topstats_s_content').innerHTML = "";
	vietvbbSetCookie('topxmenu',a);
	top_requestS = new vB_AJAX_Handler(true);
	top_requestS.onreadystatechange(handleResponsesS);
	url = 'ajax.php?do=vietvbb_stats&top=' + a;
	top_requestS.send(url);
}
function topXReload()	{
	var ul = document.getElementById('vietvbb_topstats_t');
	var li = ul.getElementsByTagName('li');
	for (i=0; i < li.length-1; i++)	{
		if (li[i].className == 'current')	{
			//document.getElementById('vietvbb_topstats_t_content').innerHTML = "";
			viewTabV(li[i]);
		}
	}	
	var select = document.getElementById('vietvbb_topstats_s');		
	viewMenuV(select.value);
}

function handleResponsesT() 
{
	if (top_requestT.handler.readyState == 4 && top_requestT.handler.status == 200)
	{
		document.getElementById('vietvbb_topstats_t_loading').style.display = 'none';				
		document.getElementById('vietvbb_topstats_t_content').innerHTML = top_requestT.handler.responseText;
    }
}
function handleResponsesS() 
{
	if (top_requestS.handler.readyState == 4 && top_requestS.handler.status == 200)
	{
		document.getElementById('vietvbb_topstats_s_loading').style.display = 'none';				
		document.getElementById('vietvbb_topstats_s_content').innerHTML = top_requestS.handler.responseText;
    }
}
addLoadEventV(addEventV);