<? 
/**
 * Product: 	ImageShack Uploader 
 * Version: 	2.5
 * Author: 		chiplove.9xpro
 * Website: 	http://chiplove.biz
*/

session_start();
define('DIR', dirname(__FILE__));
include (DIR . '/inc/functions.php');
include (DIR . '/inc/class_http.php');
include (DIR . '/inc/class_imageshack.php');
#ini_set('memory_limit', '32M');


$ob =& new MyImageShack;
$ob->login = false;
$ob->sitename = 'chiplove.biz';
$ob->tempfolder = DIR . '/temp/'; //create and CHMOD this folder to 777
$ob->watermark = ($_REQUEST['watermark'] == 'yes') ? true : false; // auto watermark true/false
$ob->watermark_file = DIR . '/logo.png'; //logo watermark

// User & pass login to imageshack.us 
$user = 'trangnt00914@fpt.edu.vn';
$pass = '674923714'; 
$login_info = 'info_login.txt';
$login_fail = 'login_fail.txt';

if($_FILES['Filedata']){
	if($ob->login){
		if(!$_SESSION['cookie']){
			if(file_exists($login_info)){
				$_SESSION['cookie'] = file_get_contents($login_info);
			}else{
				$login = $ob->Login($user, $pass);	
				if(!$login){
					write_file($login_fail, "Account: {$user}\nPass: {$pass}\nLogin fail!");
					exit;
				}else{
					write_file($login_info, $ob->cookie);
					if(file_exists($login_fail)) unlink($login_fail);
				}
			}
		}	
		$ob->cookie = $_SESSION['cookie'];
	}
	$file = $_FILES['Filedata']; 
	$link = $ob->Upload($file);
	write_file($ob->tempfolder . $file['name'] . '.txt', $link);

}
if($_GET['name']){
	$filename  = $_GET['name'];
	$file_txt  = $ob->tempfolder . $filename . '.txt';
	$file_data = $ob->tempfolder . $ob->sitename . $filename;
	if(file_exists($file_txt)){
		$link = file_get_contents($file_txt);	
		echo $link ? $link : 'Error!';	
		
		if(file_exists($file_txt))	unlink($file_txt);
		if(file_exists($file_data))	unlink($file_data);
	
	}
	elseif(file_exists($login_fail)){
		echo file_get_contents($login_fail);
	}
	else{
		/*
		if you see this error, please remove # front of ini_set('memory_limit', '32M'); in this file
		*/
		echo "Upload to imageshack fail. please remove \"#\" front of ini_set('memory_limit', '32M'); in this file";
	}
		
}
if($_POST['url']){
	echo $ob->Transfer($_POST['url']);
}
?>