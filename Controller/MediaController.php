<?php
App::uses('AppController', 'Controller');

class MediaController extends AppController {
	
	var $name = 'Media';
	var $models = array('Media');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow();
	}
	
	public function index(){
		$this->layout = 'default';
		$this->set('title_for_layout',  'Chris Kirby Media');
	}
	
	public function config(){
		$configs = array(
			'file_uploads' => ini_get('file_uploads'),
		 	'post_max_size' => ini_get('post_max_size'),
		 	'upload_max_filesize' => ini_get('upload_max_filesize'),
		 	'max_file_uploads' => ini_get('max_file_uploads'),
		 	'memory_limit' => ini_get('memory_limit')
		);
		echo json_encode($configs);
		exit;
	}
	
	public function add( $responce_type = null){
		
		if(!empty($responce_type) && $responce_type == 'json') 
			header('Content-Type: application/json');
		
		if($this->request->is('post') || $this->request->is('put')) {
				
			if(isset($this->request->data['Media'])){
				
				if(count($_FILES) > 0){
					//Request Contains Files
					$file = array_shift($_FILES);
					$unique_id = uniqid();
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					$type = $file['type'];
					$mime_parts = explode('/', $mime_type);
					$media_dir = WWW_ROOT . DS . 'media';
					$type_path = $media_dir . DS . $mime_parts[0];
					$new_path = $type_path.DS.$unique_id.'.'.$ext;
					move_uploaded_file($file['tmp_name'], $new_path );
					$this->request->data['Media']['source'] = $new_path;
				}
				
				if($this->Media->save($this->request->data)){
					//$this->redirect('edit'.DS.$this->Media->id);
					$media_id = $this->Media->id;
					echo $media_id;
				}
			}
		}
		$this->set('title_for_layout',  'Add Media');
		$this->set( 'action', 'add' );
		
		if(!empty($responce_type)) exit;
		$this->render('dropbox');
		
	}
	
	public function upload(){
		
		if(!is_dir(TMP.DS.'uploads')) mkdir(TMP.DS.'uploads', 0777);
				
		if(( $this->request->is('post') || $this->request->is('put')) && isset($this->request->data['id']) ){
				
			$id = $this->request->data['id'];
			$file_dir = TMP . 'uploads' . DS . $id;
			if(!is_dir($file_dir)) mkdir($file_dir, 0777);
			
			$start = $this->request->data['start'];
			$end = $this->request->data['end'];

			if($_FILES['file']['error'] == 0){
				move_uploaded_file($_FILES['file']['tmp_name'], $file_dir . DS . $start . '_' . $end );
				echo 'OK';
			}else{
				echo 'ERROR:'.$_FILES['file']['error'];
			}
		}
		exit;
	}
	public function finish( $id = null ){
		
		if(empty($id)){
			echo 'No ID given';
			exit;
		}
		$file_dir = TMP . 'uploads' . DS . $id;
		$media = $this->Media->findById($id);
				
		$media_dir = WWW_ROOT . 'media';
		if(!is_dir($media_dir)) mkdir($media_dir, 0777);
		
		$type_path = $media_dir . DS . $media['Media']['type'];
		if(!is_dir($type_path)) mkdir($type_path, 0777);
		
		$files = scandir($file_dir);
		sort($files);
		$file_list = array();
		
		foreach($files as $file){
			if( $file == '.' || $file == '..' ) continue;
			$file_list[] = $file_dir . DS . $file;
		}
		$ext = pathinfo($media['Media']['name'], PATHINFO_EXTENSION);
		$unique_id = uniqid();
		
		$final_path = $type_path . DS . $unique_id . '.' . $ext;
		$final = fopen($final_path, 'wb');
		
		foreach($file_list as $file) {
			$src = fopen($file, 'rb');
			stream_copy_to_stream($src, $final);
			fclose($src);
			unlink($file);
		}
		
		fclose($final);
		rmdir($file_dir);
		
		$media['Media']['source'] = str_replace( WWW_ROOT, '/', $final_path );
		if($this->Media->save($media)){
			echo json_encode($media);
		}
		
		exit;
	}
}