<?php


	require_once './kibu/core/framework/template/Template.php';

	/**
	 * Description of ContentAsset
	 *
	 * @author vyn
	 */
	class ContentAsset {

		private $_url;
		private $_assetData;
		private $_contentAssetBody;
		private $_contentAssetOutput;

		
		public function GetAssetOutput() {
			return $this->_contentAssetOutput;
		}



		public function __construct($assetData) {

			global $url;
			$this->_url = $url;	
			$this->_assetData = $assetData;

			if($this->_url->mode == 'html') {
				$this->_contentAssetOutput();
			}
			elseif($this->_url->mode == 'edit') {
				$this->_contentAssetEditOutput();
			}
			else {
				$this->_contentAssetAltOutput();
			}
		}

		private function _contentAssetOutput() {
			if ($this->_assetData['isVisible'] == 'y') {
				if ($this->_assetData['assetModuleID'] != 0) { // if this asset is a type of module
					$this->_loadModule();
				} 
				elseif (($this->_assetData['assetModuleID'] == 0) && ($this->_assetData['assetDisplayID'] != 0)) {
					$assetTpl = new Template('./kibu/core/templates/');
					$assetTpl->set('assetBody', $this->_assetData['assetBody']);
					$this->_contentAssetBody = $assetTpl->fetch($this->_assetData['templateLink'] . '.tpl.php');
				} 
				else {
					$this->_contentAssetBody = $this->_assetData['assetBody'];
				}
				$this->_applyWrapperTpl();			
			}
		}

		private function _contentAssetEditOutput() {
			if ($this->_assetData['assetModuleID'] != 0) { // if this asset is a type of module
				$this->_loadModule();
			} 
			elseif (($this->_assetData['assetModuleID'] == 0) && ($this->_assetData['assetDisplayID'] != 0)) {
				$assetTpl = new Template('./kibu/core/templates/');
				$assetTpl->set('assetBody', $this->_assetData['assetBody']);
				$this->_contentAssetBody .= $assetTpl->fetch($this->_assetData['templateLink'] . '.tpl.php');
			} 
			else {
				$this->_contentAssetBody .= $this->_assetData['assetBody'];
			}
			$tpl = new Template('./kibu/core/Content/templates/');
			$tpl->set_vars($this->_assetData);
			$this->_contentAssetBody .= $tpl->fetch("edit_asset_link.tpl.php");
			$this->_applyWrapperTpl();	
		}

		private function _contentAssetAltOutput() {
			if ($this->_assetData['isVisible'] == 'y' && $this->_assetData['assetAltDisplayID'] != 0) {
				if ($this->_assetData['assetModuleID'] != 0) { // if there is a template assigned to this asset
					$moduleLink = $this->_assetData['moduleLink'];
					$modulePath = "./kibu/modules/" . $moduleLink . '/'; // build directory out of composite properties
					$moduletpl = new Template($modulePath); // instantiate new template object $module
					if ($this->_assetData['params'] != null) { // if $assetParams has a value
						foreach ($this->_assetData['params'] as $param) { // iterate through array
							$moduletpl->set($param['assetParamName'], $param['assetParamValue']); // assign each value to its name for use in $module template
						}
					}
					if (file_exists($modulePath . $moduleLink . '_Module.php')) { // if there is a control file
						require_once $modulePath . $moduleLink . '_Module.php'; // include control file
						$moduleClass = new $moduleLink($this->_assetData, $this->_assetData['params']);
					}
					if ($this->_assetData['assetDisplayID'] != 0) {
						$moduletpl->set_vars($this->_assetData, true); // assign array of content asset values to $module template
						$moduletpl->set_vars($moduleClass->getTemplateVars(), true);
						if (method_exists($moduleClass, 'returnData')) {
							$moduletpl->set('assetBody', $moduleClass->returnData());
						}
						$this->_contentAssetBody = $moduletpl->fetch($this->_getAltDisplayTemplate($this->_assetData['assetAltDisplayID']) . '.tpl.php'); // save template file to variable
					}
				} 
				elseif ($this->_assetData['assetModuleID'] == 0) {
					$assetTpl = new Template('./kibu/core/templates/');
					$assetTpl->set('assetBody', $this->_assetData['assetBody']);
					$this->_contentAssetBody = $assetTpl->fetch($this->_getAltDisplayTemplate($this->_assetData['assetAltDisplayID']) . '.tpl.php');
				}
			}
		}

		private function _getAltDisplayTemplate($assetAltDisplayID) {
			$query = "SELECT siteTemplates.templateLink
							FROM siteTemplates, contentAssetTypes
							WHERE contentAssetTypes.assetAltDisplayID = '" . $assetAltDisplayID . "'
									AND siteTemplates.templateID = contentAssetTypes.assetAltDisplayID";
			$this->_db->setQuery($query);
			$result = $this->_db->getAssoc();
			return $result['templateLink'];
		}

		private function _loadModule() {
			require_once "./kibu/modules/" . $this->_assetData['moduleLink'] . "/" . $this->_assetData['moduleLink']. "_Module.php";
			$className = $this->_assetData['moduleLink']."_Module";
			$bodymodule = new $className($this->_assetData);
			if(is_subclass_of($bodymodule, "Module")) {
				$bodymodule->loadModule();
				$this->_contentAssetBody = $bodymodule->getOutput();
			}
			else {
				$assetTpl = new Template('./kibu/core/System/templates/');
				$assetTpl->set('assetTypeName', $this->_assetData['assetTypeName']);
				$this->_contentAssetBody = $assetTpl->fetch('kibu_err_module_inheritance.tpl.php');
			}
		}

		private function _applyWrapperTpl() {
			$assetTypeNameClean = $this->_assetData['assetTypeNameClean']; 		
			if($this->_url->mode == 'edit') {
				$assetTypeNameClean = "asset_edit ".$assetTypeNameClean."_edit";
			}

			$wrapper = new Template('./kibu/core/Content/templates/');
			$wrapper->set('assetTypeNameClean', $assetTypeNameClean);
			$wrapper->set('assetID', $this->_assetData['assetID']);
			$wrapper->set('assetBody', $this->_contentAssetBody);
			$this->_contentAssetOutput = $wrapper->fetch('content_asset_wrapper.tpl.php');
		}
	}
?>
