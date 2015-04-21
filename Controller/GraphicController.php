<?php

App::uses('AppController', 'Controller');

class GraphicController extends AppController {
	
	var $name = 'File';
	public $components = array('Graphic');
	var $uses = array('File');
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('image');
    }
	
	public function image(){
		
		if(isset($this->request['url']['path'])){
			$path = urldecode($this->request['url']['path']);
		}else{
			//Path Not Present
			return false;
		}
		$options = $this->request['url'];
		unset($options['path']);

		$path_opts = implode('&', array_map(function ($v, $k){ 
			return $k . '=' . $v; 
		}, $options, array_keys($options)));
		
		
		
		$this->Graphic->init( $path, $path_opts, $options );
		$this->Graphic->apply();
		$this->Graphic->display( true );
		
		exit;
		
	}
	
	
	
}