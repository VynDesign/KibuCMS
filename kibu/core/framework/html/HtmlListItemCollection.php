<?php

/**
 * Description of HtmlListItemCollection
 *
 * @author vyn
 */

	//namespace kibu\core\framework\html {

		require_once './kibu/core/framework/html/HtmlTagCollection.php';
		
		class HtmlListItemCollection extends HtmlTagCollection {

			public function __construct($collection = array()) {
				parent::__construct("li", $collection);
			}
		}
	//}
?>
