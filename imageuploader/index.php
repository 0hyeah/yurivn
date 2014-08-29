<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Google Picasa Image Uploader</title>
<link rel="stylesheet" href="style.css" type="text/css" />
<script  type="text/ecmascript" src="jquery.js?v=3.1"></script>
<script  type="text/ecmascript" src="script.js?v=3.1"></script>
</head>

<body>
<div class="wrapper">
	<div id="header">
    	<h1>Google Picasa Image Uploader</h1>
        <div class="description">[Y][U][R][I][V][N]</div>
    </div>
    <div class="body">
        <div class="option" style="margin-top: 20px;">
            <div class="rows" >
            	<span>Resize</span>
                 <select id="resize">
                    <option value="0">Không Resize</option>
                    <option value="1">150px...</option>
                    <option value="2">320px...</option>
                    <option value="3">640px...</option>
                    <option value="4">800px...</option>
                    <option value="5">1024px...</option>
                    <option value="6">1600px...</option>
                </select>
                <span class="note">(Resize theo chiều rộng của hình. Hình chỉ thu nhỏ, không phóng to.)</span>
            </div>

            <div class="rows method uploadfile" style="margin-top: 15px;">
                 <div class="upload">
                 	<span>Nhấn Browser để chọn file upload</span>
                 	<div id="embed"></div>
                 </div>
            </div>
            
            <div class="rows method transload">
            	<span>Nhập link ảnh vào để transload</span> 
                <div><textarea class="links"></textarea></div>
                <span class="note">(Mỗi link ảnh 1  dòng, có hỗ trợ link ảnh trong thẻ [IMG])</span>
            	<div><input type="button" class="button" value="Transload" /></div>
            </div>
           
        </div><!--/.option-->
    </div>
</div><!--/#wrapper-->
<div class="wrapper">
	<div class="body">
    	<div id="result"></div>
        <div id="status"></div>
        <div id="list" style="display:none">
        	<div class="format">
            	<a href="javascript:;" name="direct">Direct Link</a>
                <a href="javascript:;" name="bbcode">BBcoded Link</a>
            </div>
        	<div><textarea class="links" onclick="this.select()"></textarea></div>
        </div>
    </div>
	
</div>

</body>
</html>
