<?php
class UserDetail extends AppModel{

	public $name = 'UserDetail';
    public $useTable = 'user_details';
    
    public $validate = array(
    
    );
	
	public function beforeSave($options = array()) {
		
		if (isset($this->data[$this->alias]['birthdate'])){
			$this->data[$this->alias]['birthdate'] = date('Y-m-d', strtotime($this->data[$this->alias]['birthdate']));
		}
	
		return true;
		
	}
    
}