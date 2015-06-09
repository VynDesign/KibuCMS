<?php

	require_once './kibu/core/Navigation/Url.php';
	require_once './kibu/core/Authentication/Authentication.php';

	class Content extends URL_ext {

		// Class Variables 
		
		protected $_authLevel;
		protected $contentProperties = array();
		protected $contentAssets = array();
		protected $assetParams = array();
		protected $contentBody;
		
		private $_siteConfigID;
		private $_siteConfig;

		public $relPath;

		
		// Class Getters 
		
		public function getContentProperties() { return $this->contentProperties; }
		 
		public function getContentAssets() { return $this->contentAssets; }
		
		public function getContentBody() { return $this->contentBody; }

		public function getSectionName() { return $this->section; }

		public function getContentRecordValue($key = null) { return ($key == null) ? $this->contentProperties : $this->contentProperties[$key]; }

		public function getCurPage() { return $this->filename.'.'.$this->mode; }
		
		public function getContentRecordNum() { return $this->contentProperties['contentRecordNum']; }

		public function getAuthLevel() { return $this->_authLevel; }
		
		
		// Public Methods
		
		function __construct() {
			parent::__construct();		
			$this->_siteConfigID = $this->siteConfig['siteConfigID'];
			$this->_setContentProperties();
			$this->_setLocalContentAssets();
			$this->_setGlobalContentAssets();
			$this->_setAuthLevel();
			$this->_setRelPath();
		}
		

		// page title routine. keeps page title from appearing redundant by checking for pages that are the same name as sections they reside in
		public function pageTitle($siteAddress, $siteTagLine/*$sectionFullName, $contentTitle, $isSiteDefault*/) {
			$pageTitle = $siteAddress;
			$contentArray = $this->contentProperties;
			if($this->contentProperties['sectionNum'] == $this->_siteConfig['siteDefaultSectionNum']) {
				$pageTitle .= " - ". $siteTagLine;
			}
			elseif($contentArray['sectionFullName'] == $contentArray['contentTitle']) {
				$pageTitle .= " - ". $contentArray['sectionFullName'];
			}
			elseif(($contentArray['sectionFullName'] != $contentArray['contentTitle'])) {
				$pageTitle .= " - ". $contentArray['sectionFullName'] ." - ". $contentArray['contentTitle'];
			}
			return $pageTitle;
		}	
		//


		// Private Methods
		
		private function _setContentProperties(){
			// query database for content record and associated database tables/columns
			$sql = "SELECT
						userRecords.username,
						contentRecords.*,
						navigationSections.sectionID,
						navigationSections.sectionNum,
						navigationSections.sectionFullName,
						navigationSections.sectionName,
						siteTemplates.*,
						contentTypes.*
					FROM userRecords, contentRecords, navigationSections, contentTypes, siteTemplates, siteConfig
					WHERE navigationSections.sectionID = contentRecords.sectionID
						AND userRecords.userID = contentRecords.authorID
						AND contentTypes.contentTypeID = contentRecords.contentTypeID
						AND siteTemplates.templateID = contentRecords.siteTemplateID
						AND contentRecords.siteConfigID = '".$this->_siteConfigID."'";
			// if the URL has no parameters it's the homepage, so get the content of the default section page for the default site section
			if(($this->filename == null) && ($this->section == null)) {
				$sql .= " AND contentRecords.sectionID = navigationSections.sectionID AND navigationSections.sectionNum = siteConfig.siteDefaultSectionNum AND navigationSections.landingPageRecordNum = contentRecords.contentRecordNum";
			}
			elseif(($this->filename != null) && ($this->section == null)) {
					$sql .= "AND navigationSections.sectionName = '".$this->filename."' AND navigationSections.landingPageRecordNum = contentRecords.contentRecordNum";
			}
			else {
				$sql .= " AND contentRecords.titleClean = '" . $this->filename . "' AND navigationSections.sectionName = '" . $this->section . "'";
			}	
			$this->_db->setQuery($sql);
			if($this->_db->getNumRows() > 0) {
				$result = $this->_db->getAssoc();
				if($result['isVisible'] == 'inac') {
					header("Location: ". $this->urlDomain . "/");				
				}
				else {
					foreach($result as $field => $value) {
						$this->contentProperties[$field] = $value;
					}
					$this->filename = $this->contentProperties['titleClean'];
					$this->section = $this->contentProperties['sectionName'];
				}
			}
			else {
				header("Location: ". $this->urlDomain. "/");				
			}
		}

		private function _setLocalContentAssets() {
			$query = "SELECT siteTemplateContentZones.*, siteTemplateContentZonesMatchup.*, contentRecordAssets.*, contentAssetTypes.*, siteModules.moduleLink, siteModules.moduleNum, siteModules.moduleID, siteTemplates.templateLink
				FROM siteTemplateContentZonesMatchup, siteTemplateContentZones, contentRecordAssets,
						(
							(
								contentAssetTypes LEFT JOIN siteTemplates ON contentAssetTypes.assetDisplayID = siteTemplates.templateID
							)
							LEFT JOIN siteModules on siteModules.moduleID = contentAssetTypes.assetModuleID
						)
				WHERE siteTemplateContentZones.contentZoneGUID = siteTemplateContentZonesMatchup.contentZoneGUID
					AND contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID				
					AND contentRecordAssets.contentZoneID = siteTemplateContentZones.contentZoneID
					AND siteTemplateContentZones.contentZoneType = '2'
					AND contentRecordAssets.contentRecordNum = '".$this->contentProperties['contentRecordNum']."'
					AND siteTemplateContentZonesMatchup.templateID = '".$this->contentProperties['siteTemplateID']."'
				ORDER BY siteTemplateContentZonesMatchup.assetZoneNum ASC, contentRecordAssets.assetOrderNum ASC";
			
			$result = $this->_db->setQuery($query);
			while($assetsArray = $this->_db->getAssoc($result)){
				$this->contentAssets['localContent'][$assetsArray['assetZoneNameClean']][$assetsArray['assetID']] = $assetsArray;
				$this->contentAssets['localContent'][$assetsArray['assetZoneNameClean']][$assetsArray['assetID']]['params'] = null;
			}
		}
		
		private function _setGlobalContentAssets() {			
			$query = "SELECT siteTemplateContentZones.*, siteTemplateContentZonesMatchup.*, contentRecordAssets.*, contentAssetTypes.*, siteModules.moduleLink, siteModules.moduleNum, siteModules.moduleID, siteTemplates.templateLink
				FROM siteTemplateContentZonesMatchup, siteTemplateContentZones, contentRecordAssets,
					(
						(
							contentAssetTypes LEFT JOIN siteTemplates ON contentAssetTypes.assetDisplayID = siteTemplates.templateID
						)
						LEFT JOIN siteModules on siteModules.moduleID = contentAssetTypes.assetModuleID
					)
				WHERE siteTemplateContentZones.contentZoneGUID = siteTemplateContentZonesMatchup.contentZoneGUID
					AND contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID				
					AND contentRecordAssets.contentZoneID = siteTemplateContentZones.contentZoneID
					AND siteTemplateContentZones.contentZoneType = '1'
					AND siteTemplateContentZonesMatchup.templateID = '".$this->contentProperties['siteTemplateID']."'
				ORDER BY siteTemplateContentZonesMatchup.assetZoneNum ASC, contentRecordAssets.assetOrderNum ASC";
			
			$this->_db->setQuery($query);
			while($result = $this->_db->getAssoc()) {
				$content[] = $result;
			}
			if(isset($content)) {
				foreach($content as $contentAsset) {
					$this->contentAssets['globalContent'][$contentAsset['assetZoneNameClean']][$contentAsset['assetID']] = $contentAsset;
					//$this->_getGlobalContentAssetParams($contentAsset['assetID']);
				}
			}
		}

		
		//DEPRECATED - asset param gathering moved to Module.php 5/15/13
		
//		private function _setLocalContentAssetParams() {
//			$query = "SELECT siteModulesParams.siteModulesParamName, contentAssetTypeParams.defaultValue, contentRecordAssetParams.assetParamVal, contentRecordAssetParams.contentRecordAssetID
//						FROM siteModulesParams, contentRecordAssetParams, contentAssetTypeParams, contentRecords, contentRecordAssets
//						WHERE siteModulesParams.siteModulesParamID = contentAssetTypeParams.siteModulesParamID
//							AND siteModulesParams.siteModuleID = contentAssetTypeParams.siteModuleID
//							AND contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
//							AND contentRecordAssets.contentRecordNum = '".$this->contentProperties['contentRecordNum']."'
//							AND contentAssetTypeParams.assetTypeParamID = contentRecordAssetParams.assetTypeParamID
//							AND contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
//							AND contentRecords.contentRecordNum = contentRecordAssets.contentRecordNum";
//			
//			$query = "SELECT siteModulesParams.siteModulesParamName, contentRecordAssetParams.assetParamVal, contentRecordAssetParams.contentRecordAssetID
//						FROM siteModulesParams, contentRecordAssetParams, contentAssetTypes, contentRecords, contentRecordAssets
//						WHERE siteModulesParams.siteModulesParamID = contentAssetTypes.assetModuleID
//							AND contentAssetTypes.assetTypeID = contentRecordAssets.assetTypeID
//							AND siteModulesParams.siteModulesParamID = contentRecordAssetParams.assetTypeParamID
//							AND contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
//							AND contentRecordAssets.contentRecordNum = '".$this->contentProperties['contentRecordNum']."'
//							AND contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
//							AND contentRecords.contentRecordNum = contentRecordAssets.contentRecordNum";			
//			
//			
//			echo $query;
//			$this->_db->setQuery($query);
//			if($this->_db->getNumRows() > 0) {
//				while($result = $this->_db->getAssoc()){
//					foreach($this->contentAssets['localContent'] as $assetZoneNum) {
//						foreach($assetZoneNum as $key => $value) {
//							if($value['assetID'] == $result['contentRecordAssetID']) {
//								$this->contentAssets['localContent'][$value['assetZoneNameClean']][$result['contentRecordAssetID']]['params'][$result['siteModulesParamName']] = $result['assetParamVal'];
//							}
//						}
//					}
//				}
//			}
//		}
		
		
		
//		private function _getGlobalContentAssetParams($assetID) {
//			$query = "SELECT contentAssetTypeParams.assetParamName, contentRecordAssetParams.assetParamVal, contentRecordAssetParams.contentRecordAssetID
//					FROM contentRecordAssetParams, contentAssetTypeParams, contentRecordAssets
//						WHERE	contentAssetTypeParams.assetTypeParamID = contentRecordAssetParams.assetTypeParamID
//						AND contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
//						AND contentRecordAssets.assetID = '".$assetID."'";
//			$this->_db->setQuery($query);
//
//			if(($this->_db->getNumRows() > 0) && (isset($this->contentAssets))) {
//				while($result = $this->_db->getAssoc()){
//					foreach($this->contentAssets['globalContent'] as $assetZoneNum) {
//						foreach($assetZoneNum as $key => $value) {
//							if($value['assetID'] == $result['contentRecordAssetID']) {
//								$this->contentAssets['globalContent'][$value['assetZoneNameClean']][$result['contentRecordAssetID']]['params'][$result['assetParamName']] = $result['assetParamVal'];
//							}
//						}
//					}
//				}
//			}
//		}		

		private function _setRelPath() {
			$this->relPath =  '/'.$this->section.'/'.$this->filename.'.'.$this->mode;
		}		

		private function _setAuthLevel() {
			$this->_authLevel = $this->mode == 'edit' ? $this->contentProperties['editorAuthLevel'] : $this->contentProperties['visitorAuthLevel'];
		}
	}

?>