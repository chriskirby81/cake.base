<?php
App::uses('AppHelper', 'View/Helper');

class UtilsHelper extends AppHelper {
	
	
	public $counters = array();
	public $lists = array();
	
	 function counter($id, $val = null){
	 	if(!empty($val)){
	 		$this->counters[$id] = $val;
			return $val;
	 	}
		if(!isset( $this->counters[$id] )){
			$this->counters[$id] = 0;
		}else{
			$this->counters[$id]++;
		}
		return $this->counters[$id];
	}
	
	
}

?>