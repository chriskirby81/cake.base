<?php

App::uses('Component', 'Controller');

class FilesComponent extends Component {
	
	public function beforeFilter() {
        parent::beforeFilter();
    }
	
	public function write($path, $content){
		if(file_put_contents($path, $content)){
			return true;
		}else{
			return false;
		}
	}
	
     public function read($file){
	    $file_handle = fopen($file, "r");
		$values = array();
		$i = 0;
		while (!feof($file_handle) ) {
		
			$line_of_text = fgetcsv($file_handle, 1024);
			$values[$i] = $line_of_text;
			$i++;
		
		}
		fclose($file_handle);
		
		return $values;
    }
	
	public function moveUploadedFile($tmp, $new) {
		move_uploaded_file( $tmp, $new);
		return file_exists($new) ? true : false;
    }
	
	public function cleanName($name){
		$name_b = preg_replace('/[^\w\-'. (true ? '~_\.' : ''). ']+/u', '-', $name);
		return mb_strtolower(preg_replace('/--+/u', '-', $name_b), 'UTF-8');
	}
   
	public function createDirectory($path, $folder = null, $perms = 0777){
		
		$path = trim($path, DS);
		$dir = ($folder == null ? $path : $path.DS.$folder);
		
		if(!is_dir($dir)){
			$path_parts = explode(DS, $dir);
			$path_str = '';
			foreach($path_parts as $part){
				if(!empty($part)){
					$path_str .= DS.$part;
					if(!is_dir($path_str)){
						$oldmask = umask(0);
						mkdir($path_str, $perms);
						umask($oldmask);
					}
				}
			}
		}
		
		if(!is_dir($dir)) return false;
		
		return $dir;
	}
	
	public function getLocation($path){
		
		return substr($location,0,1) == '/' ? 'local' : 'remote';
		
	}
	
	public function fileFromURL($fileurl){
		
		$ext  = pathinfo($fileurl, PATHINFO_EXTENSION);
		if(!$contents = file_get_contents($fileurl)){
			return false;
		};
		
		$unique = md5(time() . $fileurl);
		$tmp = "/tmp/DLPIC_".$unique;
		$handle = fopen($tmp, "w");
		fwrite($handle, $contents);
		fclose($handle);
		//Create File Array
		$file = array();
		$file['name'] = basename($fileurl);
		$file['type'] = 'image/'.$ext;
		$file['size'] = filesize($tmp);
		$file['error'] = 0;
		$file['tmp_name'] = $tmp;
		
		return $file;
		
	}
	
	public function isUploadedFile($params) {
		
		$val = array_shift($params);
		if ((isset($val['error']) && $val['error'] == 0) ||
			(!empty( $val['tmp_name']) && $val['tmp_name'] != 'none')
		) {
			return is_uploaded_file($val['tmp_name']);
		}
		return false;
	}
	
	public function forceDownload($path = null, $name = null){
		$this->response->file($path, array('download' => true, 'name' => $name));
		
	}
	
	public function saveToCDN( $file = null ){
		
	}
	
}