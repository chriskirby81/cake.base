<?php

App::uses('AppController', 'Controller');

class SubscribeController extends AppController {
	
	var $name = 'Subscribe';
	var $uses = array('Subscribe');	
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add');
    }
	
	public function index(){
		
	}
	
	public function add(){

		if($this->request->is('post') || $this->request->is('put')){
			
			
			if(!empty( $this->request->data['Subscribe']['email'] )){
				
				$check = $this->Subscribe->find('count', array(
					'conditions' => array(
						'Subscribe.email' => $this->request->data['Subscribe']['email']
					)
				));
				
				if($check > 0){
					$this->Session->setFlash('You are already subscribed to our newsletter.');
				}else{
				
					if($this->Subscribe->save($this->request->data)){
						$this->Session->setFlash('Thank you for subscribing to our newsletter.');
					}
				
				}
				$this->redirect($this->referer());
			}
		}
		
		exit;
	}
	
	public function remove($email){
		
		$subscription = $this->Subscribe->find('first', array(
			'conditions' => array(
				'Subscribe.email' => $email
			)
		));
		
		$this->Subscribe->delete($subscription['Subscribe']['id']);
		
		
	}
	
}