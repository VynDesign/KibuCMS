<?php

		require_once './kibu/core/class/Template.php';
		require_once './kibu/core/class/Authentication.php';

class ContentAssets {

		private $_mode;
		private $_contentRecordNum = null;
		private $_auth;
		private $_db;
		private $_url;
		private $_form;
		private $_nextStep;
		private $_msg;
		private $_submit;
		private $_siteTemplates;
		private $_assetZones;
		private $_zoneAssets;
		private $_zoneOrderOptions;
		private $_contentAsset;
		private $_contentRecord;

		public function  __construct($contentRecord = null) {
				if($contentRecord != null) {
						$this->_contentRecord = $contentRecord;
				}

				$this->_setProps();
				$this->_checkAuth();
				if(isset($_POST['submit'])) {
						$this->_submit = $_POST;
						if(isset($this->_submit['orderOptions'])) {
								$this->_reorderAssets();
						}
						elseif(isset($this->_submit['assetID'])){
								if($this->_submit['nextStep'] == 'editasset') {
										$this->_nextStep = 'finish';
										$this->_editAssetForm();
								}
								elseif($this->_submit['nextStep'] == 'finish' || $this->_submit['nextStep'] == 'savechanges') {
										$this->_saveAssetChanges();
										if($this->_submit['nextStep'] == 'savechanges'){
												$this->_submit['assetID'] = $_GET['contentID'];
												$this->_nextStep = 'savechanges';
												$this->_editAssetForm();
										}
								}
						}
						elseif(isset($this->_submit['assetName'])) {
								$this->_addAsset();
						}
						elseif(isset($this->_submit['templateID'])) {
								if($this->_mode == 'addglobal') {
										$this->_nextStep = 'finish';
										$this->_addContentForm();
								}
								elseif($this->_mode == 'editglobal') {
										$this->_nextStep = 'editasset';
										$this->_chooseAssetForm();
								}
						}
				}
				else {
						if($this->_mode == 'addcontent') {
								$this->_nextStep = 'finish';
								$this->_addContentForm();
						}
						elseif($this->_mode == 'reordercontent') {
								$this->_nextStep = 'finish';
								$this->_reorderAssetsForm();
						}
						elseif($this->_mode == 'editcontent') {
								$this->_nextStep = 'editasset';
								$this->_chooseAssetForm();
						}
						elseif($this->_mode == 'addglobal') {
								$this->_nextStep = 'addcontent';
								$this->_chooseTemplateForm();
						}
						elseif($this->_mode == 'editglobal') {
								$this->_nextStep = 'chooseasset';
								$this->_chooseTemplateForm();
						}
						elseif(isset($_GET['contentID'])) {
								$this->_submit['assetID'] = $_GET['contentID'];
								$this->_nextStep = 'savechanges';
								$this->_editAssetForm();
						}
				}
		}

		private function _checkAuth() {
				if($this->_auth->getUserLevel() < 100) {
						header("Location:/Home/Login.html");
				}
		}

		public function outputFormBody() {
				return $this->_form;
		}

		public function getNextStep() {
				return $this->_nextStep;
		}

		public function getMsg() {
				return $this->_msg;
		}
		
		private function _setProps(){
				$this->_curDate = date('Y-m-d');
				$this->_curTime = date('H:i:s');

				
				$this->_auth = new Authentication();
				$this->_checkAuth();

				global $db;
				$this->_db = $db;

				global $url;
				$this->_url = $url;


				$this->_mode = $_GET['mode'];
				if(isset($_GET['recordNum'])) {
						$this->_contentRecordNum = $_GET['recordNum'];
				}

				if(isset($_POST['submit'])) {
						$this->_submit = $_POST;
				}

				$this->_getZoneAreas();
		}


		private function _addContentForm() {
				$formBody = new Template("./kibu/templates/");
				$formBody->set("assetTypes", $this->_getAssetTypes());
				$formBody->set("assetPlacements", $this->_getPlacement());
				foreach($this->_assetZones as $zone) {
						if($this->_mode == 'addglobal') {
								if($zone['templateID'] == $this->_submit['templateID']) {
										$assetZones[$zone['contentZoneID']] = $zone['assetZoneName'];
										$formBody->set_vars(array("hiddenInputID" => "templateID", "hiddenInputVal" => $this->_submit['templateID']), false);
								}
						}
						else {
								$assetZones[$zone['contentZoneID']] = $zone['assetZoneName'];
								$formBody->set_vars(array("hiddenInputID" => "contentRecord", "hiddenInputVal" => $this->_contentRecordNum), false);
						}
				}
				$formBody->set("assetZones", $assetZones);
				$this->_form = $formBody->fetch("add_content.tpl.php");
		}

		private function _reorderAssetsForm() {
				$this->_getZoneAssets();
				foreach($this->_assetZones as $assetZone) {
						$assetZones[$assetZone['assetZoneNum']] = $assetZone['assetZoneName'];
				}
				$formBody = new Template("./kibu/templates/");
				$formBody->set("assetZones", $assetZones);
				$formBody->set("zoneAssets", $this->_zoneAssets);
				$formBody->set("orderOptions", $this->_zoneOrderOptions);
				$this->_form = $formBody->fetch("reorder_assets.tpl.php");

		}

		private function _chooseTemplateForm() {
				$this->_getSiteTemplates();
				foreach($this->_siteTemplates as $template) {
						$templates[$template['templateID']] = $template['templateName'];
				}
				$formBody = new Template("./kibu/templates/");
				$formBody->set('template', $templates);
				$this->_form = $formBody->fetch('choose_template.tpl.php');
		}

		private function _chooseAssetForm() {
				$this->_getZoneAssets();
				foreach($this->_assetZones as $zoneID => $zone){
						foreach($this->_zoneAssets as $asset) {
								if(($asset['templateID'] == $zone['templateID']) && ($asset['assetZoneNum'] == $zone['assetZoneNum'])) {
										$zoneAssets[$zone['assetZoneName']][$asset['assetID']] = $asset['assetName'];
								}
						}
				}
				$formBody = new Template("./kibu/templates/");
				$formBody->set('assetZones', $zoneAssets);
				$this->_form = $formBody->fetch("choose_asset.tpl.php");

		}

		private function _editAssetForm() {
				$this->_getContentAsset($this->_submit['assetID']);
				$this->_getContentAssetParams($this->_submit['assetID']);
				if($this->_contentAsset['templateTypeName'] == 'Module') {
						$this->_contentAsset['contentRecord'] = $this->_contentRecord;
						$templateDir = './kibu/modules/'.$this->_contentAsset['moduleLink']."/";
						require_once $templateDir.$this->_contentAsset['moduleLink'].'_class.php';
						$assetClass = new $this->_contentAsset['moduleLink']($this->_contentAsset, $this->_contentAsset);

						if(method_exists($assetClass, 'setEditParamOpts')) {
								$paramOpts = $assetClass->setEditParamOpts();
						}
				}
				else {
						$templateDir = './kibu/templates/';
						$paramOpts = null;
				}
				$templateFile = $this->_contentAsset['templateLink'].'.tpl.php';

				$editTpl = new Template($templateDir);
				$editTpl->set_vars($this->_contentAsset, true);
				$editTpl->set_vars($paramOpts, false);
				$assetEditTpl = $editTpl->fetch($templateFile);

				$editSettingsTpl = new Template('./kibu/templates/');
				$editSettingsTpl->set_vars($this->_contentAsset, true);
				$editSettingsTpl->set('assetEditTpl', $assetEditTpl);
				$editSettingsTpl->set('assetZones', $this->_assetZones);
				$this->_form = $editSettingsTpl->fetch("edit_content.tpl.php");
		}

		private function _getContentAsset($assetID) {
						$query = "SELECT * FROM contentRecordAssets, siteTemplates, siteTemplateTypes, contentAssetTypes
								LEFT JOIN siteModules ON contentAssetTypes.assetModuleID = siteModules.moduleID
										WHERE contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID
												AND contentAssetTypes.assetEditID = siteTemplates.templateID
												AND siteTemplateTypes.templateTypeID = siteTemplates.templateTypeID
												AND contentRecordAssets.assetID = '".$assetID."'";

				$this->_db->setQuery($query);
				$this->_contentAsset = $this->_db->getAssoc();
		}

		private function _getContentAssetParams($assetID) {
						$query = "SELECT contentAssetTypeParams.assetParamName, contentRecordAssetParams.assetParamVal
								FROM contentAssetTypeParams, contentRecordAssetParams
										WHERE contentRecordAssetParams.assetTypeParamID = contentAssetTypeParams.assetTypeParamID
												AND contentRecordAssetParams.contentRecordAssetID = '".$assetID."'";
				
				$this->_db->setQuery($query);
				if($this->_db->getNumRows() > 0) {
						while($result = $this->_db->getAssoc()) {
								$this->_contentAsset[$result['assetParamName']] = $result['assetParamVal'];
						}
				}
		}

		private function _saveAssetChanges() {
				if(isset($this->_submit['paramOpts'])) {
						$this->_updateAssetParams();
				}

				$query = "UPDATE contentRecordAssets
						SET ";
						foreach($this->_submit['asset'] as $key => $value) {
								$query .= $key." = '".$value."', ";
						}
						$query .= " assetEditDate = CURDATE(), assetEditTime = CURTIME()
								WHERE assetID = '".$this->_submit['assetID']."'";
				$this->_db->setQuery($query);

				$this->_msg = "<div class=\"message\">Modifications to this content have been saved.</div>";
		}

		private function _updateAssetParams() {
				foreach($this->_submit['paramOpts'] as $key => $value) {
						$query = "UPDATE contentRecordAssetParams
								SET assetParamVal = '".$value."'
										WHERE contentRecordAssetID = '".$this->_submit['assetID']."'
										AND assetTypeParamID =
										(
												SELECT assetTypeParamID FROM contentAssetTypeParams WHERE assetParamName = '".$key."' LIMIT 1
										)
										LIMIT 1";

						$this->_db->setQuery($query);
				}
		}

		private function _reorderAssets() {
				foreach($this->_submit['orderOptions'] as $key => $val) {
						$query = "UPDATE contentRecordAssets
								SET
										assetOrderNum = '$val'
								WHERE
										assetID = '$key'";
						$this->_db->setQuery($query);
				}
				$this->_form = "Content succesfully reordered.";
		}

		//
		public function _addAsset($array = null, $contentRecordNum = null) {
				if($array == null && $contentRecordNum == null) {
						$array = $this->_submit;
						$contentRecordNum = $this->_contentRecordNum;
				}
				$countQuery = "SELECT contentRecordAssets.assetOrderNum
						FROM contentRecordAssets, siteTemplateContentZones";
						if($contentRecordNum != null) {
								$countQuery .= " WHERE contentRecordNum = '".$contentRecordNum."'";
						}
						else {
								$countQuery .= " WHERE siteTemplateContentZones.contentZoneID = '".$array['templateID']."'";
						}
						$countQuery .= " AND siteTemplateContentZones.contentZoneID = '".$array['contentZoneID']."'";

				$this->_db->setQuery($countQuery);
				$assetCount = $this->_db->getNumRows();
				$assetOrderNum = $assetCount +1;

				$assetCreateDate = $this->_curDate;
				$assetCreateTime = $this->_curTime;
						$query = "INSERT INTO contentRecordAssets
								SET
										assetTypeID = '".$array['assetTypeID']."',
										assetName = '".$array['assetName']."',
										contentRecordNum = '".$contentRecordNum."',
										assetCreateDate = '".$assetCreateDate."',
										assetCreateTime = '".$assetCreateTime."',
										contentZoneID = '".$array['contentZoneID']."',
										assetOrderNum = '".$assetOrderNum."',
										isVisible = 'n'";

				$this->_db->setQuery($query);

				$query = "SELECT assetID FROM contentRecordAssets WHERE assetCreateDate = '".$assetCreateDate."' AND assetCreateTime = '".$assetCreateTime."'";
				
				$this->_db->setQuery($query);
				$result = $this->_db->getAssoc();
				$this->_addAssetParams($result['assetID'], $array['assetTypeID']);
				$this->_form = "Your new content block has been added to the bottom of the content zone and page or site template specified and is ready for ";
						if($this->_mode == 'addcontent') {
										$this->_form .= "<a href=\"?mode=edit&amp;contentID=".$result['assetID']."\">editing</a>. ";
						}
						else {
										$this->_form .= "<a href=\"/modal.php?class=ContentAssets&amp;mode=editglobal\" title=\"Edit Global Content\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, params: Form.serialize('".$this->_mode."'), method:'post'}); return false;\">editing</a>. ";
						}
						$this->_form .= "You will need to change the visibility to \"visible\" for it to appear to regular site visitors.";
						if($this->_mode == 'addcontent') {
								$this->_form .= "If you wish to move it elsewhere in its content zone, you can use the 'Reorder Content' button under the 'Page Options' tab of the Admin Toolbar.";
						}
		}
		//

		public function _addAssetParams($assetID, $assetTypeID) {
				$paramsQuery = "SELECT contentAssetTypeParams.*
						FROM contentAssetTypeParams, contentAssetTypes
						WHERE contentAssetTypes.assetModuleID = contentAssetTypeParams.siteModuleID
								AND contentAssetTypes.assetTypeID = '".$assetTypeID."'";
				$this->_db->setQuery($paramsQuery);
				$numRows = $this->_db->getNumRows();
				if($numRows > 0) {
						while($result = $this->_db->getAssoc()) {
								$assetTypeParam[] = $result;
						}
						foreach($assetTypeParam as $key => $val) {
								$paramInsert = "INSERT INTO contentRecordAssetParams
										SET
												assetParamVal = '".$val['defaultValue']."',
												contentRecordAssetID = '".$assetID."',
												assetTypeParamID = '".$val['assetTypeParamID']."'";
								$this->_db->setQuery($paramInsert);
						}
				}
		}

		private function _getAssetTypes() {
			$query = "SELECT assetTypeID, assetTypeName FROM contentAssetTypes ORDER BY assetTypeName";
			$query = $this->_db->setQuery($query);
			while($result = $this->_db->getAssoc($query)){
				foreach($result as $key => $value) {
					$assetTypes[$result['assetTypeID']] = $result['assetTypeName'];
				}
			}
			return $assetTypes;
		}

		private function _getPlacement() {
			$query = "SELECT contentRecordAssets.assetOrderNum, contentAssetTypes.assetTypeName FROM contentRecordAssets, contentAssetTypes WHERE contentRecordAssets.contentRecordNum = '$this->_contentRecordNum' AND contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID ORDER BY contentRecordAssets.assetOrderNum";
			$query = $this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				while($result = $this->_db->getAssoc($query)){
					foreach($result as $key => $value) {
						$contentAssets[$result['assetOrderNum']] = $result['assetTypeName'];
					}
				}
				return $contentAssets;
			}
			else {
				return false;
			}
		}

		private function _getZoneAreas() {
				if($this->_mode == 'editglobal' || $this->_mode == 'addglobal') {
				$query = "SELECT siteTemplateContentZones.contentZoneID, siteTemplateContentZones.assetZoneNum, siteTemplateContentZones.assetZoneName, siteTemplateContentZones.templateID
						FROM siteTemplateContentZones, siteTemplates, siteTemplateZoneTypes, contentRecords
								WHERE	siteTemplateContentZones.templateID = siteTemplates.templateID
										AND siteTemplateContentZones.contentZoneType = siteTemplateZoneTypes.zoneTypeID
										AND siteTemplateZoneTypes.zoneTypeName = 'Site'
								GROUP BY siteTemplateContentZones.assetZoneName
								ORDER BY siteTemplateContentZones.assetZoneNum ASC";
				}
				else {
				$query = "SELECT siteTemplateContentZones.contentZoneID, siteTemplateContentZones.assetZoneName, siteTemplateContentZones.assetZoneNum
						FROM siteTemplateContentZones, siteTemplates, siteTemplateZoneTypes, contentRecords
								WHERE	siteTemplates.templateID = contentRecords.siteTemplateID
										AND	siteTemplateContentZones.templateID = siteTemplates.templateID
										AND	contentRecords.contentRecordNum = '".$this->_contentRecordNum."'
										AND siteTemplateContentZones.contentZoneType = siteTemplateZoneTypes.zoneTypeID
										AND siteTemplateZoneTypes.zoneTypeName = 'Page'
								ORDER BY siteTemplateContentZones.assetZoneNum ASC";
				}
				$this->_db->setQuery($query);
				while($result = $this->_db->getAssoc()) {
						$this->_assetZones[$result['contentZoneID']] = $result;
				}
		}

		private function _getZoneAssets() {
				$query = "SELECT contentRecordAssets.assetName, contentRecordAssets.assetID, siteTemplates.templateID, siteTemplateContentZones.assetZoneNum, contentRecordAssets.assetOrderNum
								FROM contentRecordAssets, siteTemplateContentZones, siteTemplates";
								if($this->_mode == 'editglobal') {
										$query .= " WHERE siteTemplateContentZones.templateID = '".$this->_submit['templateID']."'
																		AND siteTemplateContentZones.contentZoneType = '1'";
								}
								else {
										$query .= " WHERE contentRecordAssets.contentRecordNum = '".$this->_contentRecordNum."'
																		AND siteTemplateContentZones.contentZoneType = '2'";
								}
								$query .= " AND siteTemplates.templateID = siteTemplateContentZones.templateID
										AND siteTemplateContentZones.contentZoneID = contentRecordAssets.contentZoneID
										ORDER BY siteTemplateContentZones.assetZoneNum ASC, contentRecordAssets.assetOrderNum ASC";
				$this->_db->setQuery($query);
				while($result = $this->_db->getAssoc()) {
						$this->_zoneAssets[] = $result;
						$this->_zoneOrderOptions[] = $result;
				}
		}

		private function _getSiteTemplates() {
				$query = "SELECT siteTemplates.*
						FROM siteTemplates, siteTemplateTypes
								WHERE siteTemplates.templateTypeID = siteTemplateTypes.templateTypeID
										AND siteTemplateTypes.templateTypeName = 'Site'";
				$this->_db->setQuery($query);
				while($result = $this->_db->getAssoc()) {
						$this->_siteTemplates[] = $result;
				}
		}
}
?>
