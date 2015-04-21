<?php
class UserAddress extends AppModel{

	public $name = 'UserAddress';
    public $useTable = 'user_address';
    
   	public $belongsTo = 'User';
	
	
    
}