<?php


	require_once './kibu/core/framework/template/Template.php';
	require_once './kibu/core/Content/ContentAsset.php';

	/**
	 * Description of ContentZone
	 *
	 * @author vyn
	 */
	class ContentZone {

		private $_contentZoneAssets = ""; 
		private $_assetZoneNameClean;
		private $_assetZone;
		private $_contentZoneOutput;


		public function GetContentZone() {
			return $this->_contentZoneOutput;
		}


		public function __construct($assetZoneNameClean, $assetZoneData) {
			$this->_assetZoneData = $assetZoneData;
			$this->_assetZoneNameClean = $assetZoneNameClean;
			$this->_processAssets();
			$this->_applyWrapperTpl();
		}

		private function _processAssets() {
			foreach($this->_assetZoneData as $asset) {
				$contentAsset = new ContentAsset($asset);
				$this->_contentZoneAssets .= $contentAsset->GetAssetOutput();
			}

		}

		private function _applyWrapperTpl() {
			$wrapper = new Template('./kibu/core/Content/templates/');
			$wrapper->set('assetZoneNameClean', $this->_assetZoneNameClean);
			$wrapper->set('contentZoneAsset', $this->_contentZoneAssets);
			$this->_contentZoneOutput = $wrapper->fetch('content_zone_wrapper.tpl.php');
		}
	}
?>
