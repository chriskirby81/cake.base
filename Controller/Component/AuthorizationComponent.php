<?
App::uses('AuthComponent', 'Controller/Component');

class AuthorizationComponent extends AuthComponent {
	
	public $components = array('Session', 'Cookie', 'RequestHandler');
	
    // Add your code to override the core AuthComponent
	
	public $loginRedirect = array(
		'controller' => 'home'
	);
	
	public $logoutRedirect = array(
		'controller' => 'home'
	);
	
	public $loginAction = array(
		'controller' => 'user',
		'action' => 'login',
		'plugin' => null
	);
	
	public $authenticate = array(
		AuthComponent::ALL => array('userModel' => 'User'),
		'Form' => array(
			'fields' => array('username' => 'email')
		)
	);
	
	public $uses = array('User');
	
	public function beforeFilter() {
        parent::beforeFilter();
		
    }
	
	public function init() 
	{
				
		$user = parent::user();
				
		if(empty($user)){
			
			if($this->Cookie->check('Auth.remember')){
				
				$cookie = $this->Cookie->read('Auth.remember');
				
				$this->authenticate = array(
					'Form' => array(
						'fields' => array('username' => 'id')
					)
				);
				
				//Try to login Cookie User
				if(parent::login($cookie)){
					
					$this->authUser = $user;	
				}
				
			}
		}
	}
	
	public function login($user = null) 
	{
	
		//Allow Username or Password Field
		if(!empty($this->request->data['User']['email_or_username'])) 
		{
			if (strpos($this->request->data['User']['email_or_username'], '@') !== false) 
			{
				$this->request->data['User']['email'] = $this->request->data['User']['email_or_username'];
			}
			else
			{
				$this->request->data['User']['username'] = $this->request->data['User']['email_or_username'];
				$this->authenticate = array(
					AuthComponent::ALL => array('userModel' => 'User'),
					'Form' => array(
						'fields' => array('username' => 'username')
					)
				);
				
			}
			
			unset($this->request->data['User']['email_or_username']);
		}
				
        $login = parent::login($user);
		
		if($login){
			//Is now logged in 
			if(!empty($this->request->data['Auth']['remember']))
			{
				//Remember Me Checked
				if(!$this->Cookie->check('Auth.remember'))
				{
					$remember_key = array(
						'id' => $this->user('id'),
						'password' => $this->user('password')
					);
					
					if($this->Cookie->write('Auth.remember', $remember_key, true, 3600*30 ))
					{
						$this->flash('You will now stay logged in until you logout.');	
					}
					else
					{
						$this->flash('An error occoured while trying to remember you. You will logout at the end of your session');
					}
				}
			}
		}
		
		return $login;
		
    }
	
	public function logout() {
		
		//If has Remember Cookie
		if($this->Cookie->check('Auth.remember')){
			//Delete Remember Cookie
			$this->Cookie->delete('Auth.remember');
		}
		
		return parent::logout();
		
	}
	
}
