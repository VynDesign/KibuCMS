<?php

/**
 * Description of FormInputSelectOptions
 * Processes an array into a collection of <option> objects for output in an html <select>
 * If $selectedValue is set and a match is found among $optionArray, 'selected="selected"' is applied to the appropriate <option>
 *
 * @author vyn
 */
	
	//namespace kibu\core\framework\html\form {
		
	
		require_once './kibu/core/framework/html/HtmlTagCollection.php';
		require_once './kibu/core/framework/html/form/FormInputSelectOption.php';
	
		class FormInputSelectOptionsCollection extends HtmlTagCollection {

			private $_selectedValue;

			public function __construct( $optionArray, $selectedValue = null ) {
				$this->type = "FormInputSelectOption";
				$this->family = "form";				
				parent::__construct("option", $optionArray);
				$this->_selectedValue = $selectedValue;
			}

			public function Add($value, $display, $selected = false) {
				if($selected) {
					$this->_selectedValue = $value;
				}
				parent::Add($value, $display);
			}

			public function SetSelected($value) {
				$this->_selectedValue = $value;
			}

			protected function _processChanges() {
				foreach($this->collection as $key => $value) {
					$this->collection[$key]->attributes->Add("value", $key);
					if($key == $this->_selectedValue) {
						$this->collection[$key]->attributes->Add("selected", "selected");
					}
				}
				parent::_processChanges();
			}
		}
	//}
?>
