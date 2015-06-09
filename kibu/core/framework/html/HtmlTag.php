<?php

/**
 * Description of HtmlTag
 *
 * @author vyn
 */

	//namespace kibu\core\framework\html {
	
		require_once './kibu/core/framework/template/Template.php';
		require_once './kibu/core/framework/html/HtmlTagAttributes.php';
	
		class HtmlTag {

			protected $tag;
			protected $text;
			protected $markup;
			protected $template;

			public $attributes;

			public function __construct($tag, $text = null, $attributes = array()) {
				$this->tag = $tag;
				$this->text = $text;
				$this->attributes = new HtmlTagAttributes();
				if(count($attributes)) {
					foreach($attributes as $key => $value) {
						$this->attributes->Add($key, $value);
					}
				}
			}

			public function __get($name) {
				return $this->$name;
			}
			
			public function SetText($text) {
				$this->text = $text;
			}

			public function AppendText($text) {
				$this->text .= $text;
			}

			public function SetID($id) {
				$this->attributes->Add("id", $id);
			}

			public function SetClass($class) {
				$this->attributes->Add("class", $class);
			}

			public function AppendClass($class) {
				$this->attributes->Append("class", $class);
			}
			
			private function _setTemplate() {
				if($this->text == null) {
					$this->template = "html_tag_generic_self_closing.tpl.php";
				}
				else {
					$this->template = "html_tag_generic.tpl.php";
				}
			}

			public function GetMarkup() {
				$this->_applyTemplate();
				return $this->markup;

			}
			
			
			private function _applyTemplate() {
				$this->_setTemplate();
				$tpl = new Template('./kibu/core/framework/html/templates/');
				$tpl->set("tag", $this->tag);
				$tpl->set("attributes", $this->attributes);
				if($this->text != null) {
					$tpl->set("text", $this->text);
				}
				$this->markup = $tpl->fetch($this->template);
			}			
		}
	//}
?>
