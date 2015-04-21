<?php

App::uses('Component', 'Controller');
require_once(APP.'Vendor'.DS.'PHPExcel/PHPExcel.php');

class SpreadSheetComponent extends Component {
	
	var $components = array('Session', 'Excel');
	public $keys = array();
	public $crumbs = array();
	public $legend = array();
	
	public $looper = null;
	public $row_count = null;
	public $column_count = null;
	public $structure = array();
	public $groups = array();
	public $type = null;
	public $file_path = null;
	
	
	
	private $formats = array(
		'Excel' => array(
			'ods', 'csv', 'xlsx', 'xml', 'xls', 'slk', 'gnumeric'
		)
	);
	
	
	public function beforeFilter() {
		
		parent::beforeFilter();
    }
	
	function import($file_path = null){
		$this->looper = new stdClass();
		$this->file_path = $file_path;
		
		if(file_exists($this->file_path)){
			$ext = pathinfo($this->file_path, PATHINFO_EXTENSION);
			foreach($this->formats as $key => $format){
				$type_supported = false;
				if(in_array($ext, $format)){
					$type_supported = true;
					$this->data = $this->$key->import($this->file_path);
					$this->removeEmptyColumns();
					$this->prepareData();
				}
				
				if(!$type_supported){
					$this->Session->setFlash(__('Sorry, this document Type is not supported at this time.'));
				}
			}
		}else{
			$this->Session->setFlash(__('Sorry, this file could not be found in our system.'));
			return false;
		}
		
		

	}
	
	
	public function getValue($row, $column){
		return $this->data[$row][$column];
	}
	
	public function removeEmptyColumns(){
		//Remove Empty Columns
		$tmp_data = $this->data;
		for($i=0;$i<count($this->data[0]);$i++){
			
			$this->looper->has_data = false;
			//Loop Colmn
			$params = array(
				'data' => $tmp_data,
				'column' => $i,
				'function' => function($controller){
					if(!empty($controller->looper->value)){
						$controller->looper->has_data = true;
					}
				}
			);
			
			$this->loop('column', $params);
			
			if(!$this->looper->has_data){
				$this->clear('column', array('column' => $i));
			}
		}
		
	}
		
	public function getData(){
		return array(
			'keys' => $this->keys,
			'crumbs' => $this->crumbs,
			'legend' => $this->legend,
			'data' => $this->data,
			'structure' => $this->structure,
			'groups' => $this->groups
		);
		
	}
	

	public function clear($type, $params){
		switch($type){
			case 'column':
				foreach($this->data as $key => $row){
					$Xcolumn = $params['column'];
					unset($this->data[$key][$Xcolumn]);
				}
				$this->column_count = count($this->data[0]);
			break;
			case 'row':
				unset($this->data[$Xrow]);
				$this->row_count = count($this->data);
			break;
		}
	}

	public function loop($type, $params){
		$data = $params['data'];
		switch($type){
			case 'sheet':
				$this->looper->value = '';
				$this->looper->r = 0;
				foreach($data as $row){
					$this->looper->c = 0;
					foreach($row as $column){
						$this->looper->value = $column;
						$funct = $params['function'];
						$funct($this);
						$this->looper->c++;
					}
					$this->looper->r++;
				}
			break;
			case 'column':
				$this->looper->r = 0;
				$column = $params['column'];
				foreach($data as $row){
					$this->looper->c = $column;
					$this->looper->value = $row[$column];
					$funct = $params['function'];
					$controller = $this;
					$funct($this);
				}
				$this->looper->r++;
			break;
			case 'row':
			break;
		}
	}

	public function getCatChildren($cat){
		
		//Set Column / Row 
		$c = $cat['column'];
		$r = $cat['row'];
		//Set Start / End Column
		$start = $cat['column'];
		$end = ($cat['column'] + $cat['span'])-1;
		//Get Target Row Directly Benith
		$target_row = $cat['row']+1;
		//Set Empty Children Array
		$children = array();

		//print_r('Columns:  '.$start.' - '.$end." \n");	
		//Find Children and Set span Count of Cat Children.
		foreach($this->legend as $row => $legend_row){
			//If This Row is the target
			if($row == $target_row){
				//Treverse Columns
				foreach($legend_row as $col => $column){
					//If Column is Between Start and Finish Column
					if($col >= $start && $col <= $end){
						//print_r('Row: '.$row.' - Column: '.$col." \n");	
						//If Column Not Empty
						if(!empty($column)){
							//print_r('Value: '.$column." \n");
							$children[] = array(
								'title' => $column, 
								'span' => 1, 
								'row' => $row, 
								'column' => $col,
								'children' => array()
							);	
						}else{
							if(count($children)){
							$children[count($children)-1]['span']++;
							}
						}
					}
				}
			}
		}
		
		foreach($children as $key => $cat){
			$children[$key]['children'] = $this->getCatChildren($cat);
		}
		
		//If no Children Return NULL
		if(!count($children)){
			$children = null;
		}
		
		return $children;
	
	}
	
	public function seporateCrumbs(){
		//Remove Crumbs
		foreach($this->data as $rkey => $row){
			$has_data = array();
			foreach($row as $key => $column){
				if(!empty($column)){
					$has_data[$key] = true;
				}
			}
			if(count($has_data) == 1 && $has_data[0] == true){
				//Add Row To Crumbs
				$this->crumbs[] = $row[0];
				//Remove Crumb from Data
				unset($this->data[$rkey]);
			}
		}
		//Merge Data
		$this->data = array_merge(array(), $this->data);
	}
	
	public function findStructure(){
		
		$this->seporateCrumbs();
		
		foreach($this->data as $rkey => $row){
			//If keys count less than row cell count
			//echo 'Keys: '.count($keys).' Row: '.count($row)." \n";
			if(count($this->keys) < count($row)){
				//Add Row to legend.
				//echo 'Add Row'." \n";
				$this->legend[] = $row;
				//Remove Legend Rows From data
				unset($this->data[$rkey]);
			}
			foreach($row as $key => $column){
				
				//If Title Cell count is less that cell count
				if(count($this->keys) < count($row)){
					//If Column NOT empty
					if(!empty($column)){
						//Add Key to Legend
						$this->keys[$key] = $column;
						
					}
				}
				
			}
			
			
			
		}
		//Sort Keys By Key
		ksort($this->keys);
		//Shift Data Up
		$this->data = array_merge(array(), $this->data);
		//Get Legend Columns
		foreach($this->legend as $row_key => $legend_row){
			$last = '';
			foreach($legend_row as $column_key => $column_value){
				
				if(!isset($this->groups[$column_key])){ 
					$this->groups[$column_key] = array(); 
				}
				
				$is_key = $column_value == $this->keys[$column_key] ? true : false;
				
				if(empty($column_value) && !$is_key){
					$column_value = $last;
				}
				
				$last = $is_key ? '' : $column_value;
				
				if(!empty($column_value)){
				$this->groups[$column_key][$row_key] = $column_value;
				}
			}
			
		}
		
		
		$this->structure = $this->getCatChildren(array(
			'title' => 'data', 
			'span' => count($this->legend[0]), 
			'row' => -1, 
			'column' => 0,
			'children' => array()));
	}
	


	public function prepareData(){
		
		$this->findStructure();

		$legend_rows = count($this->legend);
		//echo 'Crumbs: '." \n";
		//print_r($this->crumbs);

		//echo 'Structure: '." \n";
		//print_r($this->structure);
		
		//echo 'Keys: '." \n";
		//print_r($this->keys);
	}

	public function structureHTML($structure_data){
		$html = '';
		$html .= '<tr>';
		foreach($structure_data as $structure){
			
			$title = $structure['title'];
			$class="";
			if(empty($structure['children'])){
				$class = 'key';
			}else{
				$class = 'data_set';
			}
			
			$html .= '<td class="'.$class.'">';
			if(!empty($structure['children'])){
				$html .= '<table width="100%"><tr><td colspan="'.$structure['span'].'">';
				
			}
			
			$html .= $title;
			
			if(!empty($structure['children'])){
				$html .= $this->structureHTML($structure['children']);
				$html .= '</td></tr></table>';
			}
			$html .= '</td>';
		}
		$html .= '</tr>';
		return $html;
	}
   
}