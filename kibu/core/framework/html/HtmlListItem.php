<?php

/**
 * Description of HtmlListItem
 *
 * @author vyn
 */

	//namespace kibu\core\framework\html {

		require_once './kibu/core/framework/html/HtmlTag.php';		
		
		class HtmlListItem extends HtmlTag {

			public function __construct($tag, $text) {
				parent::__construct("li", $text);
				$this->text = $text;
			}
		}
	//}
?>
