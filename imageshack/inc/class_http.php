<?

/**
 * Product: 	_Http class use curl, fsockopen
 * Version:		1.0
 * Update:		1/12/2010
 * Author:		chiplove.9xpro
 * Website:		http://chiplove.biz
*/


/**
 * This class fake a browser, you can using it for read web content or upload file to server
 * It using two functions are: curl and fsockopen
 
 Http 1.0 support for:
 + Cookie
 + Refer
 + Proxy (only httptype == curl)
 + Server authentication
 

 Http Example:
 
 	Read web content:
		$http = new Http();
		$http->setUrl("http://www.yourwebsite.com/");
		$http->execute();
		echo $http->response;
		echo $http->resCookieString;
		print_r($ob->resHeaders);
		print_r($ob->getArrayResHeaders());
		
 	Submit form:
 		$http = new Http();
		$http->setUrl("http://www.yourwebsite.com/");
		$http->setPost(array("fieldname"=> $value));
		// or $http->setPost("fieldname=$value");
		$http->execute();
		echo $http->response;
		
	Using Proxy:
		$http = new Http();
		$http->setUrl("http://www.yourwebsite.com/");
		$http->setProxy('proxy_ip:proxy_port');
		$http->execute();
		echo $http->response;
	
	Upload file:
		$filePath = getcwd().'/he.jpg';
		$http = new Http();
		$http->setUrl("http://www.yourwebsite.com/");
		$http->setSubmitMultipart(true);
		$http->setPost(array('fileupload'=>"@$filePath",'where'=> 'iframe'));
		$http->showHeader = true;
		$http->execute();
		echo $http->response;
		

*/

class Http{
	
	var $httptype;
	/* For upload*/
	var $enctype;
	var $boundary;
	
	/* Set option */
	var $url;
	var $browser;
	var $cookie;
	var $refer;
	var $showHeader;
	var $headers;
	var $nobody;
	var $post;
	var $timeout;
	
	/* Use proxy */
	var $proxy;
	var $proxy_user;
	var $proxy_password;
	
	/* For Server Authentication */
	var $AuthUsername;
	var $AuthPassword;
	
	/* Response */
	var $response;
	var $resHeaders;
	var $resHeadersArray;
	var $resCookieString;
	var $errors;
	var $resLocation;
	
	
	function __construct(){
		$this->clear();
	}
	
	function clear(){
		$this->httptype			= "curl";
		/* special variables */
		$this->enctype			= "application/x-www-form-urlencoded";
		$this->boundary			= "chiplove.9xpro";	
		
		$this->url 				= "";
		$this->browser			= "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12";
		$this->refer 			= "";
		$this->timeout 			= 10;	
		$this->cookie			= "";
		$this->showHeader		= true;	// 	show header response
		$this->nobody			= false; // show content
		$this->headers			= array();
		$this->post				= ""; 
		
		$this->proxy			= "";
		$this->proxy_user		= "";
		$this->proxy_password	= "";
		
		$this->AuthUsername		= "";
		$this->AuthPassword		= "";
		
		$this->response 		= "";
		$this->resHeaders		= array();
		$this->resCookieString	= "";
		$this->resLocation		= "";
		$this->errors			= "";
		

	}
	
	function setUrl($url){
		$this->url = $url;
	}
	
	function setRefer($refer){
		$this->refer = $refer;
	}
	
	function setHttpType($type){
		$this->httptype = $type;
	}
	
	function setAuth($username, $password){
		$this->AuthUsername = $username;
		$this->AuthPassword = $password;
	}
	
	function setSubmitMultipart($multipart = false){
		if($multipart){
			$this->enctype = "multipart/form-data";
		}else{
			$this->enctype = "application/x-www-form-urlencoded";
		}
	}
	function setCookie($cookie){
		if(is_string($cookie)){
			$this->cookie = $cookie;
		}
		elseif(is_array($cookie)){
			$temp = array();
			foreach($cookie as $key => $value){
				$temp[] = $key . '=' . $value;
			}
			$this->cookie = join(';', $temp);
		}
	}
	
	function setProxy($proxy, $username = "", $password = ""){
		$this->proxy = $proxy;
		if($username && $password){
			$this->proxy_user     = $username;
			$this->proxy_password = $password;
		}
	}
	
	function setPost($post){
		if(is_string($post)){
			$this->post = $post;
		}
		elseif(is_array($post)){
			if($this->enctype == "multipart/form-data"){
				if($this->httptype == "curl"){
					$this->post = $post;
				}
				elseif($this->httptype == "fsockopen"){
					foreach($post as $key => $value){
						if(substr($value, 0, 1) == '@'){
							$upload_file_path 	= substr($value, 1);
							$upload_field_name  = $key;
						}else{
							$more_fields[$key] = $value;
						}
					}
					$this->setPostData($upload_field_name, $upload_file_path, $more_fields);
				}
			}
			else{
				$temp = array();
				foreach($post as $key => $value){
					$temp[] = $key . '=' . $value;
				}
				$this->post = join('&', $temp);
			}
		}
	}

	function setPostData($upload_field_name, $upload_file_path, $more_fields = array()){	
	
		if(!empty($more_fields) && is_array($more_fields)){
			foreach($more_fields as $key => $value){
				$this->post  .= "--" . $this->boundary . "\r\n";
				$this->post  .= "Content-Disposition: form-data; name=\"" . $key . "\"\r\n";
				$this->post  .= "\r\n";
				$this->post  .= $value . "\r\n";
			}
		}
		if(file_exists($upload_file_path)){
			$handle = fopen($upload_file_path, "rb");
			while ($buff = fread($handle, 1024*8)){
				$binarydata .= $buff;
			}
			fclose($handle);
				
			$this->post .= "--" . $this->boundary . "\r\n";
			$this->post .= "Content-disposition: form-data; name=\"" . $upload_field_name . "\";";
			$this->post .= "filename=\"" . basename($upload_file_path) . "\"\r\n";
			$this->post .= "Content-Transfer-Encoding: binary\r\n\r\n";
			$this->post .= $binarydata . "\r\n";
			$this->post .= "--" . $this->boundary . "\r\n";
		}				
		if($this->httptype == 'curl'){
			$this->headers[] = "Content-type: " . $this->enctype . "; boundary=" . $this->boundary;
		}
	
	}
	function execute(){
		if($this->httptype == "curl"){
			$this->_curl_request();
		}
		elseif($this->httptype == "fsockopen"){
			$this->_fsockopen_request();
		}
		else{
			die('Error: Please set property "httptype" to "curl" or "fsockopen"');
		}
		$this->parseHeaders();	
		if($this->errors){
			echo $this->errors;
		}
	}
	
	function _fsockopen_request(){	
		if($this->proxy){
			die('Error: httptype "fsockopen" not support for property: "proxy". 
				Change them to "false" or "0" please.');
		}
		preg_match('#(https?)?://([a-z.]{1,})/?(.*)#i', $this->url, $m);
		list($this->url, $protocol, $domain, $path) = $m;
		#$port = ($protocol == 'https') ? 443 : 80;
		$this->timeout = ($this->timeout <= 0) ? 10 : $this->timeout;
		$fp = @fsockopen($domain, 80, $errno, $errstr, $this->timeout); 
		if(!$fp)	die("$errstr ($errno)");
		$method = $this->post ? "POST" : "GET";
		$http  = $method ." /" . $path . " HTTP/1.1\r\n";
		$http .= "Host: " . $domain . "\r\n";
		$http .= "User-Agent: " . $this->browser . "\r\n";
		$http .= "Content-Type: " . $this->enctype;
		$http .= (($this->enctype == "multipart/form-data") ? "; boundary=" . $this->boundary : "");
		$http .= "\r\n";
		if($this->AuthUsername && $this->AuthPassword)
			$http .= "Authorization: Basic " . base64_encode($this->AuthUsername . ":" . $this->AuthPassword) . "\r\n";
		if($this->cookie)	
			$http .= "Cookie: " . $this->cookie . "\r\n";
		if($this->refer)	
			$http .= "Referer: " . $this->refer . "\r\n";
		if($this->post)	
			$http .= "Content-length: " . strlen($this->post) . "\r\n";
		if($this->headers){
			foreach($this->headers as $line)
				$http .= $line . "\r\n";
		}
		$http .= "Connection: close\r\n\r\n";
		$http .= $this->post . "\r\n\r\n";
		fwrite($fp, $http);
		$i = 0;
		while (!feof($fp)){
			$this->response .= fgets($fp, 128);
			if(!$this->showHeader && $i < 1 && strpos($this->response, "\r\n\r\n")){
					unset($this->response);
					$i++;
			}
			if($this->nobody && strpos($this->response, "\r\n\r\n")){ 
				break;
			}
		}
		fclose($fp);
	}
	
	function _curl_request(){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, 					$this->url);
		if($this->refer)
			curl_setopt($ch, CURLOPT_REFERER, 			$this->refer);
		if($this->cookie)	
			curl_setopt($ch, CURLOPT_COOKIE, 			$this->cookie);
		if($this->headers)
			curl_setopt($ch, CURLOPT_HTTPHEADER, 		$this->headers);
		if($this->timeout)
			curl_setopt($ch, CURLOPT_TIMEOUT, 			$this->timeout); 	
		if($this->post){
			curl_setopt($ch, CURLOPT_POST, 				true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 		$this->post);
		}	
		if ($this->AuthUsername && $this->AuthPassword){
			curl_setopt($ch, CURLOPT_HTTPAUTH, 			CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, 			$this->AuthUsername . ':' . $this->AuthPassword);
        }
		if($this->proxy){
			#curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 	true); 
			curl_setopt($ch, CURLOPT_PROXY, 			$this->proxy);	
			curl_setopt($ch, CURLOPT_PROXYTYPE, 		CURLPROXY_SOCKS5); 		
			if($this->proxy_user && $this->proxy_password){
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, 	$this->proxy_user . ':' . $this->proxy_password);
			}
		}
		curl_setopt($ch, CURLOPT_USERAGENT,				$this->browser);
		curl_setopt($ch, CURLOPT_HEADER,				$this->showHeader);	
		curl_setopt($ch, CURLOPT_NOBODY, 				$this->nobody);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 		true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 		false);
		curl_setopt($ch, CURLOPT_ENCODING, 				'gzip,deflate');
		
		$this->response = curl_exec($ch);
		if(empty($this->response)){
			$this->errors = curl_error($ch);
		}
		curl_close($ch);
	}
	
	function parseHeaders(){
		$this->resCookieString = "";
		if($this->showHeader){
			$header = $this->getStr($this->response, "", "\r\n\r\n");
			$lines = explode("\n", $header);
			foreach($lines as $line){
				$line = trim($line);
				if($line){
					$this->resHeaders[] = $line;
					//parse headers to array
					if(!$this->resHeadersArray){
						$this->resHeadersArray['status'] = $line;
					}else{
						list($key, $value) = explode(": ", $line);
						$key = strtolower($key);
						//parse location
						if($key == 'location'){
							$this->resLocation = $value;
						}
						//parse cookie
						if($key == 'set-cookie'){
							$this->resCookieString .= $value.';';
						}
						
						if(in_array($key, array_keys($this->resHeadersArray))){
							if(!is_array($this->resHeadersArray[$key])){
								$temp = $this->resHeadersArray[$key];
								unset($this->resHeadersArray[$key]);
								$this->resHeadersArray[$key][] = $temp;
								$this->resHeadersArray[$key][] = $value;
							}else{
								$this->resHeadersArray[$key][] = $value;
							}
						}else{
							$this->resHeadersArray[$key] = $value;
						}
					}
					//end
				}
			}
		}
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