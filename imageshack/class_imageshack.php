<?
/********************************/
/*	ImageShack Process Class	*/
/* 	Version: 2.0				*/
/*	Author: chiplove.9xpro		*/	
/*	Ym: chiplove.9xpro			*/
/*	Site: www.yurivn.net		*/
/********************************/

class ImageShack{
	public function ImageShack(){

	}
	public function Login($user, $pass){
		$post = 'username=' . $user . '&password=' . $pass . '&format=json';
		$login = String::getURL('http://imageshack.us/auth.php', $post, '', '', '',1);
		preg_match_all('#Set-Cookie: (.*?)=(.*?);#i', $login, $m);
		for($i = 0; $i < count($m); $i++)
			$cookie[trim($m[1][$i])] = $m[2][$i];
		if(!$cookie['myimages']) 
			return false;
		else 
			return $cookie;
	}
	public function Upload($data, $filename, $cookie){		
		$boundary = 'yurivn.net';
		$post .= "--$boundary\r\n";
		$post .= "Content-disposition: form-data; name=\"fileupload\"; filename=\"".basename($filename)."\"\r\n";
		$post .= "Content-Transfer-Encoding: binary\r\n\r\n";
		$post .= "$data\r\n";
		$post .= "--$boundary\r\n";
		$str  = String::getURL('http://www.imageshack.us/', $post, '', $cookie, '',1 ,0 ,$boundary);
		$link = trim(String::getStr($str, 'location: ', "\n"));
		$link = str_replace('content_round.php?page=done&l=', '', $link);
		if(!$link) 
			return false;
		else 
			return $link;
	}
	public function Transfer($url, $cookie){
		$post = 'url='.$url;
		$str  = String::getURL('http://post.imageshack.us/transload.php', $post, '', $cookie, '',1 ,0);
		$link = trim(String::getStr($str, 'location: ', "\n"));
		$link = str_replace('content_round.php?page=done&l=', '', $link);
		if(!$link) 
			return false;
		else 
			return $link;
	}
}
class MyImageShack extends ImageShack{
	var $cookie;
	var $login;
	var $watermark;
	var $watermark_file;
	var $folder;
	var $sitename;
	public function MyImageShack(){
	
	}
	public function Upload($file){
		if(!$this->watermark){
			$data	  = file_get_contents($file['tmp_name']);
			$filename = $this->sitename . $file['name'];
			return parent::Upload($data, $filename, $this->cookie);
		}
		else{
			$filename = $file['name'];
			move_uploaded_file($file['tmp_name'], $this->folder . $this->sitename . $filename);		
			$newfile = $this->folder . $this->sitename . $filename;	
			$this->Watermark($newfile, $this->watermark_file);
			$data	 = file_get_contents($newfile);
			return parent::Upload($data, $this->sitename . $filename, $this->cookie);
		}		
	}
	public function Transfer($url){
		if(!$this->watermark){
			return parent::Transfer($url, $this->cookie);
		}
		else{
			//Leech image to host
			$url 		= trim($url);
			$data 		= fopen($url, "rb");
			$filename 	= $this->sitename . end(explode('/',$url));
			$newfile 	= fopen($this->folder . $filename, "w");
			flush();
			while ($buff = fread($data, 1024*8))
				fwrite($newfile, $buff);
			fclose($data);
			fclose($newfile);			
			//Leech complete
			$this->Watermark($this->folder . $filename, $this->watermark_file);
			$data	  = file_get_contents($this->folder . $filename);
			return parent::Upload($data, $this->sitename . $filename, $this->cookie);
		}	
	}
	public function Watermark($image, $logopath){
		$logo_id = imagecreatefrompng($logopath);
		imagealphablending($logo_id, false);
		imagesavealpha($logo_id, true);
		$info = getimagesize($image);
		$fileType = strtolower(substr($info['mime'], -3));
		if($fileType == 'gif')
			return false;
		switch($fileType){
			case 'png': 
				$image_id = imagecreatefrompng($image);	
				break;
			default:	
				$image_id = imagecreatefromjpeg($image);	
				break;
		}
		$image_w = imagesx($image_id);
		$image_h = imagesy($image_id);
		$logo_w	 = imagesx($logo_id);
		$logo_h	 = imagesy($logo_id);
		/* Watermark in the bottom right of image*/
		$dest_x  = ($image_w - $logo_w); 
		$dest_y  = ($image_h  - $logo_h);
		
		/* Watermark in the middle of image */
		/*
		$dest_x = ( $image_height / 2 ) - ( $logo_h / 2 )
		$dest_y = ( $image_w / 2 ) - ( $logo_w / 2 );
		*/
		imagecopy($image_id, $logo_id, $dest_x, $dest_y, 0, 0, $logo_w, $logo_h);
		switch($fileType){
			case('png'): 
				imagepng ($image_id, $image); //override to image
				break;
			default:	
				imagejpeg ($image_id, $image); 
				break;
		}       		 
		imagedestroy($image_id);
		imagedestroy($logo_id);
		return true;
	}
}
/* End of class */

class String{
	function getURL($url, $post='', $ref='', $cookie='', $headers='', $header=0, $nobody=0, $upload=0){
		$x = explode('/', $url);	$domain = $x[2];
		for($i = 3; $i <= count($x)-1; $i++)	$path .= '/'.$x[$i];
		$fp = @fsockopen($domain, 80, $errno, $errstr, 15); 
		if(!$fp)die("$errstr ($errno)");
		$method = $post ? "POST " : "GET ";
		$http  = $method . $path . " HTTP/1.1\r\n";
		$http .= "Host: " . $domain . "\r\n";
		$http .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
		$content_type =  "application/x-www-form-urlencoded";
		if($upload) $content_type = "multipart/form-data, boundary=" . $upload;
		$http .= "Content-Type: " .$content_type . "\r\n";
		if($cookie)	$http .= "Cookie: " . $cookie . "\r\n";
		if($ref)	$http .= "Referer: " . $ref . "\r\n";
		$http .= "Content-length: " . strlen($post) . "\r\n". (($upload) ? "\r\n" : "");
		if($headers) foreach($headers as $v) $http .= $v."\r\n";
		$http .= (!$upload) ? "Connection: close\r\n\r\n" : "";
		$http .= $post . "\n\r\n\r";
		fwrite($fp, $http);
		$i=0;
		while (!feof($fp)){
			$str .= fgets($fp, 128);
			if(!$header && $i<1 && strpos($str, "\n\r\n")){ $i++;unset($str);}
			if($nobody && strpos($str, "\n\r\n")) break;
		}
		fclose($fp);
		return $str;
	}
	function getStr($source, $start, $end){
		if(!$start){
			$str = explode($end, $source);
			return $str[0];
		}else{
			$str = explode($start, $source);
			if($end){		
				$str = explode($end, $str[1]);
				return $str[0];
			}else
				return $str[1];
		}
	}
}

?>