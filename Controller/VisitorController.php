<?php

class VisitorController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }
	
	public function index(){
		
	}
	
}