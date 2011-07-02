<?php

	require_once './kibu/core/class/Module.php';

class GlobalContent {

		private $_db;
		private $_contentProperties;
		private $_contentAssets;
		private $_contentBody;

		public function  __construct($contentProperties) {
				global $db;
				$this->_db = $db;
				$this->_contentProperties = $contentProperties;
				$this->_setGlobalContent();
				$this->_outputGlobalContent();
		}

		public function getGlobalAssets() {
				return $this->_contentBody;
		}

		private function _setGlobalContent() {
				$query = "SELECT contentRecordAssets.*, contentAssetTypes.*, siteTemplates.*, siteModules.*, siteTemplateContentZones.*
						FROM siteTemplateContentZones, siteTemplateZoneTypes, contentRecordAssets, contentAssetTypes
										LEFT JOIN siteModules 
												ON siteModules.moduleID = contentAssetTypes.assetModuleID
										LEFT JOIN siteTemplates
												ON siteTemplates.templateID = contentAssetTypes.assetDisplayID
										LEFT JOIN contentAssetTypeParams
												ON contentAssetTypeParams.siteModuleID = siteModules.moduleID
										LEFT JOIN contentRecordAssetParams
												ON contentRecordAssetParams.assetTypeParamID = contentAssetTypeParams.assetTypeParamID
						WHERE siteTemplateContentZones.templateID = '".$this->_contentProperties['siteTemplateID']."'
								AND contentRecordAssets.contentZoneID = siteTemplateContentZones.contentZoneID
								AND contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID
								AND contentRecordAssets.isVisible = 'y'
								AND siteTemplateZoneTypes.zoneTypeName = 'Site'
								AND siteTemplateContentZones.contentZoneType = siteTemplateZoneTypes.zoneTypeID
								ORDER BY siteTemplateContentZones.assetZoneNum ASC, contentRecordAssets.assetOrderNum ASC";
				$this->_db->setQuery($query);
				while($result = $this->_db->getAssoc()) {
						$content[] = $result;
				}
				if(isset($content)) {
						foreach($content as $contentAsset) {
								$this->_contentAssets[$contentAsset['assetZoneNum']][$contentAsset['assetID']] = $contentAsset;
								$this->_getContentAssetParams($contentAsset['assetID']);
						}
				}
		}

		private function _getContentAssetParams($assetID) {
				global $db;

				$query = "SELECT contentAssetTypeParams.assetParamName, contentRecordAssetParams.assetParamVal, contentRecordAssetParams.contentRecordAssetID
						FROM contentRecordAssetParams, contentAssetTypeParams, contentRecordAssets
								WHERE	contentAssetTypeParams.assetTypeParamID = contentRecordAssetParams.assetTypeParamID
								AND contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
								AND contentRecordAssets.assetID = '".$assetID."'";
				$db->setQuery($query);

				if(($db->getNumRows() > 0) && (isset($this->_contentAssets))) {
						while($result = $db->getAssoc()){
								foreach($this->_contentAssets as $assetZoneNum) {
										foreach($assetZoneNum as $key => $value) {
												if($value['assetID'] == $result['contentRecordAssetID']) {
														$this->_contentAssets[$value['assetZoneNum']][$result['contentRecordAssetID']]['params'][$result['assetParamName']] = $result['assetParamVal'];
												}
										}
								}
						}
				}
		}

		private function _outputGlobalContent() {
				if(isset($this->_contentAssets)) {
						foreach($this->_contentAssets as $assetZone) {
								$assetBody = '';
								foreach($assetZone as $key) {
										if($key['isVisible'] == 'y') {
												$assetBody .= "<div class=\"contentblock ".$key['assetTypeNameClean']."\">\n";
												if($key['assetModuleID'] != 0) { // if there is a template assigned to this asset
														$bodymodule = new Module();
														$assetBody .= $bodymodule->loadModule($key, $key['params']);
												}
												elseif(($key['assetModuleID'] == 0) && ($key['assetDisplayID'] != 0)) {
														$assetTpl = new Template('./kibu/templates/');
														$assetTpl->set('assetBody', $key['assetBody']);
														$assetBody .= $assetTpl->fetch($key['templateLink'].'.tpl.php');
												}
												else {
														$assetBody .= $key['assetBody'];
												}
												$assetBody .= "</div>\n";
										}
										$assetWrapper = "<div class=\"contentzone\">\n";
										$assetWrapper .= $assetBody;
										$assetWrapper .= "</div>\n";

										$this->_contentBody[$key['assetZoneNum']] = $assetWrapper; // output assetBody
								}
						}
				}
		}
}


?>
