function showpics(num,name){	
  displaypic('upload.php?gp=1&name='+name+'&num='+num, 'result',name);
}
iload = true;
var iloadnow = 0;
function iloading(){
	if(iload){
		iloadnow ++;
		if(iloadnow > 3)
			iloadnow = 0;
		//var icon = new Array('\\','-','/','|');
		var icon = new Array('.','..','...','....');
		document.getElementById('iload').innerHTML = icon[iloadnow];
		setTimeout('iloading();',160);
	}
}

function upload(){				
	document.getElementById('player_mc').uploadpics();	
}
function loading(){
  iload = true;
  document.getElementById('loading').innerHTML = 'Loading..<span id="iload"></span>';
  iloading();
}
function responseStatus(msg){
	document.getElementById('loading').innerHTML = msg;
	if(msg == 'Done!'){
		iload = false;	
		document.getElementById('getcode').style.display = 'block';
		setTimeout("showcode('bbcode');",3000);
	}
	
}
function clearlist(){
   document.getElementById('result').innerHTML = "";
}
function showcode(type){
	var code = new Array();
	if(type == 'html'){		
		code[0] = '&lt;img src="';
		code[1]	= '"&gt;';
	}else if(type == 'bbcode'){
		code[0] = '[IMG]';
		code[1]	= '[/IMG]';
	}	
	else{
		code[0] = '';
		code[1] = '';
	}
	content = document.getElementById('result').innerHTML;
	ex = content.split('</div>');
	var html = '';
	for(i in ex){
			link = ex[i].replace('<div>','');
			if(ex[i].match(/-/)){
				link =	link.split('- ');
				link = link[1]}
			html += code[0] + link + code[1] + "\n";	
	}

	html = html.replace(code[0]+'undefined'+code[1],'');
	html = html.replace(code[0]+code[1],'');
	document.getElementById('showcode').innerHTML = html;
}
transfer_id = 0
function transfer(wtm){	
	var watermark = wtm == 'yes' ? 'yes': 'no';
	iload = true;
	loading();
	listUrl = document.getElementById('listurl').value;
	ex = listUrl.split("\n");
	url = ex[transfer_id].replace('[IMG]','').replace('[/IMG]','');
	transferUrl(url,'result',transfer_id,ex.length,watermark);	
	
}
function transferUrl(url,id,num,count,watermark){
	var xmlHttp = GetXmlHttpObject();
	xmlHttp.onreadystatechange=function(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		if(xmlHttp.responseText) exist = 1;
			if(id){
				iload = false;
				var up = document.getElementById(id);
				var dv = document.createElement("div");
				dv.innerHTML = (parseInt(num)+1) +' - '+ xmlHttp.responseText;
				transfer_id ++ ;
				if(transfer_id < count)
					transfer();
				up.appendChild(dv);
				if(num==count-1)
					responseStatus("Done!");
			}
		}
	}
	url = 'url='+encodeURI(url)+'&sid='+ Math.random();
	xmlHttp.open('POST', 'upload.php?watermark='+watermark, true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", url.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(url);
}
function displaypic(url, id,name){
	var xmlHttp = GetXmlHttpObject();	
	xmlHttp.onreadystatechange=function(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		if(xmlHttp.responseText) exist = 1;
		if(id){	
			var up = document.getElementById(id);
			var dv = document.createElement("div");
			dv.innerHTML = name+' - '+xmlHttp.responseText;
			up.appendChild(dv);
		}
	}
	}
	url = decodeURIComponent(url);
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}


function GetXmlHttpObject(){
  var xmlHttp=null;
  try
  {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
  }
  catch (e)
  {
  // Internet Explorer
    try
      {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
    catch (e)
        {
        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return;
	}
return xmlHttp;
}