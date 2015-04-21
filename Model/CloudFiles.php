<?php
require_once(APP . 'Vendor' . DS.'php-cloudfiles'.DS.'cloudfiles.php');

class CloudFiles extends AppModel {
	
	public $useTable = false;
	
	private $username = RACKSPACE_USERNAME;
	private $apikey = RACKSPACE_APIKEY;
	private $connection = null;
	private $container = null;
	public $connected = false;
	public $errors = array();
	
	public function error($message = null)
	{
		$this->errors[] = $message;
	}
	
	public function connect(){
		
		//Include and Connect to Rackspace Cloudfiles
		if(!$this->connected){
			try 
			{
				$auth = new CF_Authentication($this->username, $this->apikey);
				$auth->authenticate();
				$this->connection = new CF_Connection($auth, true);
				$this->connected = true;
				
			}catch (Exception $e) 
			{
				$this->error('authentication|connection');
			}
		}
	}
	
	public function createContainer($container, $public = true){
		
		$this->connect();
		//Create Container
		try {
			$this->container = $this->connection->create_container($container);
			if($public) $this->container->make_public();  
			return true; 
		} catch (Exception $e) {
			$this->error('Could not create container ' . $container);
			return false;
		}
	}
	
	public function getContainer($container, $public = true){
		$this->connect();
		try {
			//GET Container
			$this->container = $this->connection->get_container($container);
			return true; 
		} catch (Exception $e) {
			//If no container create one.
    		return $this->createContainer($container, $public) ? true : false;
		}
		
	}
	
	public function put($file, $cloud_path, $container){
		
		$this->connect();
		
		if($this->getContainer($container)){
			$object = $this->container->create_object($cloud_path);
			return $object->load_from_filename($file) ? true : false;
		}
		return false;
		
	}
	
	public function get($cloud_file, $container){
		$this->connect();
		if($this->getContainer($container)){
			$object = $this->container->get_object($cloud_file);
			$data = $object->read();
			return $object;
		}
		return false;
		
	}
	
	public function purge(){
		//$object->purge_from_cdn	
	}
	
	
	public function fileExists($cloud_file){
		try {
		 	$this->container->get_object($cloud_file);
		 	return true;
		} catch (Exception $e) {
        	return false;
    	}
	}
	
}