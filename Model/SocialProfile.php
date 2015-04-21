<?php

class SocialProfile extends AppModel {
	
	public function hasProfile( $network, $user_id ){
		$user = $this->find('first', array(
			'conditions' => array(
				'network' => $network,
				'user_id' => $user_id
			),
			'fields' => 'id'
		));
		return !empty($user) ? $user[$this->alias]['id'] : null;
	}
	
}