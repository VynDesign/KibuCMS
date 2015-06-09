<?php

	require_once './kibu/core/Content/Content.php';
	require_once './kibu/core/Content/ContentZone.php';
		
	class Page extends Content {

		private $_pageOutput; // Holds an array of ContentZone objects
		private $_pageTemplate;
		private $_localContent = array();
		private $_globalContent = array();


		public function GetPageOutput() {
			return $this->_pageOutput;
		}


		public function __construct() {
			parent::__construct();
			$this->_processLocalZones();
			$this->_processGlobalZones();
			$this->_setPageTemplate();
			$this->_applyWrapperTpl();
		}

		private function _processLocalZones() {
			foreach($this->contentAssets['localContent'] as $key => $assetArray) {
				$contentZone = new ContentZone($key, $assetArray);
				$this->_localContent[$key] = $contentZone->GetContentZone();
			}
		}

		private function _processGlobalZones() {
			if(isset($this->contentAssets['globalContent']) && (count($this->contentAssets['globalContent']) > 0)) {
				foreach($this->contentAssets['globalContent'] as $key => $assetArray) {
					$contentZone = new ContentZone($key, $assetArray);
					$this->_globalContent[$key] = $contentZone->GetContentZone();				
				}
			}
		}

		private function _setPageTemplate() {
			if($this->contentProperties['templateLink'] != null) {
				$this->_pageTemplate = $this->contentProperties['templateLink'];
			}
			else {
				$this->_pageTemplate = "content_page_wrapper";
			}

		}

		private function _applyWrapperTpl() {
			$wrapper = new Template('./kibu/core/Content/templates/');
			$wrapper->set('localContent', $this->_localContent);
			$wrapper->set('globalContent', $this->_globalContent);
			$this->_pageOutput = $wrapper->fetch($this->_pageTemplate.".tpl.php");
		}
	}

?>
