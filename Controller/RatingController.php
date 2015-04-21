<?php

App::uses('AppController', 'Controller');

class RatingController extends AppController {
	
	var $name = 'Rating';
	var $uses = array('Rating');	
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add');
    }
	
	public function index(){
		
	}
	
	public function add(){
		$this->response->header(array(
			'Content-type: application/json'
		));
		
		if($this->request->is('post') || $this->request->is('put')){
			$responce = 'fail';
			if(!empty($this->request->data['Rating'])){
				if(empty($this->authUser)){
					throw new ForbiddenException('not authorized');
					$responce = 'error:not_authorized';
					echo json_encode($responce);
					exit;
				}elseif(empty($this->request->data['Rating']['user_id'])){
					$this->request->data['Rating']['user_id'] = $this->authUser['id'];
				}
				
				$check = $this->Rating->find('first', array(
					'conditions' => array(
						'content_type' => $this->request->data['Rating']['content_type'],
						'content_id' => $this->request->data['Rating']['content_id'],
						'user_id' => $this->request->data['Rating']['user_id']
					),
					'fields' => array('id')
				));
				
				if(!empty($check) && $this->authUser['role'] != 'admin'){
					$this->request->data['Rating']['id'] = $check['Rating']['id'];
				}
				
				if( $this->Rating->save($this->request->data) ){
					//$responce = $this->Rating->id;
					$responce = $this->Rating->getContentRating($this->request->data['Rating']['content_type'], $this->request->data['Rating']['content_id'] );
					$this->response->statusCode(200);
				}
				
			}else{
				$this->response->statusCode(400);
			}
						
		}
		echo json_encode($responce);
		exit;
	}
	
	public function remove($email){
		
		
		
	}
	
}