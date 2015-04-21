<?php

App::uses('HtmlHelper', 'View/Helper');

class CustomHtmlHelper extends HtmlHelper {
	
   //yadda yadda
	public function link($title, $url = null, $options = array(), $confirmMessage = false){
		$admin = false;
		
		if( ( !empty($options) && isset($options['admin']) ) || 
		( is_array( $url ) && isset( $url['admin'] ) ) ){
			$admin = true;
			unset($options['admin']);
			unset($url['admin']);
		}
		if($admin){
			if(is_array( $url )){
				$url = ADMIN_BASE . parent::url( $url );
			}else{
				$url = ADMIN_BASE . $url;
				$options = array();
			}
		}
		
		
		
		return parent::link($title, $url, $options, $confirmMessage );
		
	}
	
	
	
}