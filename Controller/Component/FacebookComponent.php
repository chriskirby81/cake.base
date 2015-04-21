<?php
//session_start();
App::uses('SocialComponent', 'Controller/Component');

//App::import('Vendor', 'Facebook', array('file' => 'Facebook/autoload.php' ));
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

class FacebookComponent extends SocialComponent {
	
	
	private $auth_token;
	private $server_base;
	private $login_redirect;
	private $servers = array();
	
	public $connected;
	public $session;
	public $token;
	public $profile;
	public $network = 'facebook';
	public $expires;
	public $scope;
	
	protected $errors = array();
	
	public function init( $consumer_key = NULL, $consumer_secret = NULL, $oauth_token = NULL, $oauth_token_secret = NULL ) {
		if(empty($consumer_key)) $consumer_key = FACEBOOK_APP_ID;
	    if(empty($consumer_secret)) $consumer_secret = FACEBOOK_API_KEY;
		$this->login_redirect = FULL_BASE_URL.'/social/auth/facebook/response';
		require('/var/www/cake.base/Vendor/Facebook/autoload.php');
		FacebookSession::setDefaultApplication($consumer_key, $consumer_secret);
    }

    public function getAuthUrl($scope = ''){
		$helper = new FacebookRedirectLoginHelper($this->login_redirect);
		$url = $helper->getLoginUrl();
		return $url. $scope;
	}
	
	public function authReturn(){
		$helper = new FacebookRedirectLoginHelper($this->login_redirect);
		try {
			$this->session = $helper->getSessionFromRedirect();
		} catch(FacebookRequestException $ex) {
			// When Facebook returns an error
			$this->error($ex->getMessage(), $ex->getCode());
			return false;
		} catch(\Exception $ex) {
			// When validation fails or other local issues
			$this->error($ex->getMessage(), $ex->getCode());
			return false;
		}
		if (!empty($this->session)) {
			// Logged in
			$this->connected = true;
			$info = $this->session->getSessionInfo();
			$this->token = $this->session->getToken();
			$this->scope = join(",", $info->getScopes());
			$expires = $info->getExpiresAt();
			$expires->setTimezone(new DateTimeZone('America/New_York'));
			$this->expires = $expires->format('Y-m-d H:i:s');
			$this->profile = $this->getProfile();
			$user_id = $this->profile->getId();
			return $user_id;
		}
		
	}
	
	public function getSession( $access_token ){
		$this->session = new FacebookSession($access_token);
	}
	
	public function openGraph( $method, $path = '', $vars = null, $objclass = null ){
		if($this->session) {
			try {
				$response = (new FacebookRequest(
				  $this->session, $method, $path, $vars
				))->execute()->getGraphObject($objclass);
				return $response;
			}catch(FacebookRequestException $e) {
				$this->error($ex->getMessage(), $ex->getCode());
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
				return null;
		  	}   
		}
	}
	
	public function getAccounts(){
		$response =  $this->openGraph('GET', '/me/accounts');
		return $response;
	}
	
	
	public function getProfile(){
		return $this->openGraph('GET', '/me', null, GraphUser::className());
	}
		
	public function post($link = null, $message = null){
		$response = $this->openGraph('POST', '/me/feed', array(
			'link' => $link,
			'message' => $message
		));
				
		echo "Posted with id: " . $response->getProperty('id');
		return $response;
		
	}
	
	public function error( $error, $code = null ){		
		$this->errors[] = !empty($code) ? array( $error, $code ) : $error;
	}


}