<?php
App::uses('SocialComponent', 'Controller/Component');


class TwitterComponent extends SocialComponent {
	

	public $endpoint = array(
		'base' => 'https://api.twitter.com/',
		'request_token' => 'oauth/request_token',
		'authorize' => 'oauth/authorize',
		'authenticate' => 'oauth/authenticate',
		'access_token' => 'oauth/access_token'
	);
	
	
	public $session;
	
	public $profile;
	public $network = 'twitter';
	
	
	protected $errors = array();
	
	
	public function init( $consumer_key = NULL, $consumer_secret = NULL, $oauth_token = NULL, $oauth_token_secret = NULL ){

	   if(empty($consumer_key)) $consumer_key = TWITTER_APP_ID;
	   if(empty($consumer_secret)) $consumer_secret = TWITTER_API_KEY;
	   
	   if(isset($_GET['oauth_token'])){
		   $oauth_token = $_GET['oauth_token'];
	   }
	   if(isset($_GET['oauth_verifier'])){
		   $oauth_token_secret = $_GET['oauth_verifier'];
	   }
	   parent::init($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret );
	   
	}
	
	public function authReturn(){
		$token = parent::authReturn();
		if(isset($token['oauth_token'])){
			$this->connected = true;
			$this->token = $token['oauth_token'];
			$this->token_secret = $token['oauth_token_secret'];
			$this->scope = '';
			$this->expires = null;
			$user_id = $token['user_id'];
			return $user_id;
		}
		return null;
	}

}