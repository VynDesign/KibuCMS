<?php

/**
 * Description of FormInputSelect
 * Generates an HTML form <select> input when fed a $name, $id, array of $options and, optionally, a $selectedOption
 *
 * @author vyn
 */

	//namespace kibu\core\framework\html\form {
			
		require_once './kibu/core/framework/html/HtmlTag.php';
		require_once './kibu/core/framework/html/form/FormInputSelectOptionsCollection.php';
	
		class FormInputSelect extends HtmlTag {
		
			protected $name;
			protected $id;
			protected $options;
			protected $selectedOption;

			public $optionsCollection;

			public function __construct($name, $id, $options = array(), $selectedOption = null) {
				parent::__construct("select");

				$this->name = $name;
				$this->id = $id;
				$this->options = $options;
				$this->selectedOption = $selectedOption;

				$this->attributes->Add("name", $name);
				$this->attributes->Add("id", $id);
				$this->optionsCollection = new FormInputSelectOptionsCollection($this->options, $this->selectedOption);			
			}

			public function GetMarkup() {
				$this->text = $this->optionsCollection->GetMarkup();			
				return parent::GetMarkup();
			}
		}
	//}
?>
