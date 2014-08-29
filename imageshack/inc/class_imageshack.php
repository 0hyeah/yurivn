<?
/********************************/
/*	ImageShack Uploader Class	*/
/* 	Version: 2.5				*/
/*	Author: chiplove.9xpro		*/	
/*	Ym: chiplove.9xpro			*/
/*	Site: www.chiplove.biz		*/
/********************************/

class ImageShack{ 
	var $http;
	var $cookie;
	function ImageShack(){ 
		$this->http =& new Http();
	}
	function Login($user, $pass){
		$this->http->clear();	
		$this->http->setHttpType('fsockopen');
		$this->http->setUrl('http://imageshack.us/auth.php');
		$this->http->setPost( array('username' =>  $user, 'password' => $pass, 'format' => 'json') );
		$this->http->execute();
		preg_match_all('#Set-Cookie: ((myimages|isUSER|myid).*?);#i', $this->http->response, $m);
		if(!in_array('myimages', $m[2])){
			return false;
		}else{
			$this->cookie = join(';', $m[1]);
			return true;
		}
	}
	function Upload($filePath){		
		$this->http->clear();	
		$this->http->setUrl('http://www.imageshack.us/');
		$this->http->setHttpType('fsockopen');
		$this->http->setSubmitMultipart(true);
		if($this->cookie){
			$this->http->setCookie($this->cookie);
		}
		$this->http->setPost( array('fileupload' => '@' . $filePath, 'where' => 'iframe') );
		$this->http->execute();
		$link = str_replace('content_round.php?page=done&l=', '', $this->http->resLocation); 
		return $link;
	}
	function Transfer($url){
		$this->http->clear();	
		$this->http->setHttpType('fsockopen');
		$this->http->setUrl('http://post.imageshack.us/transload.php');
		if($this->cookie){
			$this->http->setCookie($this->cookie);
		}
		$this->http->setPost( array('url' => $url) );
		$this->http->execute();
		$link = str_replace('content_round.php?page=done&l=', '', $this->http->resLocation); 
		return $link;
	}
}
class MyImageShack extends ImageShack{
	var $login;
	var $watermark;
	var $watermark_file;
	var $tempfolder;
	var $sitename;
	function MyImageShack(){
		parent::ImageShack();
	}
	function Upload($file){
		$filePath = $this->tempfolder . $this->sitename . $file['name'];
		move_uploaded_file($file['tmp_name'], $filePath);		
		if($this->watermark){
			$this->Watermark($filePath, $this->watermark_file);
		}
		return parent::Upload($filePath);	
	}
	function Transfer($url){
		$url = trim($url);
		if(!$this->watermark){
			return parent::Transfer($url);
		}
		else{
			//Leech image to host
			$filePath = $this->tempfolder . $this->sitename . basename($url);
			$data 		= fopen($url, "rb");
			$newfile 	= fopen($filePath, "w");
			flush();
			while ($buff = fread($data, 1024*8))
				fwrite($newfile, $buff);
			fclose($data);
			fclose($newfile);			
			//Leech complete
			$this->Watermark($filePath, $this->watermark_file);
			return parent::Upload($filePath);
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


?>