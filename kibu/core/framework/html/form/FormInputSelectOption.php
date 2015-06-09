<?php


/**
 * Description of FormInputSelectOption
 * 
 *
 * @author vyn
 */

	//namespace kibu\core\framework\html\form {
		
		require_once './kibu/core/framework/html/HtmlTag.php';		
		
		class FormInputSelectOption extends HtmlTag {

			private $_optionValue;
			private $_selected;

			public function __construct( $optionValue, $optionDisplay, $selected = false ) {
				parent::__construct("option");
				$this->_optionValue = $optionValue;
				$this->text = $optionDisplay;
				$this->_selected = $selected;

				$this->attributes->Add("value", $optionValue);			

				if($this->_selected) {
					$this->attributes->Add("selected", "selected");
				}
			}
		}
	//}
?>
