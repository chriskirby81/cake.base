<?php
App::uses('AppModel', 'Model');

class Subscribe extends AppModel{
	
	public $name = 'Subscribe';
	public $useTable = 'subscribes';
	
	public $validate = array(
		'email' => array(
            'required' => array(
                'rule' => array('notEmpty'),
				'required' => true,
                'message' => 'A email is required'
            ),
			'between' => array(
				'rule' => array('between', 6, 129)
			)
        )
    );
	
}

