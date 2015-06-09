<?php


	class ArrayExt extends ArrayObject {

		private $_newArray;
		
		public function returnNew() {
			return $this->_newArray;
		}
		
		private $_originalArray;
		
		public function returnOriginal() {
			return $this->_originalArray;
		}
		
		
		public function __construct($originalArray = null) {
			parent::__construct($originalArray);
			$this->_originalArray = $originalArray;
		}
		
		
		/**
		 *
		 * Rearranges a multi-dimensional _originalArray to use a 
		 * value from the child array as its key. 
		 * 
		 * @param string $newKey 
		 */
		public function reorganizeByKey($newKey) {
			foreach($this->_originalArray as $array) {
				$this->_newArray[$array[$newKey]] = $array;
			}
		}
		
		public function filterBy($expression) {
			
		}
		
				
		/**
		 *
		 * Iterates through an array and ties it together into a string
		 * using the $delimiter character supplied between each value.
		 * 
		 * @param string $delimiter: The characer or string to place between values
		 * @param bool $usekeys: Switches the method to use the array Key instead of the Value in the output string
		 * @return string 
		 * 
		 */
		public function concatArrayToString($delimiter = null, $removeLastDelimiter = true, $usekeys = false) {
			$string = "";
			$delimiter = (is_null($delimiter)) ? '; ' : $delimiter;
			foreach($this->_originalArray as $key => $value) {
				if($usekeys) {
					$string .= $key . $delimiter;
				}
				else {
					$string .= $value . $delimiter;
				}
			}
			if($removeLastDelimiter) {
				$dLen = strlen($delimiter);
				$string = substr($string, 0, -$dLen);
			}
			return $string;
		}
		
	}

?>
