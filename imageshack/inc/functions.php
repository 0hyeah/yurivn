<?
function write_file($filePath, $content, $mode = 'w'){
	$fp = fopen($filePath, $mode);
	fwrite($fp, $content);
	fclose($fp);
}

?>