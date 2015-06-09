<?php

	require_once './kibu/core/SystemManagement/SystemManagement.php';

	class AssetManagement extends SystemManagement {

		protected $_contentRecordNum = null;
		protected $_siteTemplates;
		protected $_assetZones;
		protected $_zones;
		protected $_assetTypes;
		protected $_zoneAssets;
		protected $_contentAsset;
		protected $_contentRecord;
		protected $_assetID;

		public function  __construct($contentRecord = null) {
			parent::__construct();
			
			if($contentRecord != null) {
				$this->_contentRecord = $contentRecord;
			}
			
			$this->_setProps();
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
		}

		
		protected function _getContentAsset() {
			$query = "SELECT * FROM contentRecordAssets, siteTemplates, siteTemplateTypes, contentAssetTypes
				LEFT JOIN siteModules ON contentAssetTypes.assetModuleID = siteModules.moduleID
					WHERE contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID
						AND contentAssetTypes.assetEditID = siteTemplates.templateID
						AND siteTemplateTypes.templateTypeID = siteTemplates.templateTypeID
						AND contentRecordAssets.assetID = '".$this->_assetID."'";
			$this->_db->setQuery($query);
			$this->_contentAsset = $this->_db->getAssoc();
		}
		
		
		protected function _getContentAssetParams() {
			$query = "SELECT siteModulesParams.siteModulesParamName, contentRecordAssetParams.assetParamVal
					FROM siteModulesParams, contentRecordAssetParams
						WHERE contentRecordAssetParams.assetTypeParamID = siteModulesParams.siteModulesParamID
							AND contentRecordAssetParams.contentRecordAssetID = '".$this->_assetID."'";
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				while($result = $this->_db->getAssoc()) {
					$this->_contentAsset[$result['siteModulesParamName']] = $result['assetParamVal'];
				}
			}
		}

		
		protected function _getAssetTypes() {
			$query = "SELECT assetTypeID, assetTypeName FROM contentAssetTypes ORDER BY assetTypeName";
			$query = $this->_db->setQuery($query);
			while($result = $this->_db->getAssoc($query)){
				foreach($result as $key => $value) {
					$this->_assetTypes[$result['assetTypeID']] = $result['assetTypeName'];
				}
			}
		}
		
		protected function _getZoneAssets() {
						
			$query = "
				SELECT
					siteTemplateContentZonesMatchup.assetZoneNum,
					siteTemplateContentZones.assetZoneName, siteTemplateContentZones.assetZoneNameClean,
					siteTemplateZoneTypes.zoneTypeName,
					contentRecordAssets.assetID,
					contentRecordAssets.assetName,
					contentRecordAssets.assetOrderNum
				FROM 
					siteTemplateContentZonesMatchup,
					siteTemplateContentZones,
					siteTemplateZoneTypes,
					contentRecordAssets,
					contentRecords
				WHERE
					siteTemplateZoneTypes.zoneTypeID = siteTemplateContentZones.contentZoneType
					AND contentRecordAssets.contentZoneID = siteTemplateContentZones.contentZoneID
					AND (
						( contentRecordAssets.contentRecordNum = contentRecords.contentRecordNum AND siteTemplateZoneTypes.zoneTypeID = '2' )
						OR
						( siteTemplateZoneTypes.zoneTypeID = '1' )
					)
					AND siteTemplateContentZones.contentZoneGUID = siteTemplateContentZonesMatchup.contentZoneGUID
					AND siteTemplateContentZonesMatchup.templateID = contentRecords.siteTemplateID
					AND contentRecords.contentRecordNum = '$this->_contentRecordNum'
				ORDER BY 
					siteTemplateZoneTypes.zoneTypeID DESC, siteTemplateContentZonesMatchup.assetZoneNum ASC, contentRecordAssets.assetOrderNum ASC";
						
			$this->_db->setQuery($query);
			while($result = $this->_db->getAssoc()) {
				$zone = $result['assetZoneName']."<br />(".$result['zoneTypeName']." Zone ".$result['assetZoneNum'].")";
				$assetID = $result['assetID'];
				$this->_zoneAssets[$zone][$assetID] = $result;
			}
		}
		
		
		protected function _setZoneOrderOptions() {
			foreach($this->_zoneAssets as $zone => $assets) {				
				$optionCount = count($assets);
				foreach($assets as $asset) {
					for($i = 1; $i <= count($assets); $i++) {
						$this->_zoneAssets[$zone][$asset['assetID']]['orderOptions'][$i] = $i;
					}
				}			
			}
			
			foreach($this->_zoneAssets as $zone => $assets) {
				foreach($assets as $asset) {
					$options = new FormInputSelectOptionsCollection($asset['orderOptions'], $asset['assetOrderNum']);
					$this->_zoneAssets[$zone][$asset['assetID']]['orderOptions'] = $options->GetMarkup();
				}
			}			
		}
		
		
		//
		public function _addAsset($array = null, $contentRecordNum = null) {
			if($array == null && $contentRecordNum == null) {
				$array = $this->_submit;
			}
			
			$this->_submit['assetOrderNum'] = $this->_getAssetCount($array['contentZoneID']) + 1;
			$this->_submit['assetCreateDate'] = $this->_curDate;
			$this->_submit['assetCreateTime'] = $this->_curTime;
			
			$table = "contentRecordAssets";
			
			$data = array(
				'assetTypeID' => $this->_submit['assetTypeID'],
				'assetName' => $this->_submit['assetName'],
				'contentRecordNum' => $this->_submit['contentRecordNum'],
				'assetCreateDate' => $this->_submit['assetCreateDate'],
				'assetCreateTime' => $this->_submit['assetCreateTime'],
				'contentZoneID' => $this->_submit['contentZoneID'],
				'assetOrderNum' => $this->_submit['assetOrderNum'],
				'isVisible' => "n"
			);

			$this->_db->insert($table, $data);
			if($this->_db->getAffectedRows() > 0) {

				$assetID = $this->_getAssetID();
				
				$this->_addAssetParams($assetID, $this->_submit['assetTypeID']);
				
				$this->_msg = "Your new content asset has been added to the bottom of the content zone specified and is ready for editing. You will need to change the visibility to \"visible\" for it to appear to regular site visitors. If you wish to move it elsewhere in its content zone, you can use the 'Reorder Content' button under the 'Manage Page' tab of the Admin Toolbar.";
			}
			else {
				$this->_error = true;
				$this->_msg = "An error was encountered attempting to add this new content asset: " .$this->_db->getError();
				
			}
		}
		//

		
		public function _addAssetParams($assetID, $assetTypeID) {
			$q = "SELECT siteModulesParams.*
				FROM siteModulesParams, contentAssetTypes
					WHERE contentAssetTypes.assetModuleID = siteModulesParams.siteModuleID
						AND contentAssetTypes.assetTypeID = '".$assetTypeID."'";
			$this->_db->setQuery($q);
			$numRows = $this->_db->getNumRows();
			if($numRows > 0) {
				while($result = $this->_db->getAssoc()) {
					$assetTypeParam[] = $result;
				}
				foreach($assetTypeParam as $key => $val) {
					$table = "contentRecordAssetParams";
					$data = array(
						'assetParamVal' => $val['siteModulesParamDefaultValue'],
						'contentRecordAssetID' => $assetID,
						'assetTypeParamID' => $val['siteModulesParamID']
					);
					
					$this->_db->insert($table, $data);					
				}
			}
		}	
		
		
		protected function _getAssetZonesByContentRecord() {			
			$query = " 
				SELECT 
					siteTemplateContentZones.assetZoneName, 
					siteTemplateContentZones.contentZoneID,
					siteTemplateZoneTypes.zoneTypeName
				FROM 
					contentRecords,
					siteTemplateContentZones,
					siteTemplateContentZonesMatchup,
					siteTemplateZoneTypes
				WHERE siteTemplateContentZones.contentZoneGUID = siteTemplateContentZonesMatchup.contentZoneGUID
					AND siteTemplateContentZonesMatchup.templateID = contentRecords.siteTemplateID
					AND contentRecords.contentRecordNum = '".$this->_contentRecordNum."'
					AND siteTemplateZoneTypes.zoneTypeID = siteTemplateContentZones.contentZoneType
				ORDER BY siteTemplateContentZonesMatchup.assetZoneNum ASC";
						
			$this->_db->setQuery($query);
			
			while($zones = $this->_db->getAssoc()) {
				$this->_zones[$zones['contentZoneID']] = $zones['assetZoneName'] . " (".$zones['zoneTypeName'].")";
			}
		}	
	}





	class AssetManagement_AddAsset extends AssetManagement {
		
		
		public function __construct($contentRecord = null, $global = false) {
			$this->_permAbility = "Add Local Content";
			parent::__construct($contentRecord);
	
			$this->_formTpl = "add_content.tpl.php";
			
			if((count($this->_submit)) && (isset($this->_submit['assetName']))) {
				$this->_addAsset();
			}
			
			$this->_setFormData();
			parent::_setFormBody();
		}
		
		protected function _setFormData() {
			parent::_getAssetTypes();
			
			$assetTypeOptions = new FormInputSelectOptionsCollection($this->_assetTypes);
			
			parent::_getAssetZonesByContentRecord();			
			$zoneOptions = new FormInputSelectOptionsCollection($this->_zones);
			
			$this->_formData["assetTypeOptions"] = $assetTypeOptions->GetMarkup();
			$this->_formData['assetZoneOptions'] = $zoneOptions->GetMarkup();
			$this->_formData["hiddenInputID"] = "contentRecordNum";
			$this->_formData["hiddenInputVal"] = $this->_contentRecordNum;			
		}
		
		
		protected function _getAssetCount($contentZoneID) {
			$q = "SELECT contentRecordAssets.assetOrderNum FROM contentRecordAssets, siteTemplateContentZones
				 WHERE contentRecordNum = '".$this->_contentRecordNum."'
					AND siteTemplateContentZones.contentZoneID = '".$contentZoneID."'";

			$this->_db->setQuery($q);
			$assetCount = $this->_db->getNumRows();
			return $assetCount;
		}
		
		protected function _getAssetID() {
			$query = "SELECT assetID FROM contentRecordAssets 
				WHERE assetTypeID = '".$this->_submit['assetTypeID']."' 
					AND contentZoneID = '".$this->_submit['contentZoneID']."'
					AND contentRecordNum = '".$this->_submit['contentRecordNum']."'
					AND assetCreateDate = '".$this->_submit['assetCreateDate']."' 
					AND assetCreateTime = '".$this->_submit['assetCreateTime']."'";
				
			$this->_db->setQuery($query);
			$result = $this->_db->getAssoc();
			return $result['assetID'];
		}	
	}
	
	
	
	
	class AssetManagement_EditAsset extends AssetManagement {
				
		public function __construct($contentRecord = null) {
			$this->_permAbility = "Edit Local Content";
			parent::__construct($contentRecord);
			if(count($this->_submit)) {
				if(isset($this->_submit['assetID'])){
					$this->_assetID = $this->_submit['assetID'];
					if($this->_submit['nextStep'] == 'savechanges') {
						$this->_saveAssetChanges();
					}
				}
			}
			elseif(isset($_GET['assetID'])) {
				$this->_assetID = $_GET['assetID'];
				$this->_nextStep = 'savechanges';
			}
			parent::_getContentAsset();
			parent::_getContentAssetParams();
			$this->_editAssetForm();			
		}			
		
		private function _editAssetForm() {
			if($this->_contentAsset['moduleID'] != null) {
				$this->_contentAsset['contentRecord'] = $this->_contentRecord;
				$templateDir = './kibu/modules/'.$this->_contentAsset['moduleLink']."/";
				require_once $templateDir.$this->_contentAsset['moduleLink'].'_Module.php';
				if(class_exists($this->_contentAsset['moduleLink']."_edit")) {
					$editClass = $this->_contentAsset['moduleLink']."_Module_Edit";
				}
				else {
					$editClass = $this->_contentAsset['moduleLink']."_Module";
				}
				$assetClass = new $editClass($this->_contentAsset);

				$paramOpts = $assetClass->getEditParamOpts();
			}
			else {
				$templateDir = './kibu/core/SystemManagement/templates/';
				$paramOpts = null;
			}
			$templateFile = $this->_contentAsset['templateLink'].'.tpl.php';
			
			if(!file_exists($templateDir."/".$this->_contentAsset['templateLink'].".tpl.php")) {
				$templateDir = "./kibu/core/SystemManagement/templates/";
				
			}
						
			$editTpl = new Template($templateDir);
			$editTpl->set_vars($this->_contentAsset, true);
			$editTpl->set_vars($paramOpts, false);
			$assetEditTpl = $editTpl->fetch($templateFile);

			$editSettingsTpl = new Template('./kibu/core/SystemManagement/templates/');
			$editSettingsTpl->set_vars($this->_contentAsset, true);
			$editSettingsTpl->set('assetEditTpl', $assetEditTpl);
			$editSettingsTpl->set('assetZones', $this->_assetZones);
			$this->_form = $editSettingsTpl->fetch("edit_content.tpl.php");
		}
		
		private function _saveAssetChanges() {
			if(isset($this->_submit['paramOpts'])) {
				$this->_updateAssetParams();
			}
			$query = "UPDATE contentRecordAssets SET ";
				foreach($this->_submit['asset'] as $key => $value) {
					$query .= $key." = '".$value."', ";
				}
				$query .= " assetEditDate = CURDATE(), assetEditTime = CURTIME()
						WHERE assetID = '".$this->_submit['assetID']."'";
			$this->_db->setQuery($query);

			$this->_msg = "Modifications to this content have been saved.";
		}

		private function _updateAssetParams() {
			foreach($this->_submit['paramOpts'] as $key => $value) {
				//TODO implement transaction
				$query = "UPDATE contentRecordAssetParams SET assetParamVal = '".$value."'
					WHERE contentRecordAssetID = '".$this->_submit['assetID']."'
						AND assetTypeParamID IN
							(
								SELECT siteModulesParamID FROM siteModulesParams WHERE siteModulesParamName = '".$key."'
							)
						LIMIT 1";
				
				$this->_db->setQuery($query);
			}
		}			
	}
	
	
	
	
	
	
	class AssetManagement_ReorderAssets extends AssetManagement {
		
		
		public function __construct($contentRecord = null) {
			$this->_permAbility = "Reorder Page Content";
			parent::__construct($contentRecord);
			$this->_formTpl = "reorder_assets.tpl.php";
			if(count($this->_submit)) {
				$this->_reorderAssets();
			}
			$this->_setFormData();
			parent::_setFormBody();			
		}
		

		private function _setFormData() {
			parent::_getZoneAssets();
			parent::_setZoneOrderOptions();
			$this->_formData['assetZones'] = $this->_zoneAssets;
		}
		
		private function _reorderAssets() {
			//TODO Implement transaction
			foreach($this->_submit['orderOptions'] as $key => $val) {
				$table = "contentRecordAssets";
				$data = array('assetOrderNum' => $val);
				$where = "assetID = '$key'";
				$this->_db->update($table, $data, $where);
				if($this->_db->error) {
					$this->_error = true;
					$this->_msg = "An error was encountered attempting to reorder content assets: ".$this->_db->getError();
				}
			}
			$this->_msg = "Content succesfully reordered.";
		}
		
	}
	
	
?>
