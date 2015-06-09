<?php

/**
 * Description of HtmlTagCollection
 *
 * @author vyn
 */
	
	//namespace kibu\core\framework\html {
		
		class HtmlTagCollection {

			protected $tag;
			protected $family;
			protected $type = "HtmlTag";
			protected $markup;
			protected $collection;
			protected $itemClass;
			protected $itemAttributes;

			public function __construct($tag, $items = array(), $attributes = array()) {
				$this->tag = $tag;
				
				if(isset($this->family)) {
					require_once "./kibu/core/framework/html/".$this->family."/".$this->type.".php";
				}
				else {
					require_once "./kibu/core/framework/html/".$this->type.".php";
				}
				
				if(count($attributes)) { $this->itemAttributes = $attributes; }
				if(count($items)) { $this->Fill($items); }
			}

			public function Fill($items = array(), $attributes = array()) {
				if(count($attributes)) {
					$this->itemAttributes = $attributes;
				}
				foreach($items as $key => $value) {
					$this->Add($key, $value);
				}
			}

			public function Add($key, $value = null, $attributes = array()) {
				if(count($attributes)) {
					$this->itemAttributes = $attributes;
				}
				if(is_a($key, $this->type)) {
					$this->collection[] = $key;
				}
				elseif(count($this->collection && (!key_exists($key, $this->collection)))) {
					$this->collection[$key] = new $this->type($this->tag, $value, $attributes);
				}
			}

			public function SetItemClass($class) {
				$this->itemClass = $class;
			}	

			public function SetItemAttributes($attributes = array()) {
				$this->itemAttributes = $attributes;
			}

			public function Finalize() {
				$this->_processChanges();
			}

			public function GetMarkup() {
				$this->_processChanges();
				foreach($this->collection as $item) {
					$this->markup .= $item->GetMarkup();
				}
				return $this->markup;
			}
			
			public function Retrieve($key = null) {
				if($key != null)
				{
					return $this->collection[$key];
				}
				else {
					return $this->collection;
				}
			}			

			public function __get($key) {
				return $this->collection[$key];
			}

			protected function _processChanges() {
				foreach($this->collection as $key => $value) {
					if(count($this->itemAttributes)) {
						$this->collection[$key]->attributes->Fill($this->itemAttributes);
					}
					$this->collection[$key]->attributes->Prepend("class", $this->itemClass);
				}
			}
		}
	//}
?>
