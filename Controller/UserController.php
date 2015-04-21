<?php 

App::uses('CakeEmail', 'Network/Email');

class UserController extends AppController {
	
	var $name = 'User';

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'login', 'authorized');
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
		
		
		
        if ($this->request->is('post')){
			
            $this->User->create();
			//print_r($this->request->data);
			$this->request->data['User']['token'] = Security::hash(String::uuid());
            if ($this->User->save($this->request->data['User'])) 
			{
				
                $this->Session->setFlash(__('The user has been saved'));
				
				//$Email = new CakeEmail();
				//$Email->template('welcome')->emailFormat('both')->to($this->User->email())->from(array('admin@'.DOMAIN => 'Admin'));
				
				
				if ($this->Auth->login()) 
				{
					$this->redirect($this->Auth->redirect());
					
				}else
				{
					$this->Session->setFlash(__('There was a problem logging you in, please try to login again if the problem continues please contact us.'));
				}
            } 
			else 
			{
				debug($this->User->validationErrors);
				
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
		$this->set('title_for_layout', 'Create User Account');
    }
	
	public function authorized($type = 'json') {
		
		
		$responce = null;
		if(!empty($this->authUser)){
			$responce = $this->authUser;
		}else{
			throw new ForbiddenException('not authorized');
			exit;
		}
		switch($type){
			case 'json':
			$this->response->header(array(
				'Content-type: application/json'
			));
			echo json_encode($responce);
			break;
		}
		exit;
	}
	
	public function details($user_id = NULL) {
		
	 	$owner = false;
		
		if(!empty($this->authUser))
		{
			if(empty($user_id)) $user_id = $this->authUser['id'];
			if($user_id == $this->authUser['id']) $owner = true;
		}
		
		if(empty($user_id)){
			//Empty	
		}
		
		$user = $this->User->find('first', array(
			'contain' => array(
				'UserDetail'
			),
			'conditions' => array(
				'User.id' => $user_id
			)
		));
		
		if(!empty($user))
		{
		$this->data = $user;
		$this->set('owner', $owner);
		}
				
	}

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        $this->redirect(array('action' => 'index'));
    }
	
	public function login( $view = null ) {
		if(!empty($view)){
			$this->layout = 	$view;
		}
		if($this->Auth->user()){
			$this->redirect($this->Auth->redirect());
		}
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				if(isset($this->request->data['User']['redirect'])){
					$this->redirect($this->request->data['User']['redirect']);
				}else{
				$this->redirect($this->Auth->redirect());
				}
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'));
			}
		}
		$this->set('title_for_layout', 'User Login');
	}

	public function logout(){
		
		$this->redirect($this->Auth->logout());
	}
	
}

?>