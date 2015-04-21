<?php

App::uses('Component', 'Controller/Component');


class ScraperComponent extends Component {
	
	public $authorized;
	private $ch = null;
	protected $errors = array();
	public $cookie_dir = '';
	public $domain = null;
	public $currentPage = null;
	public $responce = null;
	
	public $fields = array(
		'username' => 'username',
		'password' => 'password'
	);
		
	public function init( ) {
		
    }
	
	public function filterTag( $tag = null){
		
		$this->html = preg_replace('/<'.$tag.'\b[^<]*(?:(?!<\/'.$tag.'>)<[^<]*)*<\/'.$tag.'>/i', '', $this->html);
	}
	
	public function absoluteRefs(){
		$this->html = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);
		$this->html = preg_replace("#(<\s*img\s+[^>]*src\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);
		$this->html = preg_replace("#(<\s*link\s+[^>]*href\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);
		$this->html = preg_replace("#(<\s*form\s+[^>]*action\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);
		$this->html = preg_replace("#(<\s*input\s+[^>]*src\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);

		
		return $this->html;
	}
	
	public function filter_responce(){
		$this->html = preg_replace("#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);
		$this->html = preg_replace("#(<\s*img\s+[^>]*src\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);
		$this->html = preg_replace("#(<\s*link\s+[^>]*href\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);
		$this->html = preg_replace("#(<\s*form\s+[^>]*action\s*=\s*[\"'])(?!http)([^\"'>]+)([\"'>]+)#", '$1'.$this->base.'$2$3', $this->html);
	//	$this->html = str_replace( 'src="','src="'.$base.'/', $this->html);
		//Remove all Scripts
		$this->html = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/i', '', $this->html);
		$this->html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $this->html);
		return $this->html;
	}

    public function login( $loginUrl = null, $username = null, $password = null, $post_fields = array()){
		//pr($loginUrl);
		//pr($username);
		//pr($password);
		$username_fld = isset($this->fields['username']) ? $this->fields['username'] : 'username';
		$password_fld = isset($this->fields['password']) ? $this->fields['password'] : 'password';
		
		if(!empty($username) && is_array($username)){
			$usern = $username;
			$username = reset($usern);
			$username_fld = key($usern);
		}
		
		if(!empty($password) && is_array($password)){
			$passn = $password;
			$password = reset($passn);
			$password_fld = key($passn);
		}
		$this->cookie_dir = TMP.DS.'curl_cookies';
		if(!is_dir($this->cookie_dir)){
			mkdir($this->cookie_dir, 0777 );
		}
		$parsed = parse_url( $loginUrl );
		$this->domain = $parsed['host'];
		$this->scheme = $parsed['scheme'];
		$this->base = $this->scheme.'://'.$this->domain;

		//$params = $username_fld.'='.$username.'&'.$password_fld.'='.$password;
		
		$params = array();
		$params[$username_fld] = $username;
		$params[$password_fld] = $password;
		
		$params = array_merge($params, $post_fields);
		//pr($params);
		$post_fields = http_build_query($params);
		//pr($post_fields);
		//exit;
		//init curl
		$ch = curl_init();
		//Set the URL to work with
		curl_setopt($ch, CURLOPT_URL, $loginUrl);
		// ENABLE HTTP POST
		curl_setopt($ch, CURLOPT_POST, true);
		
	//pr($params);
	//exit;
	
		//Set the post parameters
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields );
		//Handle cookies for the login
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_dir.DS.$this->domain.'.txt');  //could be empty, but cause problems on some hosts
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->cookie_dir.DS.$this->domain.'.txt');
		curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i586; de; rv:5.0) Gecko/20100101 Firefox/5.0');            
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
		//not to print out the results of its query.
		//Instead, it will return the results as a string return value
		//from curl_exec() instead of the usual true/false.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		//curl_setopt($ch,CURLOPT_HEADER , true);
		curl_setopt($ch, CURLOPT_REFERER, $this->base );
		
		//execute the request (the login)
		$this->responce = curl_exec($ch);
		$this->url = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
		$info = curl_getinfo($ch);
		$err = curl_error($ch);
		
		curl_close($ch);
		//pr($err);
		//pr($info);
		//pr($this->responce);
		//exit;
		$this->html = $this->responce;
		
		return true;
		
	}
	
	public function scrape( $url = null ){
		return $this->request( $url );
	}
	
	public function request( $url = null, $params = array(), $method = 'GET' ){
		
		$this->cookie_dir = TMP.DS.'curl_cookies';
		if(!is_dir($this->cookie_dir)){
			mkdir($this->cookie_dir, 0777 );
		}
		
		$parsed = parse_url( $url );
		$this->domain = $parsed['host'];
		$this->scheme = $parsed['scheme'];
		$this->base = $this->scheme.'://'.$this->domain;
		//init curl
		$ch = curl_init();
		//Set the URL to work with
		curl_setopt( $ch, CURLOPT_URL, $url );
		// ENABLE HTTP POST
		if( $method == 'POST' ){
			curl_setopt( $ch, CURLOPT_POST, 1 );
			$query_str = http_build_query( $params );
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query_str );
		}
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_dir.DS.$this->domain.'.txt');  //could be empty, but cause problems on some hosts
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->cookie_dir.DS.$this->domain.'.txt');
		curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i586; de; rv:5.0) Gecko/20100101 Firefox/5.0');            
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		//curl_setopt($ch,CURLOPT_HEADER , true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_REFERER, $this->base );
		
        $status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$this->responce = curl_exec( $ch );
		
		$this->currentPage = '';
		$this->url = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
		$this->html = $this->responce;
				
		return true;
	}
	
}