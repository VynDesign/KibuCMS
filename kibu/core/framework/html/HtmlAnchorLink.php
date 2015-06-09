<?php

/**
 * Description of HtmlAnchorLink
 *
 * @author vyn
 */	
	//namespace kibu\core\framework\html {

		require_once './kibu/core/framework/html/HtmlTag.php';
		
		class HtmlAnchorLink extends HtmlTag {

			protected $url;


			public function __construct($tag, $text, $attributes = array()) {
				parent::__construct($tag, $text, $attributes);
				if(key_exists("href", $attributes)) {
					$this->url = $attributes["href"];
				}
			}

			public function SetURL($url) {
				$this->url = $url;
				$this->attributes->href = $this->url;
			}

			public function SetTarget($val)
			{
				$this->attributes->target = $val;
			}
		}
	//}
?>
