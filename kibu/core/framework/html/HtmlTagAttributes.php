<?php

/**
 * Description of HtmlTagAttributes
 *
 * @author vyn
 */

	//namespace kibu\core\framework\html {
		
		require_once './kibu/core/framework/html/HtmlTagAttribute.php';
		
		class HtmlTagAttributes {

			protected $attribs = array();
			protected $allowedAttribs = array();
			protected $attribsString = null;
			
			public function __construct($attribs = array(), $allowedAttribs = array()) {
				if(count($allowedAttribs)) {
					$this->allowedAttribs = $allowedAttribs;
				}
				if(count($attribs)) {
					$this->Fill($attribs);
				}
			}

			public function Add($type, $value) {
				if($this->_checkAllowed($type)) {				
					$this->attribs[$type] = new HtmlTagAttribute($type, $value);
				}
			}

			public function Fill($attribs = array()) {
				foreach($attribs as $type => $value) {
					if($this->_checkAllowed($type)) {
						$this->attribs[$type] = new HtmlTagAttribute($type, $value);
					}
				}		
			}

			public function Prepend($type, $value) {
				if(array_key_exists($type, $this->attribs)) {
					$this->attribs[$type]->Prepend($value);
				}
				else {
					$this->Add($type, $value);
				}
			}

			public function Append($type, $value) {	
				if(array_key_exists($type, $this->attribs)) {
					$this->attribs[$type]->Append($value);
				}
				else {
					$this->Add($type, $value);
				}
			}
			
			public function Overwrite($type, $value) {
				if(array_key_exists($type, $this->attribs)) {
					$this->attribs[$type]->Overwrite($value);
				}				
			}

			public function __toString() {
				$this->attribsString = "";
				if(count($this->attribs)) {
					foreach($this->attribs as $attribute) {
						$this->attribsString .= (string)$attribute;
					}
				}
				return $this->attribsString;
			}
			
			private function _checkAllowed($type) {
				if(!count($this->allowedAttribs)) { return true; }
				elseif(array_key_exists($type, $this->allowedAttribs)) { return true; }
				return false;
			}
		}
	//}
?>
