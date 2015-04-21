<?php

App::uses('AppHelper', 'View/Helper'); 
App::uses('GraphicComponent', 'Controller/Component');
/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class GraphicHelper extends AppHelper {
		
	public function image( $path = null, $params = array(), $_ext = null ){
		
		$url = "";
		$ext = !empty($_ext) ? $_ext : pathinfo($path, PATHINFO_EXTENSION);
		$params['ext'] = $ext;
		
		$path = str_replace(' ','%20', $path);
		$path_opts = '';
		if(!empty($params)) 
			$path_opts = implode('&', array_map(function ($v, $k) { 
			return $k . '=' . $v; 
			}, $params, array_keys($params)));
		
		$name = md5($path . $path_opts);
		$dir = WWW_ROOT.'img/processed';
		
		$local = $dir . DS . $name .'.'.$ext;
		//return $local;
		if( file_exists( $local ) ){
			return '/img/processed/'.$name.'.'.$ext;
		}else{
			return '/graphic/image/?path='.urlencode($path).'&'.$path_opts;
		}
		
	}

}
