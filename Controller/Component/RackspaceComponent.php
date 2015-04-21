<?php

App::uses('Component', 'Controller');

class RackspaceComponent extends Component {
	
	private $auth_token;
	private $server_base;
	private $curl_response;
	
	private $servers = array();
	
	protected $errors = array();
	
	public function init() {

		$this->setAuthToken();
		
    }

        
	private function setAuthToken()
	{
		$data = array(
			'credentials'  => array(
				'username' => 'ckirby1981',
				'key'      => 'b25424c98361927890a171938ce8fd1b',
			)
		);
		$response = $this->doRequest('auth','post', $data);
		if(!$this->hasErrors()){
			$this->auth_token  = $response->auth->token->id;
			$this->server_base = str_replace("\n",'',$response->auth->serviceCatalog->cloudServers[0]->publicURL);
		} else {
			die($this->getErrors());
		}
		
	}
        
        private function doRequest($path, $method = 'get', $data = null)
        {
            $headers = array(
                'Cache-Control: no-cache, must-revalidate',
                'Expires: Mon, 28 May 2012 08:08:08 GMT'
            );
            
            if(stripos($path, 'auth') !== false){
                $base = 'https://auth.api.rackspacecloud.com/v1.1';
            }
            
            if(stripos($path, 'servers') !== false || stripos($path, 'images') !== false || stripos($path, 'limits') !== false){
                $base      = $this->server_base;
                $headers[] = 'X-Auth-Token: ' . $this->auth_token;
                if(strcasecmp($method, 'delete') != 0){
                    $path .= '.json?rtime=' . time();
                }
            }
            
            
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $base . '/' . $path);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            
            if(strcasecmp($method, 'post') == 0){
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            
            if(strcasecmp($method, 'delete') == 0){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            }
            
            if(!empty($headers)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            
            $ret = curl_exec($ch);

            if(curl_errno($ch)){
                $this->setError(curl_error($ch));
                return false;
            } else {
                $this->curl_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                return json_decode($ret);
            }
        }

        public function getServers()
        {
            $response = $this->doRequest('servers');
            
            if(!$this->hasErrors()){
				$servers = $response->servers;
                return $response->servers;
            } else {
                return false;
            }
        }
        
        public function getServerDetail($id)
        {
            $response = $this->doRequest('servers/' . $id);
            
            if(!$this->hasErrors()){
                return $response;
            } else {
                return false;
            }
        }
        
        public function createServer($name, $image, $flavor = 4)
        {
            $data = array(
                'server' => array(
                    'name'     => $name,
                    'imageId'  => $image,
                    'flavorId' => $flavor
                )
            );
            $response = $this->doRequest('servers', 'post', $data);
            
            if(!$this->hasErrors()){
                if($this->curl_response > 299){
                    $response = 'HTTP Status: ' . $this->curl_response . ': ' . $response;
                    $this->setError($response);
                    return false;
                }
                return $response;
            } else {
                return false;
            }
        }
        
        public function createImageFromServer($server_id, $name)
        {
            $data = array(
                'image' => array(
                    'name'     => $name,
                    'serverId' => $server_id
                )
            );
            
            $response = $this->doRequest('images', 'post', $data);
            
            if(!$this->hasErrors()){
                if($this->curl_response > 299){
                    $response = 'HTTP Status: ' . $this->curl_response . ': ' . print_r($response, 1);
                    $this->setError($response);
                    return false;
                }
                return $response;
            } else {
                return false;
            }
        }
        
        public function deleteServer($id)
        {
            $response = $this->doRequest('servers/' . $id, 'delete');
            if($this->curl_response > 299){
                $this->setError('Received response: ' . $this->curl_response);
            }
            if(!$this->hasErrors()){
                return true;
            } else {
                return false;
            }
        }
        
        public function getImages()
        {
            $response = $this->doRequest('images/detail');
            
            if(!$this->hasErrors()){
                return $response;
            } else {
                return false;
            }
        }
        
        public function getLimits()
        {
            $response = $this->doRequest('limits');
            
            if(!$this->hasErrors()){
                return $response;
            } else {
                return false;
            }
        }
        
        public function setError($message)
        {
            $this->errors[] = $message;
        }
        
        public function getErrors()
        {
            return implode("\n", $this->errors);
        }
        
        public function hasErrors()
        {
            return !empty($this->errors);
        }
   
}