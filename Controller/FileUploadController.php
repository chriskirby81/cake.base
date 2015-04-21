<?php

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class FileController extends AppController {
	
	var $name = 'File';
	
	var $components = array('Files', 'Session');
	var $uses = array('File');
	
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }
	
	public function upload($action = null){
		
		if(empty($action)){
			
			
			$this->layout = false;
			$error = false;
			$files = $rdata = array();
			$upload_data = array();

			$upload_id = $_POST[ini_get("session.upload_progress.name")];
			$upload_dir = TMP . "uploads/{$upload_id}/";

			$uploadFolder = new Folder( $upload_dir );
			if(empty($uploadFolder->path)) $uploadFolder->create( $upload_dir, 0777);
			
			$_SESSION[$upload_id] = 'transfering';
			
			foreach($_FILES as $file){
				
				//$filename = $this->Files->cleanName($file['name']);
				$filename = uniqid().'.'.pathinfo($file['name'], PATHINFO_EXTENSION);

				$new_path = $upload_dir . $filename;
				move_uploaded_file( $file['tmp_name'], $new_path );
				$tmpFile = new File($uploadFolder->path . $filename);
				
				if($tmpFile->exists())
				{
					unset($file['tmp_name']);
					$file['path'] = $upload_id . DS . $filename;
					$file['user_id'] = 1;
					$this->File->create();
					$this->File->save($file);
					$file['id'] = $this->File->id;
					
					
				}else $file['error'] = 1;
				
				$files[] = $file;
				$rdata[] = $file;
			}
			
			
			
			$upload_data['upload_id'] = $upload_id;
			$upload_data['files'] = $files;
				
			if(isset($_POST['type']) && $_POST['type'] == 'iframe'){
				$this->layout = 'ajax';
				$this->set('upload_data', $upload_data);
				$this->set('post_type', $_POST['type']);
			}else{
				echo json_encode($upload_data);
				exit;
			}
		}else{
			
			if($action == 'check'){
				$this->layout = false;
				//Close Cake Session
				session_write_close();
				//Initiate PHPSession
				session_name('PHPSESSID');
				//Star PHP Session
				session_start();
				
				if(isset($_POST['progress_keys'])):

					$progress_keys = $_POST['progress_keys'];
					$progress = array();
					foreach($progress_keys as $key){
						$progress_key = ini_get("session.upload_progress.prefix") . $key;

						if(isset($_SESSION[$progress_key])){
							$progress[]['data'] = $_SESSION[$progress_key];
						}else{
							$progress[]['data'] = 'error';
						}
						$progress[count($progress)-1]['key'] = $key;
					}
					echo json_encode($progress);
				else:
				echo 'E:empty_progress_key';
				endif;
				
				//Change Back to CAKEPHP session name
				session_write_close();
				session_name('CAKEPHP');
				session_start();
				exit;
			}elseif($action == 'transfer'){
				echo 'Transfer';
			}
		}
		
	}
	
	public function get($file_id = null){
		$file = $this->File->findById($file_id);
		$path = APP.$file['File']['path'];
		print_r($path);
		exit;
	}
	
	
	public function uploadInfo(){
		$upload_id = $_POST[ini_get("session.upload_progress.name")];
		
		echo 'Upload ID: '.$upload_id;
		var_dump($_SESSION);
		
		$info = array();
		if (function_exists("uploadprogress_get_info")) {
			$info = uploadprogress_get_info($upload_id);
			if(!is_array($info)){
				$info['error'] = 'ERR';
			}
		} else {
			$target_path = APP."tmp/uploads/{$upload_id}/";
			$progress_key = strtolower(ini_get("session.upload_progress.prefix").$upload_id);
			if(isset($_SESSION[$progress_key])){
				$info = $_SESSION[$progress_key];
			}else{
				$info = '!! Upload Session Not Set';
			}
	
		}
		
		echo json_encode($info);
		exit;
	}
	
	public function transfer_upload(){
		

	}
	
	
	
}