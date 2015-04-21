<?php
App::uses('Component', 'Controller');

class DomComponent extends Component {
	
	var $errors = array();
	
	public function load( $file = null ){
		
		$content = file_get_contents($file);
		$doc = new DOMDocument();
    	$doc->load($file);
	
		pr($doc);
		
	}
	
}

	
