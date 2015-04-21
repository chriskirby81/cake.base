<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel{
	
	public $name = 'User';
	
	
	public $validate = array(
        'username' => array(
			'required' => array(
				'rule' => array('notEmpty')
			),
			'unique' => array(
				'rule' => 'isUnique'
			),
			'between' => array(
				'rule' => array('between', 5, 30)
			),
			'alphaNumeric' => array(
                'rule'     => 'alphaNumeric',
                'message'  => 'Alphabets and numbers only'
            )
		),
		'name' => array(
			'between' => array(
				'rule' => array('between', 3, 30)
			)
		),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            ),
			'between' => array(
				'rule' => array('between', 5, 30)
			)
        ),
		'email' => array(
            'required' => array(
                'rule' => array('notEmpty'),
				//'required' => true,
                'message' => 'A email is required'
            ),
			'between' => array(
				'rule' => array('between', 6, 129)
			)
        ),
		'email_or_username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A Email or Username is required'
            ),
			'between' => array(
				'rule' => array('between', 3, 129)
			)
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('user','admin')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        )
    );

	
	public $hasOne = array(
		'UserDetail' => array(
			'dependent'    => true
		),
		'UserAddress' => array(
			'dependent'    => true
		)
	);
	
	public $components = array('Cookie');

	public function invite( $data = null, $referrer = null ) {
		if(!empty($data) && isset($data['User']['name']) && isset($data['User']['email'])){
			if(!empty($referrer)) $data['User']['referrer'] = $referrer;
			$data['User']['token'] = Security::hash(String::uuid());
			if($this->save($data)){
				$data['User']['id'] = $this->id;
				return $data['User'];
			}else{
				return false;
			}	
		}
	}
	
	
	public function beforeSave($options = array()) {
		
		if (isset($this->data[$this->alias]['password'])){
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
	
		if (isset($this->data[$this->alias]['email_or_username'])){
			if (strpos($this->data[$this->alias]['email_or_username'], '@') !== false) {
				$this->data[$this->alias]['email'] = $this->data[$this->alias]['email_or_username'];
			}else{
				$this->data[$this->alias]['username'] = $this->data[$this->alias]['email_or_username'];
			}
			unset($this->data[$this->alias]['email_or_username']);
		}
		
		return true;
		
	}
	
	public function afterFind($results, $primary = false){
		
		
		foreach ($results as $key => $val) {
		
			if(isset($val[$this->alias]['password']))
			{
				//unset($results[$key][$this->alias]['password']);
			}
			
		}
		return $results;
	}
}

