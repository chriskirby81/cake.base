<?php


class SideBarController extends AppController {
	
	public $name = 'SideBar';
	public $uses = array('SideBarItem');
	public $components = array();
	public $helpers = array();
	
	public function index(){
		
		$sidebar_items = $this->SideBarItem->find('all');
		$this->set('sidebar_items', $sidebar_items);
		//print_r($items);
		
	}
	
	
	public function add(){
		
		if($this->request->is('post')){
		
			if($this->SideBarItem->save($this->request->data)){
				
				$this->Session->setFlash('You have successfully created this SideBarItem');
				
				$this->redirect('/sidebar/view/'. $this->SideBarItem->id);
				
			}else{
				
				$this->Session->setFlash('Could not save SideBarItem please try again.');	
				
			}
		
		}else{
			
			
			
		}
		
		//print_r($items);
		
		$this->render('form');
		
	}
	
	public function edit($id = null){
		
		$item = $this->SideBarItem->find('first', array(
			'conditions' => array(
				'id' => $id 
			)
		));
		
		print_r($item);
		
	}
	
	
	public function view(){
		
		$items = $this->SideBarItem->find('all');
		
		//print_r($items);
		
	}
	
}