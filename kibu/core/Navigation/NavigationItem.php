<?php

/**
 * Description of NavigationItem
 *
 * @author vyn
 */

	require_once './kibu/core/framework/url/Url.php';
	require_once './kibu/core/framework/html/HtmlAnchorLink.php';

	class NavigationItem extends HtmlAnchorLink {
	
		protected $contentRecordNum;
		protected $sectionID;
		protected $contentTitle;
		protected $titleClean;
		protected $link;
		protected $orderNum;
		protected $isSectionDefault;
		protected $visible = true;
		protected $selected = false;
		
		public function __construct() {
		
		}
	}

?>
