<?php

App::uses('Component', 'Controller');

class DatabaseComponent extends Component {
	
	 public function beforeFilter() {
        parent::beforeFilter();
    }
	
	public function tryCreate($controller = null, $model = null){
		
		if ($controller->request->is('post') || $controller->request->is('put')) {
		
			if ($controller->$model->save($controller->request->data)) {
				$controller->Session->setFlash(__('The '.$model.' has been saved'));
				$controller->redirect(array('action' => 'index'));
			} else {
				$controller->Session->setFlash(__('The chart could not be saved. Please, try again.'));
			}
		
		}
	}
	
	public function tryEdit($controller = null, $model = null){
		
		if ($controller->request->is('post') || $controller->request->is('put')) {
		
			if ($controller->$model->save($controller->request->data)) {
				$controller->Session->setFlash(__('The '.$model.' has been saved'));
				$controller->redirect(array('action' => 'index'));
			} else {
				$controller->Session->setFlash(__('The chart could not be saved. Please, try again.'));
			}
		
		}
		
	}
   
}
