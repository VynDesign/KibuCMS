<?php

/**
 * Description of HtmlAnchorLinkCollection
 *
 * @author vyn
 */
	//namespace kibu\core\framework\html {
	
		require_once './kibu/core/framework/html/HtmlTagCollection.php';
		require_once './kibu/core/framework/html/HtmlAnchorLink.php';
		
		class HtmlAnchorLinkCollection extends HtmlTagCollection {

			public function __construct($items = array(), $attributes = array()) { 
				$this->type = "HtmlAnchorLink";
				parent::__construct("a", $items, $attributes);
			}

			public function SetSelected($value) {
				$this->_selectedValue = $value;
			}

			public function Add($key, $value = null, $attributes = array()) {
				if(is_a($key, $this->type)) {
					$this->collection[$key->url] = $key;
				}
				else {
					parent::Add($key, $value, $attributes);
				}
			}
			
			protected function _processLinks() {
				foreach($this->collection as $key => $value) {
					$this->collection[$key]->attributes->Add("href", $key);
				}
			}

			protected function _processChanges() {
				$this->_processLinks();
				parent::_processChanges();
			}
		}
	//}
?>
