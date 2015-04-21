<?php

App::uses('AppController', 'Controller');

class HomeController extends AppController {
	
	var $name = 'Home';
	
	var $components = array('Session');
	
	
    public function beforeFilter() {
        parent::beforeFilter();
       // $this->Auth->allow('*');
    }
	
	public function index(){
		$this->render('home');
	}
	
	public function user()
	{
		$this->render('home');
	}
	
	public function visitor()
	{
		$this->render('home');
	}
	
}