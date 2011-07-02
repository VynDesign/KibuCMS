<?php
	/**
	 * A class designed to generate the content area of a web page
	 *
	 *
	 * @package Kibu
	 * @author Vyn Raskopf
	 * @copyright Kibu 2010
	 * @version 1.0.0.1
	 */

	require_once './kibu/core/class/Url.php';
	require_once './kibu/core/class/Form.php';
	require_once './kibu/core/class/Module.php';
	require_once './kibu/core/class/Authentication.php';

	class Content extends URL {

		public $_section;
		public $_filename;
		public $_mode;
		protected $_authLevel;
		protected $_contentRecord = array();
		protected $_contentType = array();
		protected $_contentAssets = array();
		protected $_assetParams = array();
		protected $_contentBody;
		private $_siteConfigID;
		private $_siteConfig;

		public $relPath;


		function __construct($siteConfig) {
			parent::__construct();
			$this->_siteConfig = $siteConfig;
			$this->_siteConfigID = $siteConfig['siteConfigID'];
			$this->_setContentRecord();
			$this->_setContentType();
			$this->_setContentAssets();
			$this->_getContentAssetParams();
			$this->setAuthLevel();
			$this->setRelPath();
			$this->bodyAssembler();
		}
		//


		private function _setContentRecord(){
			global $db;
			// query database for content record and associated database tables/columns
			$sql = "SELECT
								userRecords.username,
								contentRecords.*,
								navigationSections.sectionID,
								navigationSections.sectionNum,
								navigationSections.sectionFullName,
								navigationSections.sectionName,
								contentTypes.*
						FROM userRecords, contentRecords, navigationSections, contentTypes, siteConfig
						WHERE navigationSections.sectionID = contentRecords.sectionID
							AND userRecords.userID = contentRecords.authorID
							AND contentTypes.contentTypeID = contentRecords.contentTypeID
							AND contentRecords.siteConfigID = '".$this->_siteConfigID."'";
			// if the URL has no parameters it's the homepage, so get the content of the default section page for the default site section
			if(($this->_filename == null) && ($this->_section == null)) {
				$sql .= " AND contentRecords.sectionID = navigationSections.sectionID AND navigationSections.sectionNum = siteConfig.siteDefaultSectionNum AND navigationSections.landingPageRecordNum = contentRecords.contentRecordNum";
			}
			elseif(($this->_filename != null) && ($this->_section == null)) {
					$sql .= "AND navigationSections.sectionName = '".$this->_filename."' AND navigationSections.landingPageRecordNum = contentRecords.contentRecordNum";
			}
			else {
				$sql .= " AND contentRecords.titleClean = '" . $this->_filename . "' AND navigationSections.sectionName = '" . $this->_section . "'";
			}
			$query = $db->setQuery($sql);
			if($db->getNumRows() > '0') {
				$result = $db->getAssoc();
				foreach($result as $field => $value) {
					$content[$field] = $value;
				}
				$this->_contentRecord = $content;
				$this->_filename = $content['titleClean'];
				$this->_section = $content['sectionName'];
			}
		}
		//

		public function getContentRecord() {
			return $this->_contentRecord;
		}

		//
		private function _setContentType() {
			global $db;
			if($this->_mode == 'html' || $this->_mode == 'edit') {
							$contentRecordNum = $this->_contentRecord['contentRecordNum'];
							$query = "SELECT *
								FROM siteTemplates
								WHERE siteTemplates.templateID = '".$this->_contentRecord['siteTemplateID']."'";
			}
			else {
				$query = "SELECT siteTemplates.*
						FROM siteTemplates
							WHERE siteTemplates.templateLink = '" . $this->_mode . "'";
			}
			$query = $db->setQuery($query);
			$result = $db->getAssoc($query);
			foreach($result as $field => $value) {
				$this->_contentType[$field] = $value;
			}
		}
		//

		public function getContentType() {
			return $this->_contentType;
		}

		//
		private function _setContentAssets() {
			global $db;
			$query = "SELECT contentRecordAssets.*, contentAssetTypes.*, siteTemplates.templateLink, siteModules.moduleLink, siteModules.moduleNum, siteModules.moduleID, siteTemplateContentZones.*
						FROM siteTemplateContentZones, contentRecordAssets, ((contentAssetTypes LEFT JOIN siteTemplates ON contentAssetTypes.assetDisplayID = siteTemplates.templateID)
								LEFT JOIN siteModules on siteModules.moduleID = contentAssetTypes.assetModuleID)
						WHERE contentRecordAssets.contentRecordNum = '" . $this->_contentRecord['contentRecordNum'] . "'
							AND contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID
							AND siteTemplateContentZones.contentZoneID = contentRecordAssets.contentZoneID
							AND siteTemplateContentZones.contentZoneType = '2'
							AND siteTemplateContentZones.templateID = '".$this->_contentRecord['siteTemplateID']."'
						ORDER BY siteTemplateContentZones.assetZoneNum ASC, contentRecordAssets.assetOrderNum ASC";
			$result = $db->setQuery($query);
			while($assetsArray = $db->getAssoc($result)){
				$this->_contentAssets[$assetsArray['assetZoneNum']][$assetsArray['assetID']] = $assetsArray;
				$this->_contentAssets[$assetsArray['assetZoneNum']][$assetsArray['assetID']]['params'] = null;
			}
		}
		//

		public function getContentAssets(){
			return $this->_contentAssets;
		}

		private function _getContentAssetParams() {
				global $db;

				$query = "SELECT contentAssetTypeParams.assetParamName, contentRecordAssetParams.assetParamVal, contentRecordAssetParams.contentRecordAssetID
						FROM contentRecordAssetParams, contentAssetTypeParams, contentRecords, contentRecordAssets
						WHERE contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
								AND contentRecordAssets.contentRecordNum = '".$this->_contentRecord['contentRecordNum']."'
								AND contentAssetTypeParams.assetTypeParamID = contentRecordAssetParams.assetTypeParamID
								AND contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID";
				$db->setQuery($query);
				if($db->getNumRows() > 0) {
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

				private function bodyAssembler() {
						foreach($this->_contentAssets as $assetZone) {
								$assetBody = '';
								foreach($assetZone as $key) {
										$key['contentRecord'] = $this->_contentRecord;
										if($this->_mode == 'html') {
												$assetBody .= $this->_contentAssetOutput($key);
										}
										elseif($this->_mode == 'edit') {
												$assetBody .= $this->_contentAssetEditOutput($key);
										}
										else {
												$assetBody .= $this->_contentAssetAltOutput($key);
										}
								}
								if($this->_mode == 'html' || $this->_mode == 'edit') {
										$assetWrapper = "<div class=\"contentzone\">\n";
										$assetWrapper .= $assetBody;
										$assetWrapper .= "</div>\n";
								}
								else {
										$assetWrapper = $assetBody;
								}
								$this->_contentBody[$key['assetZoneNum']] = $assetWrapper; // output assetBody
						}
				}

				private function _contentAssetOutput($key){
						global $url;
								$assetBody = '';
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

						return $assetBody;

				}

				private function _contentAssetEditOutput($key) {
						global $url;
						$assetBody = '';
						if((!isset($_GET['contentID'])) || ($_GET['contentID'] != $key['assetID'])) {
								$assetBody .= "<a class=\"editAssetLink\" href=\"?mode=edit&amp;contentID=".$key['assetID']."#content".$key['assetID']."\">Edit \"".$key['assetName']."\"</a>";
						}
						if((isset($_GET['contentID'])) && ($_GET['contentID'] == $key['assetID'])) {
								$assetBody .= "<div class=\"contentblock ".$key['assetTypeNameClean']."\" id=\"content".$key['assetID']."\">\n";
								require_once './kibu/core/class/ContentAssets.php';
								$contentAssets = new ContentAssets($this->_contentRecord);

								$formBtns = new Template('./kibu/templates/');
										$vars = array(
												'nextStep' => 'savechanges',
												'submitButtonID' => 'submit',
												'submitButtonName' => 'submit',
												'submitButtonVal' => 'Save',
												'submitBtnExtra' =>	 null,
												'resetButtnID' => 'reset',
												'resetButtonName' => 'reset',
												'resetButtonVal' => 'Cancel Edit',
												'resetBtnExtra' => "onclick=\"location.href='".$url->_filename.".html';\""
											);
								$formSubmitTpl = "form_submit_2btn.tpl.php";

								$formBtns->set_vars($vars, true);

								$formTpl = new Template("./kibu/templates/");
								$formTpl->set_vars($key, true);
								$formTpl->set("ID", null);
								$formTpl->set("formBody", $contentAssets->outputFormBody());
								$formTpl->set("method", "post");
								$formTpl->set("action", $url->_filename.".html?".$_SERVER['QUERY_STRING']);
								$formTpl->set("class", "modal");
								$formTpl->set("name", "editmode");
								$formTpl->set("formExtra", null);
								$formTpl->set("msg", $contentAssets->getMsg());
								$formTpl->set('formSubmit', $formBtns->fetch($formSubmitTpl));
								$assetBody .= $formTpl->fetch("form_wrapper.tpl.php");
								$assetBody .= "</div>";
						}
						return $assetBody;

				}

				private function _contentAssetAltOutput($key) {
						global $url;
						$assetBody = '';
						if($key['isVisible'] == 'y' && $key['assetAltDisplayID'] != 0) {
								if($key['assetModuleID'] != 0) { // if there is a template assigned to this asset
										$moduleLink = $key['moduleLink'];
										$modulePath = "./kibu/modules/".$moduleLink.'/'; // build directory out of composite properties
										$moduletpl = new Template($modulePath); // instantiate new template object $module
										if($key['params'] != null) { // if $assetParams has a value
												foreach($key['params'] as $param) { // iterate through array
														$moduletpl->set($param['assetParamName'], $param['assetParamValue']); // assign each value to its name for use in $module template
												}
										}
										if(file_exists($modulePath.$moduleLink.'_class.php')) { // if there is a control file
												require_once $modulePath.$moduleLink.'_class.php'; // include control file
												$moduleClass = new $moduleLink($key, $key['params']);
										}
										if($key['assetDisplayID'] !=0) {
												$moduletpl->set_vars($key, true); // assign array of content asset values to $module template
												$moduletpl->set_vars($moduleClass->getTemplateVars(), true);
												if(method_exists($moduleClass, 'returnData')) {
														$moduletpl->set('assetBody', $moduleClass->returnData());
												}
												$assetBody .= $moduletpl->fetch($this->_getAltDisplayTemplate($key['assetAltDisplayID']).'.tpl.php'); // save template file to variable
										}
								}
								elseif($key['assetModuleID'] == 0) {
										$assetTpl = new Template('./kibu/templates/');
										$assetTpl->set('assetBody', $key['assetBody']);
										$assetBody .= $assetTpl->fetch($this->_getAltDisplayTemplate($key['assetAltDisplayID']).'.tpl.php');
								}
						}
						return $assetBody;
				}

				private function _getAltDisplayTemplate($assetAltDisplayID) {
						global $db;
						$query = "SELECT siteTemplates.templateLink
								FROM siteTemplates, contentAssetTypes
								WHERE contentAssetTypes.assetAltDisplayID = '".$assetAltDisplayID."'
										AND siteTemplates.templateID = contentAssetTypes.assetAltDisplayID";
						$db->setQuery($query);
						$result = $db->getAssoc();
						return $result['templateLink'];

				}

		public function getContentBody() {
				return $this->_contentBody;
		}

		public function getSectionName() {
			return $this->_section;
		}

		public function getContentRecordValue($key = null) {
			$contentRecord = $this->_contentRecord;
			if($key == null) {
				return $contentRecord;
			}
			elseif($key) {
				return $contentRecord[$key];
			}
		}

		public function getCurPage() {
			return $this->_filename.'.'.$this->_mode;
		}

		private function setAuthLevel() {
			$contentArray = $this->_contentRecord;
			if($this->_mode == 'edit') {
				$this->_authLevel = $contentArray['editorAuthLevel'];
			}
			else {
				$this->_authLevel = $contentArray['visitorAuthLevel'];
			}
		}

		public function getAuthLevel() {
			return $this->_authLevel;
		}

		public function setRelPath() {
			$this->relPath =  '/'.$this->_section.'/'.$this->_filename.'.'.$this->_mode;
		}

		// page title routine. keeps page title from appearing redundant by checking for pages that are the same name as sections they reside in
		function pageTitle($siteAddress, $siteTagLine/*$sectionFullName, $contentTitle, $isSiteDefault*/) {
			$pageTitle = $siteAddress;
			$contentArray = $this->_contentRecord;
			if($this->_contentRecord['sectionNum'] == $this->_siteConfig['siteDefaultSectionNum']) {
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

		public function getContentRecordNum() {
			return $this->_contentRecord['contentRecordNum'];
		}
	}

?>