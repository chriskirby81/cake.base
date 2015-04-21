<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('FormHelper', 'View/Helper'); 
App::uses('CakeValidationRule', 'Model/Validator');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class CustomFormHelper extends FormHelper {
	
	public $field_counts = array();
	public $script = true;
	public $skip_fields = array('created', 'modified', 'deleted');
	
	public function getInputId($type = 'default'){
		if(!isset($this->field_counts[$type])){
			$this->field_counts[$type] = 1;
		}else{
			$this->field_counts[$type]++;
		}
		
		return $type . '_' . $this->field_counts[$type];
	}
	
	public function create($model = null, $options = array()){
		$this->form_model = $model;
		if(isset($options['script']) && $options['script'] == false){
			$this->script = false;
			unset($options['script']);
		}
		$options['novalidate'] = true;
		return parent::create($model, $options);
	}
	
	public function end($title=null,$options=null){
		if($this->script == true){
			$script = "<script> require('form'); </script>";
			return parent::end($title, $options).$script;
		}else{
			$this->script = true	;
		}
		return parent::end($title, $options);
	}
	
	
	
	public function fileDrop($fieldName, $options = array(), $attributes = array()){
		
		$options['type'] = 'hidden';
		
		$id = $this->getInputId('filedrop');
		
		$script = "<script> require(['files/filedrop'], function(dropbox){ console.log(dropbox); dropbox.config({ scriptbase: '/media' }); dropbox.create('{$id}'); }); </script>";
		$field = '';
		$field = parent::file($fieldName);
		
		return '<div id="'.$id.'" class="input file_drop">' . $field . '</div> ' . "\n " . $script;
		
	}
	
	public function selector($fieldName, $options = array(), $attributes = array()){
		
		$options['type'] = 'radio';
		
		$options['div'] = array(
			'class' => 'input selector'
		);
		$options['before'] = '<div>';
		$options['separator'] = '</div><div>';
		$options['after'] = '</div>';
		$options['legend'] = false;
		
		return parent::input($fieldName, $options, $attributes);
		
    }
	
	public function parseFieldModel($fieldName = null)
	{
		$field = new stdClass;
		$field->name = $fieldName;
		$field->model = null;
		
		if (strpos($fieldName,'.') !== false){
			$parts = explode(".", $fieldName);
			$field->name = $parts[count($parts)-1];
			$field->model= $parts[0];
		}	
		
		$field->model = empty($field->model) ? $this->defaultModel : $field->model;
		
		return $field;
		
	}
	
	public function getFieldValidation($modelName = null, $fieldName = null){
		
		$validation = null;
		$model = isset( $this->_models[$modelName] ) ? $this->_models[$modelName] : $this->_getModel($modelName);
		if(!empty($model) && isset($model->validate[$fieldName])){
			$validation = $model->validate[$fieldName];
		}
		return !empty($validation) ? $this->parseFieldValidation($validation) : array();
		
	}
	
	public function parseFieldValidation($rules = array(), $key = null){
		
		$rule = array();
		
		if(!empty($key)){
			if($key == 'required'){
				 $rule['required'] = true;
			}
		}
		
		if(!empty($rules)){
			if(isset($rules['rule'])){
				//One Rule per field 
				$rule['rule'] = $rules['rule'];
				if(isset($rule['required'])) $rule['required'] = $rule['required'];
				if(isset($rule['allowEmpty'])) $rule['allowEmpty'] = $rule['allowEmpty'];
				if(isset($rule['on'])) $rule['on'] = $rule['on'];
				if(isset($rule['message'])) $rule['message'] = $rule['message'];
			}elseif(is_array($rules) && count($rules) > 0){
				//Multiple Rules per Field
				foreach($rules as $key2 => $val){
					$rule[] = $this->parseFieldValidation($val, $key2);
				}
			}else{
				//Simple Rule
				$rule = $rules;
			}
		}
	
		return $rule;
		
	}
	
	
	public function setFieldValidation($options = array(), $validations = array(), $schema = array()){
		if(!isset($options['type'])) $options['type'] = 'text';
		foreach($validations as $validation){
			
			$rule = null;
			$regex = '';
			$rulename = null;
			if(isset($validation['rule'])){
				$rule = $validation['rule'];
				$params = null;
				if(is_array($rule)){
					$rulename = $rule[0];
					if(count($rule) > 1){
						array_shift($rule);
						$params = $rule;
					}
				}elseif(is_string($rule)){
					$rulename = $rule;
				}
			}
			
			
			
			switch($rulename){
				case 'alphaNumeric':
					$options['data-chars'] = 'alphanumeric';
					$regex .= 'a-z0-9';
				break;
				case 'numeric':
					$options['data-chars'] = 'numeric';
					$regex .= '0-9';
				break;
				case 'between':
					$options['data-minlength'] = $params[0];
					$options['maxlength'] = $params[1];
				break;
				case 'minLength':
					$options['data-minlength'] = $params[0];
				break;
				case 'maxLength':
					$options['maxlength'] = $params[0];
				break;
				case 'allowEmpty':
					$options['data-allow-empty'] = $params[0];
				break;
				case 'isUnique':
					$options['data-is-unique'] = true;
				break;
				case 'notEmpty':
					$options['data-allow-empty'] = $params[0];
				break;
				case 'money':
				break;
				case 'email':
					
				break;
			}
			
			if(!empty($regex)){
				$options['pattern'] = '['.$regex.']';
			}
			
		}
		
		if(!empty($schema)){
			if( !isset($options['maxlength']) && isset($schema['length']) ){
				if($options['type'] != 'checkbox') $options['maxlength'] = $schema['length'];
			}
			if( !isset( $options['required'] ) && isset($schema['null']) && empty($schema['null']) ){
				$options['required'] = true;
			}
			if(!isset($options['data-chars']) && isset($schema['type'])){
				$options['data-chars'] = $schema['type'];
			}
		}
		
		//print_r($rn);
		
		
		//print_r($options);
		
		return $options;
		
	}
	
	public function getModelSchema($modelName = null, $fieldName = null){
		
		$validation = null;
		$model = null;
		
		if(isset($this->_models[$modelName])){
			$model = $this->_models[$modelName];
		}else{
			$model = $this->_getModel($modelName);
		}
		
		if(!empty($fieldName)){
			return isset($model->_schema[$fieldName]) ? $model->_schema[$fieldName] : null;
		}else{
			return $model->_schema;
		}
		
		
	}
	
	public function input($fieldName, $options = array(), $attributes = array()){
		
		$modelName = null;
		
		$field = $this->parseFieldModel($fieldName);
		$suffix = $fieldName;
		$schema = $this->getModelSchema($field->model, $field->name);
		//print_r($schema);
		
		$validations = $this->getFieldValidation($field->model, $field->name);
		//print_r($validations);
	
		//$key = $this->_introspectModel($field->model, 'key', $field->name);
		//$fields = $this->_introspectModel($field->model, 'fields', $field->name);
		//$validates = $this->_introspectModel($field->model, 'validates', $field->name);
		//$errors = $this->_introspectModel($field->model, 'errors', $field->name);
		//print_r($fields);
	//	print_r($validates);
	//	print_r($errors);
		
		
		$options = $this->setFieldValidation( $options, $validations, $schema );
		
		$type = isset($options['type']) ? $options['type'] : null;
		
				
		if(empty($type)){
			
			$cfields = array('password', 'phone', 'email');
			if(in_array($field->name, $cfields)){
				$type = $field->name;
			}
			if(preg_match('/date$/', $field->name)){
				$type = 'datetime';	
			}
			if(empty($type)){
				$type = 'text';	
			}
		}
		
		//Handel Types
		
		switch($type){
			case 'text':
				//Remove Label and Add Label Style 
				$label = isset($options['label']) ? $options['label'] : ucwords(str_replace('_', ' ', $field->name));
				$options['label'] = $label;
				$options['type'] = $type;
				$options['after'] = '<div class="field_status"></div>';
			break;	
			case 'email':
				//Remove Label and Add Label Style 
				$label = isset($options['label']) ? $options['label'] : ucwords(str_replace('_', ' ', $field->name));
				if(!isset($options['placeholder'])) $options['placeholder'] = 'example@website.com';
				
				$options['label'] = $label;
				$options['type'] = $type;
				$options['after'] = '<div class="field_status"></div>';
			break;	
			case 'password':
				//Handel Passwords
				if(!isset($options['label'])) $options['label'] = 'Password';
				$options['after'] = '<div class="field_status"></div>';
				if(!isset($options['placeholder'])) $options['placeholder'] = '- - - - - - ';
			break;
			case 'datetime':
				//Handel Passwords
				$options['type'] = 'datetime';
				$options['dateFormat'] = 'DMY';
				$options['after'] = '<div class="field_status"></div>';
				if(!isset($options['placeholder'])) $options['placeholder'] = 'MM/DD/YYYY';
			break;
			case 'select':
				//Remove Label and Add Label Style 
				$label = isset($options['label']) ? $options['label'] : ucwords(str_replace('_', ' ', $field->name));
				$options['label'] = $label;
				$options['type'] = $type;
				$options['between'] = '<div class="cselect">';
				$options['after'] = '</div><div class="field_status"></div>';
			break;	
			case 'currency':
				$options['type'] = 'text';
				$options['between'] = '<span>$</span>';
			break;
			case 'link':
				$options['type'] = 'text';
				$options['div'] = array(
					'class' => 'input link'
				);
			break;
		}
				
		return parent::input($fieldName, $options, $attributes);
		
	}
	
	public function datepicker($fieldName, $options = array(), $attributes = array()){
		
		$options['div'] = array(
			'class' => 'input datepicker'
		);
		
		$options['placeholder'] = 'MM/DD/YYYY';
		
		$options['type'] = 'text';
		$options['before'] = parent::input($fieldName, array('type' => 'hidden', 'div' => false), array());
		
		$field = parent::input('_'.$fieldName, $options, $attributes);
		
		
		
        return $field;
    }
	
	public function link($fieldName, $options = array(), $attributes = array()){
		$options['type'] = 'link';
       return $this->input($fieldName, $options, $attributes);
    }
	
	public function textarea($fieldName, $options = array(), $attributes = array()){
		
		
		$options['div'] = array(
			'class' => 'textarea'
		);
		//$options['type'] = 'textarea';
		if(!isset($options['label'])) $options['label'] = $fieldName;
		
       return parent::textarea($fieldName, $options, $attributes);
    }

	
	public function wysiwyg($fieldName, $options = array(), $attributes = array()){
	
		$options['label'] = false;
		$options['class'] = 'wysiwyg-source';
		$before = '<div class="input wysiwyg">';
		$before .= '<header></header>';
		$before .= '<aside class="view_wrap" >';
		$field = parent::textarea($fieldName, $options, $attributes );
		$after = '<div class="wysiwyg-view" ></div></aside></div>';
		return $before . $field . $after;
    }

}
