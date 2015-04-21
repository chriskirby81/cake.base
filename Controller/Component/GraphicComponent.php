<?php

App::uses('Component', 'Controller');

class GraphicComponent  extends Component{
	
	public $name = 'Graphic';
	
	public $components = array('Files', 'Graphics');

	public $webroot = null;
	public $image = null;
	public $final = null;
	public $source = null;
	public $edits = null;
	public $data = null;
	public $options = null;
	
	public function init($path, $params = null, $options = array()){
		
		$this->source = new stdClass;
		$this->final = new stdClass;
		$this->data = new stdClass;
		$ext = null;

		if(!empty($options)){
			if(isset($options['ext'])) $ext = $options['ext'];
			$this->options = $options;
		}
		
		if(empty($ext)) $ext  = pathinfo($path, PATHINFO_EXTENSION);
	
		$this->source->path = str_replace(' ','%20', $path);
		$this->final->dir = WWW_ROOT.'img/processed';
		
		if(!empty($params)){
			$this->final->name = md5($this->source->path.$params);
		}else{
			$this->final->name = md5($this->source->path);
		}		
		
		$this->final->ext = $ext;
		$this->final->path = $this->final->dir.'/'.$this->final->name .'.'. $this->final->ext;
	
		if(!is_dir( $this->final->dir )) mkdir( $this->final->dir, 0777 );
		
		if($this->checkDirs()){
			//Load Image into Object
			$this->load();
			if(!empty($this->image)){
				//Image loaded
				$this->set( $options );
			}else{
				$this->error('Could not load image into object');
			}
		}
		
	}
	
	public function checkDirs(){
		if(!empty($this->options) && 
			!is_dir($this->final->dir) && 
				!mkdir($this->final->dir, 0777, true) ) return false;
		return true;
	}
		
	public function error($msg = null){
		$this->flash($msg, 'error');
	}
	
	public function load(){
		
		$this->tmp = new stdClass;
		$this->edits = new stdClass;
		$tmp = $this->Files->fileFromURL( $this->source->path );

		$this->tmp->type = $tmp['type'];
		$this->tmp->path = $tmp['tmp_name'];
		
		$size = $this->Graphics->getImageSize($this->tmp->path);
		$this->source =  (object)array_merge((array)$this->source, (array)$size);
		
		if($this->tmp->path){
			$this->image = $this->Graphics->createImageFromFile($this->tmp->path);
		}
	}
	
	public function apply(){
		
		$edits = $this->edits;
		$source = $this->source;
		
		//Edit Dimentions
		if( isset( $edits->height ) || isset( $edits->width ) ){
			$edits->resize = true;
			//Height Set
			$edits->width = isset( $edits->width ) ? 
				$edits->width : $edits->height * $source->aspect;
			//Width Set
			$edits->height = isset( $edits->height ) ? 
				$edits->height : $edits->width / $source->aspect;
		}

		//Max Height
		if(isset( $edits->maxHeight ) && $edits->height > $edits->maxHeight ){
			$edits->resize = true;
			$this->Graphics->scale( $source, $edits, $edits->maxHeight / $source->height  );
		}
		//Max Width
		if(isset( $edits->maxWidth ) && $edits->width > $edits->maxWidth ){
			$edits->resize = true;
			$this->Graphics->scale( $source, $edits, $edits->maxWidth / $source->width  );
		}
		
		if( isset( $edits->cropW ) || isset( $edits->cropH )  ){
			$edits->crop = true;
			$edits->cropW = isset( $edits->cropW ) ? $edits->cropW : 
				( isset( $edits->width ) ? $edits->width : $source->width );
			$edits->cropH = isset( $edits->cropH ) ? $edits->cropH : 
				( isset( $edits->height ) ? $edits->height : $source->height );
			$edits->cropY = isset( $edits->cropY ) ? $edits->cropY : 0;
			$edits->cropX = isset( $edits->cropX ) ? $edits->cropX : 0;
		}
		
		
		$this->edits = $edits;
		$this->source = $source;
		
	
		
		if(isset( $this->edits->width )) $this->edits->width = round( $this->edits->width );
		if(isset( $this->edits->height )) $this->edits->height = round( $this->edits->height );
		
		if(!isset( $this->edits->resize )) $this->edits->resize = false;
		if(!isset( $this->edits->crop )) $this->edits->crop = false;
	}
	
	public function applyPreset($preset = null){
		
		if(!empty($preset)){
			$this->options = $preset;
		}
		
		if(!empty($this->options)){
			
			$pre = $this->Graphics->getPreset($this->options);
			
			if(isset($pre->altwidth) && $this->source->width+100 < $pre->width){
				$pre->width = $pre->altwidth;
				$pre->height = $pre->altheight;
			}
			
			//Merge Preset with edits
			$this->edits =  (object)array_merge((array)$this->edits, (array)$pre);
			
			$this->edits->scale = $this->Graphics->scaleTo($this->source, $this->edits, 'fill');
			
			$this->edit();
			
		}
				
	}
	
	public function edit(){
		
		//Size Image
		$this->image = $this->Graphics->resizeImage($this->image, $this->source, $this->edits->scale);
		
		if(isset($this->edits->scale->crop)){
			$this->image = $this->Graphics->cropImage($this->image, $this->edits->scale, $this->edits->scale->crop);
		}
			
		
		if(isset($this->edits->fx)){
			$this->applyFX();
		}
		
		if(isset($this->edits->overlay)){
			$this->Graphics->layerImage($this->image, $this->edits->overlay);
		}
		
	}
	
	
	public function info(){
		
		$this->data->source = $this->source;
		//$this->setPreset($this->options);
		if(isset($this->edits)){
			$this->data->final = (object)array_merge((array)$this->final, (array)$this->edits);
		}else{
			$this->setSize();
			$this->data->final = $this->final;
		}
				
		return $this->data;
	}
	
	public function setSize(){
		$size = $this->Graphics->getImageSize($this->final->path);
		$this->final = (object)array_merge((array)$this->final, (array)$size);
	}
	
	public function set( $params ){
		$params = json_decode(json_encode($params), FALSE);
		$this->edits =  (object)array_merge((array)$this->edits, (array)$params);
	}
	

	
	public function details(){
		print_r($this);
		exit;
	}
	
	public function applyFX(){
		$this->Graphics->applyFX($this->image, $this->edits->fx);
	}
	
	public function save( $img = null ){
		if(empty($img)) $img = $this->image;
		switch( $this->final->ext ){
			case 'png':
				imagepng($img, $this->final->path);
			break;
			case 'gif':
				imagegif($img, $this->final->path);
			break;
			default:
				imagejpeg($img, $this->final->path, 80);
		}
				
		if(file_exists($this->final->path)){
			return true;
		}else{
			return false;	
		}
	}
	
	public function process(){
		$transparent = $this->final->ext == 'png' || $this->final->ext == 'gif' ? true : false;
		$this->final->width = $this->source->width;
		$this->final->height = $this->source->height;
		
		//Resize
		if($this->edits->resize){
			
			$canvas = $this->Graphics->createCanvas( 
				$this->edits->width, 
				$this->edits->height, 
				$transparent 
			);
			imagecopyresampled($canvas, $this->image, 0, 0, 0, 0, $this->edits->width, $this->edits->height,  $this->final->width, $this->final->height );
			$this->image = $canvas;
			$this->final->width = $this->edits->width;
			$this->final->height = $this->edits->height;
		}
		
		if($this->edits->crop){
			$canvas = $this->Graphics->createCanvas( 
				$this->edits->cropW, 
				$this->edits->cropH, 
				$transparent 
			);
			imagecopy($canvas, $this->image, 0, 0, $this->edits->cropX, $this->edits->cropY, $this->final->width, $this->final->height );
			$this->final->width = $this->edits->cropW;
			$this->final->height = $this->edits->cropY;
			//resource $dst_im , resource $src_im , int $dst_x , int $dst_y , int $src_x , int $src_y , int $src_w , int $src_h 
			$this->image = $canvas;
		}
		
		return true;
	}
	
	public function display( $save = false ){
	
		$this->process();
	
		switch( $this->final->ext ){
			case 'png':
				header('Content-Type: image/png');
				if($save){
					if( $this->save( $this->image ) )
						readfile( $this->final->path );
				}else{
					imagepng($this->image);
				}
			break;
			case 'gif':
				header('Content-Type: image/gif');
				imagegif($this->image, null, 90);
			break;
			default:
				header('Content-Type: image/jpeg');
				if($save){
					if( $this->save( $this->image ) )
						readfile( $this->final->path );
				}else{
					imagejpeg($this->image, null, 80);
				}
		}
		
		
		exit;
	}
	
}