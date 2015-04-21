<?php

App::uses('Component', 'Controller');

class GraphicsComponent  extends Component{
	
	public $name = 'Graphics';
	
	public $presets = array(
		
	);
	
	public function scale($src, $dest, $ratio ){
		if(isset($src->width)) $dest->width = $src->width * $ratio;
		if(isset($src->height)) $dest->height = $src->height * $ratio;
		return true;
	}
	
	public function scaleTo($src, $target, $type = 'fit'){
		
		$scale = new stdClass;
		$scale->type = $type;
		$target_ratio = $target->width / $target->height;
		$src_ratio = $src->width / $src->height;
		
		if($target_ratio > $src_ratio){
			//Dest Wider than Source
			if($type == 'fill'){
				$scale->width = $target->width;
				$scale->height = $target->width / $src_ratio;
			}elseif($type == 'fit'){
				$scale->height = $target->height;
				$scale->width = $target->height * $src_ratio;
			}
		}elseif($target_ratio < $src_ratio){
			//Dest Taller than Source
			if($type == 'fill'){
				$scale->height = $target->height;
				$scale->width = $target->height * $src_ratio;
			}elseif($type == 'fit'){
				$scale->width = $target->width;
				$scale->height = $target->width / $src_ratio;
			}
		}else{
			//Dest Same Aspect as Source
			$scale->width = $target->width;
			$scale->height = $target->height;
		}
		
		$scale->ratio = $scale->width / $scale->height;
		
		if($type == 'fill'){
			$crop = new stdClass;
			$crop->width = $target->width;
			$crop->height = $target->height;
			$crop->x = $scale->width - $target->width;
			$crop->y = $scale->height - $target->height;
			$scale->crop = $crop;
		}
		
		return $scale;
	}
	
	
	
	function getPreset($type){
		$pre = new stdClass;
		if(!empty($type)){
			//echo $type;	
			switch($type){
				case 'casino_list':
					$pre->width = 200;
					$pre->height = 200;
				break;
			}
		}
		return $pre;
	}
	
	function applyFX($img, $effect){
		switch($effect->type){
			case 'blur':
				$blur_strength = $effect->strength;
				for($i=0;$i<$blur_strength;$i++){
					imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
				}
				return true;
			break;
		}
	}
	
	function getImageSize($path){
		$isize = getimagesize($path);
		if(!empty($isize)){
			$size = new stdClass;
			$size->width = $isize[0];
			$size->height = $isize[1];
			$size->aspect = $size->width / $size->height;
			return $size;
		}else{
			return null;	
		}
	}
	
	function image($file = null){
		return $this->createImageFromFile($file);
	}
	
	function createImageFromFile($file){
		
		$mime = mime_content_type($file);
		$mime_parts = explode('/', $mime);
	
		switch($mime_parts[1]){
			case 'png':
				$image = imagecreatefrompng($file);
			break;
			case 'gif':
				$image = imagecreatefromgif($file);
			break;
			case 'jpeg':
				$image = imagecreatefromjpeg($file);
			break;
			default:
				 die('Invalid image type');
		}
		
		return $image;
	}
	
	function cropImage($image, $src, $crop){
		print_r($crop);
		
		$x = $crop->x/2;
		$y = $crop->y/2;
		
		$cropped = imagecreatetruecolor($crop->width, $crop->height);
		imagecopy($cropped, $image, 0, 0, $x, $y, $src->width, $src->height);
		//resource $dst_im , resource $src_im , int $dst_x , int $dst_y , int $src_x , int $src_y , int $src_w , int $src_h 
		return $cropped;
	}
	
	function resize($image, $src, $dest){
		//print_r($src);
		//print_r($dest);
		//exit;
		$sized = imagecreatetruecolor($dest->width, $dest->height);
		imagecopyresampled($sized, $image, 0, 0, 0, 0, $dest->width, $dest->height,  $src->width, $src->height);
		return $sized;
	}
	
	public function createCanvas( $width, $height, $transparent = false ){
		if($transparent){
			$img = imagecreatetruecolor( $width, $height );
			imagealphablending( $img, false );
			imagesavealpha( $img, true );
			$transp = imagecolorallocatealpha($img, 255, 255, 255, 127);
			imagefilledrectangle($img, 0, 0, $width, $height, $transp);
		}else{
			$img = imagecreatetruecolor($width, $height);
		}
		return $img;
	}
	
	function layerImage($image, $params){
		
		$tmp = $this->Files->fileFromURL($params->img);
		$overlay = $this->createImageFromFile($tmp['tmp_name']);
			
		imagecopyresampled($image, $overlay, $params->x, $params->y, 0, 0, imagesx($overlay), imagesy($overlay), imagesx($overlay), imagesy($overlay));
		
		
	}
	
	
}