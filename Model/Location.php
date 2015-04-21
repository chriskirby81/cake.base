<?php
class Location extends AppModel{
	
	public $useDbConfig = 'data';
	public $name = 'Location';
    
    
    public $validate = array(
    
    );
	
	public function stateList($country = 'US'){
		
		$states = Cache::read('StateSelect.'.$country, 'long');
		
		if(!$states){
			$states = $this->find('list', array(
				'conditions' => array(
					'country' => $country
				),
				'group' => array(
					'state_code'
				),
				'fields' => array(
					'state_code', 'state'
				)
			));
			Cache::write('StateSelect.'.$country, $states, 'long');
		}
		
		return $states;
	}
    
}