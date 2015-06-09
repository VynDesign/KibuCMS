<?php

/**
 * Description of CSVFile
 *
 * @author vyn
 */
	require_once './kibu/core/framework/file/File.php';

	class CsvFile extends File {

		public $delimiter;
		public $enclosure;
		public $escape;
		protected $firstLineKeys;
		
		public function __construct($file, $delimiter = ',', $enclosure = '\"', $escape = "'\'") {
			parent::__construct($file);
			parent::read();
		}
		
		public function ToArray($firstLineKeys = false) {
			$this->firstLineKeys = $firstLineKeys;			
			$array = explode("\n", rtrim($this->contents));
			if($this->firstLineKeys) {
				$keys = str_getcsv($array[0], ",", "\"");
				array_shift($array);
			}
			if(count($array)) {
				foreach($array as $line) {
					$lineparts = str_getcsv($line, ",", "\"");	
					$this->firstLineKeys ? $array1[$lineparts[0]] = array_combine($keys, $lineparts) : $array1[$lineparts[0]] = $lineparts ;
				}
				return $array1;
			}
			else {
				!$this->hasContents;
			}
			return false;
		}		
	}

?>
