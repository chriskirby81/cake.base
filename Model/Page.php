<?php

class Page extends AppModel {
	
	/*
	CREATE TABLE IF NOT EXISTS `pages` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `controller` char(32) NOT NULL,
	  `action` char(32) NOT NULL,
	  `url` varchar(564) NOT NULL,
	  `created` datetime NOT NULL,
	  `deleted` datetime DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
	
	*/
	
	public $name = 'Page';	
	
	public $action = null;
	public $controller = null;
	public $page = array();
		
	public function url(){
		$this->url = Router::url();
		return  $this->url;
	}
	
	public function init($url = null, $controller = null, $action = null){
		
		if(empty($url)){
			$url = $this->url();	
		}
		
		if(!empty($controller)){
			$this->controller = $controller;
		}
		
		if(!empty($action)){
			$this->action = $action;
		}
		
		if(strpos($url, '/var/www/') !== false){
			$this->page = null;
			return false;
		}
	
		$page = $this->find('first', array(
			'conditions' => array(
				'Page.url' => $url
			)
		));
		
		if(!empty($page)){
			$this->viewed($page['Page']['id']);
			// Found Page
			$this->page = $page;
		}else{
			// Cant Find Page
			$this->createPage();	
		}
		
		
	}
	
	function viewed($page_id = null){
		$this->updateAll(
			array('views' => "Page.views + 1"),                    
			array('id' => $page_id )
		);
	}
	
	function createPage($url = null){
		
		if(empty($url)){
			$url = $this->url();	
		}
		
		$site_id = (defined('DOMAIN') ? DOMAIN : $_SERVER['HTTP_HOST'] );
		
		$this->data['Page'] = array(
			'site_id' => $site_id,
			'url' => $url,
			'controller' => $this->controller,
			'action' => $this->action
		);
		
		if($this->save($this->data)):
			return $this->id;
		else:
			return null;
		endif;
		
	}
	
	function getPageMeta($view = null){
		
	
		$meta = new stdClass;
		$meta->id = isset($view['page_id']) ? $view['page_id'] : '';
		$meta->url = isset($view['current_page']) ? $view['current_page'] : '';
		$meta->key = '';
		$meta->title = isset($view['title_for_layout']) ? $view['title_for_layout'] : '';
		$meta->description = isset($view['meta_description']) ? $view['meta_description'] : '';
		$meta->keywords = isset($view['meta_keywrods']) ? $view['meta_keywrods'] : '';
	
	
		return $meta;
	}
	
}
