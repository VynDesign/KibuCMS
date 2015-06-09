<?php

/**
 * Description of PageSettings
 *
 * @author vyn
 */

	require_once './kibu/core/System/Utility.php';
	require_once './kibu/core/SystemManagement/SystemManagement.php';

	class PageSettings extends SystemManagement {

		protected $_publishingLayouts;
		protected $_layoutAssets;
		protected $_contentRecordNum;
		protected $_curDate;
		protected $_curTime;
		protected $_siteTemplates;
		protected $_siteSections;
		protected $_userLevels;
		protected $_visitorAuthOptions;
		protected $_editorAuthOptions;
		protected $_isVisibleOptions;
		protected $_publishingLayoutOptions;
		protected $_templateMasterDefault;

		public function  __construct() {
			parent::__construct();
			$this->_setProps();
		}

		private function _setProps() {
			$this->_curDate = date('Y-m-d');
			$this->_curTime = date('H:i:s');

			$this->_required = array("contentTitle", "isVisible", "sectionID", "visitorAuthLevel", "editorAuthLevel");
			
			$this->_formTpl = "page_settings.tpl.php";
			
			$this->_templateMasterDefault = $this->_url->siteConfig['templateMasterDefault'];
		}
		
		protected function _setFormData() {
			
			if(count($this->_submit)) {
				$this->_formData = $this->_submit;
			}
			elseif($this->_formData == null) {
				
				$this->_formData['isVisible'] = null;
				$this->_formData['visitorAuthLevel'] = null;
				$this->_formData['editorAuthLevel'] = null;
				$this->_formData['publishingLayoutID'] = null;
				$this->_formData['siteTemplateID'] = null;
				$this->_formData['sectionID'] = null;
			}
			
			$this->_getSections();
			$this->_getpublishingLayouts();	
			$this->_getSiteTemplates();
			$this->_getUserLevels();
			$this->_getIsVisible();
			$this->_formData['isVisibleOptions'] = $this->_isVisibleOptions;
			$this->_formData['visitorAuthOptions'] = $this->_visitorAuthOptions;			
			$this->_formData['editorAuthOptions'] = $this->_editorAuthOptions;
			$this->_formData['sections'] = $this->_siteSections;
			$this->_formData['siteTemplates'] = $this->_siteTemplates;
			$this->_formData['publishingLayouts'] = $this->_publishingLayoutOptions;
		}
//
//		protected function _checkReq() {
//			if($this->_submit['contentTitle'] == null | $this->_submit['isVisible'] == null | $this->_submit['sectionID'] == null | $this->_submit['visitorAuthLevel'] == null | $this->_submit['editorAuthLevel'] == null){
//				$this->_msg .= "The following required fields were not filled out:\n<ul class=\"message\">";
//				if($this->_submit['contentTitle'] == null) {
//					$this->_msg .= "<li>Content Title</li>\n";
//				}
//				if($this->_submit['isVisible'] == null) {
//					$this->_msg .= "<li>Visibility</li>\n";
//				}
//				if($this->_submit['sectionID'] == null) {
//					$this->_msg .= "<li>Site Section</li>\n";
//				}
//				if($this->_submit['visitorAuthLevel'] == null) {
//					$this->_msg .= "<li>Viewer Authorization</li>\n";
//				}
//				if($this->_submit['editorAuthLevel'] == null){
//					$this->_msg .= "<li>Editor Authorization</li>\n";
//				}
//				$this->_msg .="</ul>\n";
//				
//				$this->_error = true;
//				return false;
//			}
//			else {
//				return true;
//			}
//		}

		protected function checkTitle($titleClean) {
			$sectionID = $this->_submit['sectionID'];
			$query = "SELECT contentID FROM contentRecords WHERE titleClean = '$titleClean' AND sectionID = '$sectionID'";
			$query = $this->_db->setQuery($query);
			$numrows = $this->_db->getNumRows($query);
			if($numrows == '0') {
				return true;
			}
			elseif($numrows > '0') {
				$this->_error = true;
				$this->_msg .= "There is already a page by the name \"".$titleClean."\" in that section. Please review the form below and make any necessary adjustments.";
				return false;
			}
		}
		
		protected function checkSectionPages() {
			$query = "SELECT COUNT(contentRecordNum) as pageCount FROM contentRecords WHERE sectionID = '".$this->_submit['sectionID']."'";
			$this->_db->setQuery($query);
			$result = $this->_db->getAssoc();
			if($result['pageCount'] > 0) {
				$pageOrderNum = $result['pageCount'] + 1;
			}
			else {
				$pageOrderNum = 1;
				$setDefaultLanding = "UPDATE navigationSections
					SET landingPageRecordNum = '".$this->_submit['contentRecordNum']."'
						WHERE sectionID = '".$this->_submit['sectionID']."'";
				$this->_db->setQuery($setDefaultLanding);
			}
			return $pageOrderNum;
		}			

		private function _getSections() {
			$siteSections = Utility::getSections();
			foreach($siteSections as $section) {
				$sectionOptions[$section['sectionID']] = $section['sectionFullName'];
			}
			$options = new FormInputSelectOptionsCollection($sectionOptions, $this->_formData['sectionID']);
			$this->_siteSections = $options->GetMarkup();
		}
		
		
		protected function _getSiteTemplates() {
			$query = "SELECT siteTemplates.templateID, siteTemplates.templateName FROM siteTemplates, siteTemplateTypes
					WHERE siteTemplateTypes.templateTypeName = 'Page'
						AND siteTemplates.templateTypeID = siteTemplateTypes.templateTypeID
					ORDER BY templateID ASC";

			$this->_db->setQuery($query);
			while($result = $this->_db->getAssoc()){
				$siteTemplates[$result['templateID']] = $result['templateName'];
			}
			$options = new FormInputSelectOptionsCollection($siteTemplates, $this->_formData['siteTemplateID']);
			$this->_siteTemplates = $options->GetMarkup();
		}
		

		protected function _getpublishingLayouts() {
			$query = "SELECT layoutID, layoutName FROM publishingLayouts ORDER BY layoutID";
			$this->_db->setQuery($query);
			while($result = $this->_db->getAssoc()) {
				$this->_publishingLayouts[$result['layoutID']] = $result['layoutName'];
			}
			
			$publishingLayoutOptions = new FormInputSelectOptionsCollection($this->_publishingLayouts, $this->_formData['publishingLayoutID']);
			$this->_publishingLayoutOptions = $publishingLayoutOptions->GetMarkup();
		}
		
		
		protected function _getUserLevels() {
			$this->_userLevels = Utility::getUserLevels();
			
			$visitorAuthOptions = new FormInputSelectOptionsCollection($this->_userLevels, $this->_formData['visitorAuthLevel']);
			$this->_visitorAuthOptions = $visitorAuthOptions->GetMarkup();
			
			$editorAuthOptions = new FormInputSelectOptionsCollection($this->_userLevels, $this->_formData['editorAuthLevel']);
			$this->_editorAuthOptions = $editorAuthOptions->GetMarkup();						
		}
		
		protected function _getIsVisible() {
			$isVis['vis'] = "Visible";
			$isVis['invis'] = "Invisible";
			$isVis['inac'] = "Inaccessible";
			
			$options = new FormInputSelectOptionsCollection($isVis, $this->_formData['isVisible']);
			$this->_isVisibleOptions = $options->GetMarkup();
		}
	}


	class PageSettings_CreatePage extends PageSettings {
		
		
		public function __construct() {
			
			$this->_permAbility = "Create Page";			
			
			parent::__construct();
			
			$this->_nextStep = 'save';
			
			if(count($this->_submit) && ($this->checkRequired())) {
				if($this->_createPage()) {
					$this->_nextStep = 'finish';
				}
			}
			else {
				$this->_nextStep = 'save';
				$this->_setFormData();
			}	
			
			$this->_setFormData();
			parent::_setFormBody();
		}
		
		protected function _setFormData() {
			parent::_setFormData();
			if(!count($this->_submit)) {
				$this->_formData['contentTitle'] = Null;
				$this->_formData['titleClean'] = Null;
				$this->_formData['metaKeywds'] = Null;
				$this->_formData['metaDesc'] = Null;
				$this->_formData['isVisible'] = Null;
				$this->_formData['sectionID'] = Null;
				$this->_formData['visitorAuthLevel'] = Null;
				$this->_formData['editorAuthLevel'] = Null;
				$this->_formData['message'] = Null;
				$this->_formData['siteTemplateID'] = $this->_templateMasterDefault;
				$this->_formData['contentRecordNum'] = Utility::guidGen();
			}	
			
		}	
		
		private function _createPage() {
			$contentTitle = htmlentities($this->_submit['contentTitle']);
			if($this->_submit['titleClean'] == Null) {
				$titleClean = Utility::stripChars($this->_submit['contentTitle']);
			}
			else {
				$titleClean = Utility::stripChars($this->_submit['titleClean']);
			}
			$metaKeywds = $this->_submit['metaKeywds'];
		    $metaDesc = $this->_submit['metaDesc'];
		    $isVisible = $this->_submit['isVisible'];
		    $sectionID = $this->_submit['sectionID'];
		    $visitorAuthLevel = $this->_submit['visitorAuthLevel'];
		    $editorAuthLevel = $this->_submit['editorAuthLevel'];
			$publishingLayoutID = $this->_submit['publishingLayoutID'];
			$siteTemplateID = $this->_submit['siteTemplateID'];
			$contentRecordNum = $this->_submit['contentRecordNum'];			
			
			if($this->checkTitle($titleClean)) {
				$orderNum = $this->checkSectionPages();
				$table = "contentRecords";
				
				$data = array(
					'siteConfigID' => $this->_url->siteConfig['siteConfigID'],
					'contentTitle' =>  $contentTitle,
					'titleClean' => $titleClean,
					'ownerID' => $this->_auth->getUserID(),
					'authorID' => $this->_auth->getUserID(),
					'submitDate' => $this->_curDate,
					'submitTime' => $this->_curTime,
					'metaKeywds' => $metaKeywds,
					'metaDesc' => $metaDesc,
					'isVisible' => $isVisible,
					'sectionID' => $sectionID,
					'visitorAuthLevel' => $visitorAuthLevel,
					'editorAuthLevel' => $editorAuthLevel,
					'contentRecordNum' => $contentRecordNum,
					'orderNum' => $orderNum,
					'publishingLayoutID' => $publishingLayoutID,
					'siteTemplateID' => $siteTemplateID
				);
				
				$this->_db->insert($table, $data);
				if($this->_db->getAffectedRows() > 0) {
					if($publishingLayoutID > 0) {
						$this->_publishLayoutAssets($publishingLayoutID);
					}
					$this->_msg = "Your new page has been created.";
					return true;
				}
				else {
					$this->_error = true;
					$this->_msg = "There was a problem creating page: ".$this->_db->getError()."";
					return false;
				}
			}
		}
		
		private function _publishLayoutAssets($layoutID) {
			$query = "SELECT *
				FROM publishingLayoutsAssets, contentAssetTypes
					WHERE publishingLayoutsAssets.layoutID = '".$layoutID."'
						AND publishingLayoutsAssets.assetTypeID = contentAssetTypes.assetTypeID
					ORDER BY publishingLayoutsAssets.assetOrderNum";
			$this->_db->setQuery($query);
			
			if($this->_db->getNumRows() > 0) {
				
				while($assetsArray = $this->_db->getAssoc()){
					$assets[$assetsArray['assetTypeName']] = $assetsArray;
				}		
			
				$this->_layoutAssets = $assets;	
				
				foreach($this->_layoutAssets as $asset) {
					$this->_addAsset($asset);
				}				
			}
		}

		private function _addAsset($asset) {
			$assetCreateDate = $this->_curDate;
			$assetCreateTime = $this->_curTime;
			
			$table = "contentRecordAssets";
			
			$data = array(
				'assetTypeID' => $asset['assetTypeID'],
				'assetName' => $asset['assetName'],
				'contentRecordNum' => $this->_submit['contentRecordNum'],
				'assetCreateDate' => $assetCreateDate,
				'assetCreateTime' => $assetCreateTime,
				'assetOrderNum' => $asset['assetOrderNum'],
				'contentZoneID' => $asset['contentZoneID']
			);
				
			$this->_db->insert($table, $data);
			
			if(!$this->_db->error) {
				require_once 'AssetManagement.php';
				$contentAssets = new AssetManagement();

				$assetTypeID = $asset['assetTypeID'];
				
				$query = "SELECT assetID
					FROM contentRecordAssets
						WHERE assetCreateDate = '".$assetCreateDate."'
							AND assetCreateTime = '".$assetCreateTime."'
							AND assetTypeID = '".$assetTypeID."'";

				$this->_db->setQuery($query);
				$result = $this->_db->getAssoc();
				$contentAssets->_addAssetParams($result['assetID'], $assetTypeID);
			}
		}
	}	
	

	
	
	class PageSettings_ModifyPage extends PageSettings {
		
		private $_originalTitleClean;
		
		public function __construct() {
			$this->_permAbility = "Modify Page";
			
			parent::__construct();
			
			$this->_contentRecordNum = $_GET['recordNum'];
			$this->_nextStep = 'save';
						
			$this->_setSectionInfo();
			
			if((count($this->_submit)) && $this->checkRequired()) {
				$this->_savePageSettings();
			}			
			
			$this->_setFormData();
			parent::_setFormBody();
		}
		
		
		protected function _setFormData() {
			if(count($this->_submit)) {
				$this->_formData = $this->_submit;
			}
			parent::_setFormData();
			$this->_formData['publishingLayoutID'] == 0;
		}
		
		private function _setSectionInfo() {
			$query = "SELECT * FROM contentRecords WHERE contentRecordNum = '".$this->_contentRecordNum."'";
			$this->_db->setQuery($query);
			$this->_formData = $this->_db->getAssoc();
			$this->_originalTitleClean = $this->_formData['titleClean'];
		}
		
		private function _savePageSettings() {
			$contentRecordNum = $this->_submit['contentRecordNum'];
			$contentTitle = htmlentities($this->_submit['contentTitle']);
			
			if($this->_submit['titleClean'] == Null) {
				$titleClean = Utility::stripChars($this->_submit['contentTitle']);
			}
			else {
				$titleClean = Utility::stripChars($this->_submit['titleClean']);
			}
			
			$metaKeywds = $this->_submit['metaKeywds'];
			$metaDesc = $this->_submit['metaDesc'];
			$isVisible = $this->_submit['isVisible'];
			$sectionID = $this->_submit['sectionID'];
			$visitorAuthLevel = $this->_submit['visitorAuthLevel'];
			$editorAuthLevel = $this->_submit['editorAuthLevel'];
			$siteTemplateID = $this->_submit['siteTemplateID'];

			if($titleClean == $this->_originalTitleClean || $this->checkTitle($titleClean)) {				
				$table = "contentRecords";
				$data = array(
					'contentTitle' => $contentTitle,
					'titleClean' => $titleClean,
					'metaKeywds' => $metaKeywds,
					'metaDesc' => $metaDesc,
					'isVisible' => $isVisible,
					'sectionID' => $sectionID,
					'visitorAuthLevel' => $visitorAuthLevel,
					'editorAuthLevel' => $editorAuthLevel,
					'siteTemplateID' => $siteTemplateID
				);
				$where = "contentRecordNum='$contentRecordNum'";

				$this->_db->update($table, $data, $where);
				if($this->_db->getAffectedRows() > 0) {
					$this->_msg = "Page settings saved successfully.";
				}
				else {
					$this->_msg = "There was a problem saving page settings: ".$this->_db->getError()."";
				}
			}
		}
	}

?>
