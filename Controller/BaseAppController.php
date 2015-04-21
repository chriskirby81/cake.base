<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class BaseAppController extends Controller {
	
	var $helpers = array('Session', 
		'Form'  => array('className' => 'CustomForm'), 
		'Html'  => array('className' => 'CustomHtml'),
		'Utils'
	);
	
	var $device = 'desktop';
	
	var $uses = array('User', 'Page');
	var $sidebars = array();
	var $page = array();
	var $debug = false;
	
	var $components = array(
		//'DebugKit.Toolbar',
        'Session',
        'Auth' => array( 'className' => 'Authorization' )
    );
	
	var $pageControllers = array(
		
	);
	
	var $pageActions = array(
		
	);
		
	public $hasSidebar = true;
	public $isAdmin = false;
	
	//Gloabl Vars
	public $authUser = null;
	
	public function beforeFilter() {
		
		//$this->Auth->allow();
		$this->Auth->init();
		
		$this->set('device', $this->device);
		
		$this->authUser = $this->Auth->user();
		if($this->authUser['role'] == 'admin'){
			$this->isAdmin = true;
		}
		$this->set('isAdmin', $this->isAdmin);
		$this->set('authUser', $this->authUser);
		$this->set('debug', $this->debug);

		$this->set('page_id', $this->params['controller'].'-'.$this->params['action']);
		
		if(!empty($this->params['controller']) && 
			in_array($this->params['controller'], $this->pageControllers ) &&
			in_array($this->params['action'], $this->pageActions )
		){
			$this->Page = ClassRegistry::init('Page');
			$this->Page->init(null, $this->params['controller'], $this->params['action']);
		}
		
		if(!empty($this->authUser)){
			$this->set('admin', $this->authUser);
		}else{
			$this->set('admin', null);	
		}
		
		parent::beforeFilter();
		
	}
	
	
	public function afterFilter() {
				
	}
	
	public function meta( $key, $value ) {
		if(!isset($this->page['meta'])) $this->page['meta'] = array();
		$this->page['meta'][$key] = $value;
	}
	
	public function sidebar( $sidebar = null, $params = null ) {
		$this->sidebars[] = empty($params) ? $sidebar : array( 'sidebar' => $sidebar, 'options' => $params );
		
	}
	
	public function _flash( $messages = null ){
		if(!empty($messages)){
			
			if(is_array($messages)){
				foreach($messages as $message){
					 $this->Session->setFlash(__($message));
				}
			}else{
				 $this->Session->setFlash(__($messages));
			}
		}
	}


	public function flash( $message = null, $url = array(), $pause = 1, $layout = 'flash' ){
		if(!empty($messages)){
			
			if(is_array($messages)){
				foreach($messages as $message){
					 $this->Session->setFlash(__($message));
				}
			}else{
				 $this->Session->setFlash(__($messages));
			}
		}
	}
	
	public function _email( $options = array() ){
		
		
		if(!empty($options)){
			
			$Email = new CakeEmail('default');
			if(isset($options['template']))
				$Email->template($options['template']);
			if(isset($options['format']))
				$Email->emailFormat($options['format']);	
			if(isset($options['vars']))
				$Email->viewVars($options['vars']);
			if(isset($options['from']))
				$Email->from($options['from']);
			if(isset($options['to']))
				$Email->to($options['to']);
			if(isset($options['subject']))
				$Email->subject($options['subject']);
				
			$Email->send();
		
		}
		
	}
	
	public function error( $params = null ) {
		if(!empty($params)){
			if(is_numeric($params)){
				$this->response->statusCode($params);
				exit;
			}	
		}
	}
	
	public function beforeRender() {
		
		$metadata = $this->Page->getPageMeta($this->viewVars);
		$this->set('metadata', $metadata);	
		
		if(isset( $this->page['meta'])){
			$this->set('meta', $this->page['meta'] );	
		}
		
    	$this->set('sidebars', $this->sidebars );
		
        return parent::beforeRender();
    }
	
	
}
