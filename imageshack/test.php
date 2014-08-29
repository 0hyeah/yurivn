<? session_start();
define('DIR', dirname(__FILE__));
include (DIR . '/inc/functions.php');
include (DIR . '/inc/class_http.php');
include (DIR . '/inc/class_imageshack.php');

$user = 'xxxx';
$pass = 'xxx';


?>
<form action="" method="post" enctype="multipart/form-data">
File: <input name="file" type="file" /><br />
Url: <input name="url" type="text" /><br />
<input name="do" value="Upload" type="submit" />
</form>
<?
if($_POST['do']){

	$imageshack = new MyImageShack;
	$imageshack->login = false;
	$imageshack->sitename = 'chiplove.biz';
	$imageshack->tempfolder = DIR . '/temp/'; //create and CHMOD this folder to 777
	$imageshack->watermark =  true; // auto watermark true/false
	$imageshack->watermark_file = DIR . '/logo.png'; //logo watermark
	
	if($imageshack->login){
		if(!$_SESSION['c_login']){
			$login = $imageshack->Login($user, $pass);	
			if(!$login) die('Login not success');
			$_SESSION['c_login'] = $imageshack->cookie;
		}
		$imageshack->cookie = $_SESSION['c_login'];
	}
	
	if($_FILES){
		$link = $imageshack->Upload($_FILES['file']);
	}
	else{
		$link = $imageshack->Transfer($_POST['url']);
	}
	echo $link;
	
}
?>