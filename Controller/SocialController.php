<?php

App::uses('AppController', 'Controller');

class SocialController extends AppController {
	
	var $name = 'Social';
	
	var $components = array('Session', 'Facebook', 'Twitter');
	var $uses = array('SocialProfile');
	
    public function beforeFilter() {
		
		parent::beforeFilter();
        $this->Auth->allow();
    }
	
	public function auth( $network = null, $state = null ){
		
		if(empty($network)){
			echo "No Network Set";
			exit;
		}
		
		$social_profile = array();
		$social_profile['site_id'] = SITE_ID;
		$social_profile['network'] = $network;
		$exists = false;
		
		if(!empty($this->authUser)){
			$social_profile['user_id'] = $this->authUser['id'];
			$exists = $this->SocialProfile->hasProfile( $network, $this->authUser['id'] );
			if($exists){
				$social_profile['id'] = $this->authUser['id'];
			}
		}
		
		$scope = '';
		if($this->request->is('post') || $this->request->is('put')){
			$scope = $this->request->data['scope'];
		}
	
		$networkName = ucwords($network);
		$currentNetwork = $this->$networkName;
		
		$currentNetwork->init();
		
		if(empty($state)){
			$auth_url = $currentNetwork->getAuthUrl($scope);
			$this->redirect($auth_url);
		}elseif( $state == 'response' ){
			//Network Auth Responce
			$account_id = $currentNetwork->authReturn();
			if($account_id){
				$this->Session->write('Social.'.$network, $account_id );
				$this->Session->write('Social.'.$network.'.token', $currentNetwork->token );
				$social_profile['profile_id'] = $account_id;
				$social_profile['token'] = $currentNetwork->token;
				$social_profile['token_secret'] = $currentNetwork->token_secret;
				$social_profile['expires'] = $currentNetwork->expires;
				$social_profile['scope'] = $currentNetwork->scope;
				$this->SocialProfile->save($social_profile);
			}else{
				print_r($currentNetwork->errors)	;
			}
			
			echo $account_id;
		}elseif( $state == 'admin' ){
			$auth_url = $currentNetwork->getAuthUrl('manage_pages,publish_actions');
			$this->redirect($auth_url);
		}
		
		exit;
	}
	
	public function page( $page_id = null ){
		
	}

	public function post( $network = null ){
		
		$networkName = ucwords($network);
		$currentNetwork = $this->$networkName;
		
		if($this->Session->read('Social.'.$network )){
			$token = $this->Session->read('Social.'.$network );
			$currentNetwork->getSession($token);
			$currentNetwork->post();
		}
		exit;

	}

	
	public function profile( $network = null ){
		
		$networkName = ucwords($network);
		$currentNetwork = $this->$networkName;
			
		if($this->Session->read('Social.'.$network )){
			$token = $this->Session->read('Social.'.$network );
			$currentNetwork->getSession($token);
			$currentNetwork->getProfile();
		}
		exit;
	}
}