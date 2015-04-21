<?php

$jsopts = array(
	'data-config' => isset($config) ? $config : '/js/config.js',
	'data-require' => isset($require) ? $require : '/js/app.js' 
);

$file = WWW_ROOT . 'js/controllers/' . $this->params['controller'].'.js';
$action_file = WWW_ROOT . 'js/controllers/' . $this->params['controller'].'-'.$this->params['action'].'.js';

if( file_exists( $action_file ) ){
	$jsopts['data-onready'] = '$/'.$this->params['controller'].'-'.$this->params['action'].'.js'; 
}elseif( file_exists( $file ) ){
	$jsopts['data-onready'] = '$/'.$this->params['controller'].'.js'; 
}
	
echo $this->Html->script('app/require', $jsopts );