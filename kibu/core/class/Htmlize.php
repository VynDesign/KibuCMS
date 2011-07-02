<?php

	class Htmlize {
		
		protected $_incomingData;
		protected $_workingData;
		protected $_outgoingData;
		
		public function __construct($data) {
			$this->_incomingData = $data;
		}
		
		public function listify($parentClass = null, $childClass = null, $listType = null, $listMarker = null) {
			if(is_array($this->_incomingData)) {
				
			}
		}
		
		public function linkify($class = null, $target = null) {
			if(is_array($this->_incomingData)) {
				
			}
		}
		
		public function getOutput() {
			return $this->_outgoingData;
		}
		
	}
?>