<? session_start();?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP Script - Imageshack Uploader 2.0 - chiplove.9xpro</title>
<script type="text/javascript" src="ajax.js"></script> 

</head>

<body>
<?
$op = $_GET['op'] ? $_GET['op'] : 'upload';
$watermark = $_GET['watermark'] ? $_GET['watermark'] : 'yes';
?>
<a href="?op=upload&watermark=<?=$watermark?>">Upload</a> | 
<a href="?op=transfer&watermark=<?=$watermark?>">Transfer</a> | 
<a href="?op=<?=$op?>&watermark=yes">Wartermark</a> | 
<a href="?op=<?=$op?>&watermark=no">No watermark</a> 
<? if(!$_GET['op'] || $_GET['op'] == 'upload'){?>
    <form method="post" enctype="multipart/form-data" action=""> 
    Select file upload:<br />
    <div id="inputfile" style="height:20px;width:250px;">
    	 <div style="float: left;">
            <input type="text" style="height:20px;border:1px solid #ccc;width:170px;">
        </div>
        <div style="float: left;">
            <embed type="application/x-shockwave-flash" src="upload.swf?watermark=<?=$watermark?>" id="player_mc" name="player_mc" quality="high" wmode="transparent" pluginurl="http://www.macromedia.com/go/getflashplayer" pluginspage="http://www.macromedia.com/go/getflashplayer" width="75" height="25">
        </div>
	</div>
    </form> 	
<? }else{?>
    <form method="post" enctype="multipart/form-data" action="?op=transfer"> 
    Enter Url<br />
    <div id="inputfile"><textarea id="listurl" name="listurl" rows="10" cols="70"></textarea></div>
    <input type="button" onclick="clearlist();transfer('<?=$watermark?>');" value="Transfer"/>
    </form> 	
<? }?>
<div style="clear:both"></div>
<div id="result"></div>
<div id="loading"></div>
<div id="getcode" style="display:none">
    <div><a onclick="showcode('bbcode');" href="#BBCode">BBCode</a> | <a onclick="showcode('html');"href="#HTML">HTML Code</a> | <a onclick="showcode('none');" href="#BBCode">Direct Link</a>
     </div>
    <div><textarea id="showcode" rows="10" cols="80" onclick="this.select();"></textarea></div>
</div>
</body>
</html>