<?php

App::uses('Component', 'Controller');
require_once(APP.'Vendor'.DS.'PHPExcel/PHPExcel.php');

class ExcelComponent extends Component {
	
		public function import($file_path){
			$this->file_path = $file_path;
				//Get Doc Type
			$this->type = PHPExcel_IOFactory::identify($this->file_path);
			//Create Reader
			$reader = PHPExcel_IOFactory::createReader($this->type);
			/** Load $file_path to a PHPExcel Object  **/
			$data = $reader->load($this->file_path);
			//Load Active Sheet
			$workSheet = $data->getActiveSheet();
			
			$r = 0;
			$data = array();

			foreach ($workSheet->getRowIterator() as $row) {
				$data[$r] = array();
				$c = 0;
  				$cellIterator = $row->getCellIterator();
  				$cellIterator->setIterateOnlyExistingCells(false); // This loops all cells,
			  	foreach ($cellIterator as $cell) {
				  	$data[$r][$c] = trim($cell->getValue());
					$c++;
			  	}
				$r++;
			}
			return $data;
		}
			
}