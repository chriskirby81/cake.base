<?php
App::uses('Component', 'Controller');


class SocialComponent extends Component {
	
	var $components = array('OAuth');
	
	
	private $auth_token;
	private $server_base;
	private $login_redirect;
	
	public $connected;
	public $token;
	public $token_secret;
	public $expires;
	public $scope;
	
	public function init($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL){
		$this->OAuth->endpoints = $this->endpoint;
		$this->OAuth->base($this->endpoint['base']);
		$this->OAuth->init($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret );
	}
	
	public function getAuthUrl($oauth_callback = null){
		$this->login_redirect = !empty($oauth_callback) ? $oauth_callback : FULL_BASE_URL.'/social/auth/'.$this->network.'/response';
		$token = $this->OAuth->getRequestToken( $this->endpoint['base'].$this->endpoint['request_token'], $this->login_redirect);
		switch ($this->OAuth->http_code) {
			case 200:
				/* Build authorize URL and redirect user to Twitter. */
				$url = $this->OAuth->getAuthorizeURL($token);
				return $url;
			break;
			default:
				/* Show notification if something went wrong. */
				echo 'Could not connect to Network. Refresh the page or try again later.';
				return null;
		}
		return null;
	}
	
	public function authReturn(){
		return $this->OAuth->getAccessToken($_GET['oauth_verifier']);
	}

	
	public function error( $error, $code = null ){		
		$this->errors[] = !empty($code) ? array( $error, $code ) : $error;
	}
	
}

