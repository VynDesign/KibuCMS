<?php

/**
 * Description of HtmlTagAttribute
 *
 * @author vyn
 */

	//namespace kibu\core\framework\html {

		class HtmlTagAttribute {

			protected $type;
			protected $value;

			public function __construct($type = null, $value = null) {
				$this->type = $type;
				$this->value = $value;
			}

			public function __get($name) {
				return $this->$type;
			}

			public function __set($name, $value) {
				$this->$type = $value;
			}

			public function __toString() {
				$tpl = new Template('./kibu/core/framework/html/templates/');
				$tpl->set("type", $this->type);
				$tpl->set("value", $this->value);
				return $tpl->fetch('html_tag_attribute.tpl.php');
				//return (string)" ".$this->type."=\"".$this->value."\"";
			}
		
			public function Prepend($value) {				
				$this->value = $value." ".$this->value;
			}

			public function Append($value) {	
				$this->value .= " ".$value;
			}
			
			public function Overwrite($value) {
				$this->value = $value;
			}
		}
	//}
?>
