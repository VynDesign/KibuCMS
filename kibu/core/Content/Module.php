<?php

	require_once './kibu/core/Content/interface/IModule.php';
	require_once './kibu/core/Navigation/Url.php';
	require_once './kibu/core/Authentication/Authentication.php';

	abstract class Module {

		protected $_moduleDir = './kibu/modules/';
		protected $_moduleNum;
		protected $_params;
		protected $_paramOpts;
		protected $_submit;
		protected $_error;
		protected $_output;
		protected $_db;
		protected $_url;
		protected $_auth;
		protected $_mode = 'display';
		protected $_requestedModule;
		protected $_moduleLink;
		protected $_modulePath;
		protected $_moduleTpl;
		protected $_contentRecordNum;
		
		public $requiresAuth = true;
		public $isForm = true;

		public function __construct($requestedModule) {

			global $db;
			$this->_db = $db;

			$this->_url = new URL_ext();
			
			$this->_auth = new Authentication();
			
			$this->_requestedModule = $requestedModule;	
						
			if(isset($_GET['mode'])) {
				$this->_mode = $_GET['mode'];
			}			
			
			if(isset($_GET['contentRecordNum'])) {
				$this->_contentRecordNum = $_GET['contentRecordNum'];
			}
			elseif(isset($this->_requestedModule['contentRecordNum'])) {
				$this->_contentRecordNum = $this->_requestedModule['contentRecordNum'];
			}
			
			$this->setRequestedModule();
			if(isset($this->_requestedModule['moduleNum'])) {
				$this->_moduleNum = $this->_requestedModule['moduleNum'];
			}
			else {
				$this->_setModuleNum();		
			}			
		}
		

		public function setRequestedModule() {
			if(isset($this->_requestedModule['params'])) {
				$this->_params = $this->_requestedModule['params'];
			}			
			
			if(!isset($this->_requestedModule['moduleLink'])) {
				$this->_setModuleData();
			}
			else {
				$this->_moduleLink = $this->_requestedModule['moduleLink'];
				$this->_modulePath = $this->_moduleDir . $this->_moduleLink . '/'; // build directory out of composite properties
				$this->_moduleTpl = new Template($this->_modulePath); // instantiate new template object $module
				if((isset($this->_requestedModule['params'])) && count($this->_requestedModule['params'])) {
					$this->_params = $this->_requestedModule['params'];
				}
				else {
					$this->_getContentAssetParams($this->_requestedModule['assetID']);
				}
			}
		}

		public function loadModule() {		
			if ($this->_requestedModule['assetDisplayID'] != 0) {
				$this->_moduleTpl->set_vars($this->_requestedModule); // assign array of content asset values to $module template
				$this->_moduleTpl->set_vars($this->_params);
				$this->_moduleTpl->set('assetBody', $this->returnData());
				$this->_output = $this->_moduleTpl->fetch($this->_requestedModule['templateLink'] . '.tpl.php'); // save template file to variable
			} else {
				$this->_output = null;
			}
		}

		protected function _setModuleNum() {
			$query = "SELECT moduleNum FROM siteModules WHERE moduleLink = '" . $this->_moduleLink . "'";
			$this->_db->setQuery($query);
			$result = $this->_db->getAssoc();
			$this->_moduleNum = $result['moduleNum'];
		}

		
		private function _checkPermissions() {
			if($this->_mode != 'display' && $this->requiresAuth) {
				$query = "SELECT editorAuthLevel FROM contentRecords WHERE contentRecordNum = '" .$this->_contentRecordNum. "'";
				$this->_db->setQuery($query);
				$return = $this->_db->getAssoc();
				if($this->_auth->getUserLevel() < $return['editorAuthLevel']) {
					return false;
				}
				return true;
			}
			return true;
		}
		
//		private function _setModuleParams($assetID) {
//			$query = "SELECT contentAssetTypeParams.assetParamName, contentRecordAssetParams.assetParamVal, contentRecordAssetParams.contentRecordAssetID
//					FROM contentRecordAssetParams, contentAssetTypeParams, contentRecords, contentRecordAssets
//					WHERE contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
//						AND contentRecordAssets.contentRecordNum = '".$this->_contentRecordNum."'
//						AND contentAssetTypeParams.assetTypeParamID = contentRecordAssetParams.assetTypeParamID
//						AND contentRecordAssetParams.contentRecordAssetID = contentRecordAssets.assetID
//						AND contentRecords.contentRecordNum = contentRecordAssets.contentRecordNum";
//			$this->_db->setQuery($query);
//			while($result = $this->_db->getAssoc()) {
//				$this->_params[$result['assetParamName']] = $result['assetParamVal'];
//			}
//		}
	
		private function _getContentAsset($assetID) {
			$query = "SELECT * FROM contentRecordAssets, siteTemplates, siteTemplateTypes, contentAssetTypes
					LEFT JOIN siteModules ON contentAssetTypes.assetModuleID = siteModules.moduleID
					WHERE contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID
						AND contentAssetTypes.assetEditID = siteTemplates.templateID
						AND siteTemplateTypes.templateTypeID = siteTemplates.templateTypeID
						AND contentRecordAssets.assetID = '".$assetID."'";

			$this->_db->setQuery($query);
			$this->_requestedModule = $this->_db->getAssoc();
		}

		private function _getContentAssetParams($assetID) {
			$query = "SELECT siteModulesParams.siteModulesParamName, contentRecordAssetParams.assetParamVal
					FROM siteModulesParams, contentRecordAssetParams, contentAssetTypes
						WHERE siteModulesParams.siteModulesParamID = contentRecordAssetParams.assetTypeParamID
							AND siteModulesParams.siteModuleID = contentAssetTypes.assetModuleID
							AND contentRecordAssetParams.contentRecordAssetID = '".$assetID."'";
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				while($result = $this->_db->getAssoc()) {
					$this->_params[$result['siteModulesParamName']] = $result['assetParamVal'];
				}
			}
		}
	
		private function _setModuleData() {
			$this->_setModuleNum();
			if(isset($this->_requestedModule['module'])) {
				$this->_moduleLink = $this->_requestedModule['module'];
			}
			if((!isset($this->_requestedModule['moduleID'])) && (isset($this->_requestedModule['assetID']))) {
				$this->_getContentAsset($this->_requestedModule['assetID']);
				$this->_getContentAssetParams($this->_requestedModule['assetID']);
			}
		}

		public function getOutput() {			
			if($this->_checkPermissions()) {
				return $this->_output; // return the compiled template for use in calling script
			}
		}

		public function getEditParamOpts() {
			return $this->_paramOpts;
		}
		
		public function getSubmit() {
			return $this->_submit;
		}

		public function returnData() {
			return $this->_params;
		}

		public function getTemplateVars() {
			return $this->_params;
		}

		public function getError() {
			return $this->_error;
		}
	}

?>
