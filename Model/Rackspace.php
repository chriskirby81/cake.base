<?php


class Rackspace extends AppModel {
	
	public $useTable = false;
	
	private $auth_token;
	private $server_base;
	private $curl_response;
	private $api_username = RACKSPACE_USERNAME;
	private $api_key = RACKSPACE_APIKEY;
	
	private $errors = array();
	private $servers = array();
	
	public $connected = false;
	
	public function connect($api_username = null, $api_key = null)
	{
		
		if(!empty($api_username)) $this->api_username = $api_username;
		if(!empty($api_key)) $this->api_key = $api_key;
		
		$data = array(
			'credentials'  => array(
				'username' => $this->api_username,
				'key'      => $this->api_key
			)
		);
		
		$response = $this->sendRequest('auth','post', $data);

		if(!$this->hasErrors()){
			$this->auth_token  = $response->auth->token->id;
			$this->server_base = str_replace("\n",'',$response->auth->serviceCatalog->cloudServers[0]->publicURL);
			
			$this->connected = true;
			return true;
		} else {
			return false;
		}
		
	}
        
    private function sendRequest($path, $method = 'get', $data = null)
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
		
		if(stripos($path, 'updates') !== false){
			$base      = $this->server_base;
			$path = 'servers';
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
        $response = $this->sendRequest('servers');
        
        if(!$this->hasErrors()){
			$servers = $response->servers;
            return $response->servers;
        } else {
            return false;
        }
    }
        
    public function getServerDetail($id)
    {
        $response = $this->sendRequest('servers/' . $id);
        
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
				
        $response = $this->sendRequest('servers', 'post', $data);
      
        if(!$this->hasErrors()){
            if($this->curl_response > 299){
				$this->setError($response);
               // $response = 'HTTP Status: ' . $this->curl_response . ': ' . json_encode($response);
                return false;
            }
            return $response->server;
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
        
        $response = $this->sendRequest('images', 'post', $data);
        
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
        $response = $this->sendRequest('servers/' . $id, 'delete');
        if($this->curl_response > 299){
            $this->setError('Received response: ' . $this->curl_response);
			$this->setError($response);
        }
        if(!$this->hasErrors()){
            return true;
        } else {
            return false;
        }
    }
        
    public function getImages()
    {
        $response = $this->sendRequest('images/detail');
		
        if(!$this->hasErrors()){
            return $response->images;
        } else {
            return false;
        }
    }
	
	public function getUpdates()
    {
        $response = $this->sendRequest('updates', 'get', array('changes-since' => strtotime('-4 hours')));
		
        if(!$this->hasErrors()){
			echo 'Updates OK';
			print_r($response);
            return $response;
        } else {
			print_r($this->errors);
            return false;
        }
    }
        
    public function getLimits()
    {
        $response = $this->sendRequest('limits');
        
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
        return $this->errors;
    }
        
    public function hasErrors()
    {
        return !empty($this->errors);
    }
   
}