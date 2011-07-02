<?php

	class Module {

		protected $_moduleDir = './kibu/modules/';
		protected $_moduleNum;
		protected $_installedModules;
		protected $_registeredModules;
		protected $_unregisteredModules;
		protected $_unregisteredModuleInfo;
		protected $_disabledModules;
		protected $_enabledModules;
		protected $_submit = array();
		private $_loadedModule;
		private $_contentRecord;

		public function __construct() {
				$this->setRegistered();
				$this->setInstalled();
				$this->setEnabledDisabled();
				$this->setUnregistered();
				if(isset($_POST['managemodules'])) {
						$this->manageModules();
				}
		}

		public function loadModule($loadedModule, $assetParams) {
				$this->_loadedModule = $loadedModule; // save passed array to local class
				$moduleLink = $loadedModule['moduleLink'];
				$modulePath = $this->_moduleDir.$moduleLink.'/'; // build directory out of composite properties
				$moduletpl = new Template($modulePath); // instantiate new template object $module
				if($assetParams != null) { // if $assetParams has a value
						foreach($assetParams as $paramName => $paramVal) { // iterate through array
								$moduletpl->set($paramName, $paramVal); // assign each value to its name for use in $module template
						}
				}
				if(file_exists($modulePath.$moduleLink.'_class.php')) { // if there is a control file
						require_once $modulePath.$moduleLink.'_class.php'; // include control file
						$moduleClass = new $moduleLink($this->_loadedModule, $assetParams);
				}
				if($this->_loadedModule['assetDisplayID'] !=0) {
						$moduletpl->set_vars($this->_loadedModule, true); // assign array of content asset values to $module template
						$moduletpl->set_vars($moduleClass->getTemplateVars(), true);
						if(method_exists($moduleClass, 'returnData')) {
								$moduletpl->set('assetBody', $moduleClass->returnData());
						}
						$output = $moduletpl->fetch($this->_loadedModule['templateLink'].'.tpl.php'); // save template file to variable
				}
				else {$output = null;}
				return $output; // return the compiled template for use in calling script
		}

		private function setInstalled() {
				$openDir = opendir($this->_moduleDir); // open the module directory
				while (($file = readdir($openDir)) !== false) { // iterate through all files/folders found in directory
						if(substr($file, 0, 1) != ".") { // exlude files/folders that begin with '.'
								$this->_installedModules[] = $file; // save results to array, stored in local property '_installedModules'
						}
				}
				closedir($openDir); // close directory being read
		}

		private function setRegistered() {
				global $db; // call $db object from global memory
				$query = "SELECT * FROM siteModules ORDER BY moduleName"; // set query
				$query = $db->setQuery($query); // run query through $db object
				while($assoc = $db->getAssoc()) { // iterate through associative array generated by query
						$this->_registeredModules[$assoc['moduleName']] = $assoc; // build multi-dimensional array of returned results, store it in local property '_registeredModules'
				}
		}

		private function setUnregistered() {
				if(is_array($this->_registeredModules)) {
						foreach($this->_registeredModules as $key => $value) { // iterate through array in '_registeredModules'
								$registeredModules[] = $value['moduleLink']; // create temporary array that matches pattern of '_installedModules'
						}
						$this->_unregisteredModules = array_diff($this->_installedModules, $registeredModules); // remove all installed AND registered modules to form an array of '_unregisteredModules'
				}
		}

		private function setEnabledDisabled() {
				if(is_array($this->_registeredModules)) {
						foreach($this->_registeredModules as $key => $value) { // iterate through array in _registeredModules
								if($value['moduleEnabled'] == 'y') { // if enabled
										$this->_enabledModules[$key] = $value; // add to array stored as '_enabledModules'
								}
								if($value['moduleEnabled'] == 'n') { // if disabled
										$this->_disabledModules[$key] = $value; // add to array stored as '_disabledModules'
								}
						}
				}
		}

		public function findModuleNum($moduleLink) {
			global $db; // access $db object from global memory
			$query = "SELECT moduleNum FROM siteModules WHERE moduleLink = '".$moduleLink."'"; // set query to retreive moduleNum
			$query = $db->setQuery($query); // set query to $db object
			$assoc = $db->getAssoc($query); // get associative array from $db object
			$this->_moduleNum = $assoc['moduleNum']; // assign result to local property '_moduleNum'
		}

		public function getModuleNum() {
			return $this->_moduleNum; // return '_moduleNum' from local property to calling script
		}


		private function manageModules() {
				if(isset($_POST['enabledisable'])) {
						$this->enableDisable();
				}
				elseif(isset($_POST[''])) {

				}
				else {
						$this->registerModule();
				}
		}


		private function enableDisable() {

		}

		private function setUnregisteredInfo(){
				$moduleInfo = array();
				foreach($this->_unregisteredModules as $key => $value) {
						$configPath = $this->_moduleDir.$value.'/config.php';
						if(file_exists($configPath)) {
								include $configPath;
								$moduleInfo[] = $moduleConfig;
						}
						$this->generateModuleNum();
				}
				$this->_unregisteredModuleInfo = $moduleInfo;
		}

		private function registerModuleFields() {
				ob_start();
				foreach($this->_unregisteredModuleInfo as $key => $value) {
						$checkbox = new Template('./kibu/templates/');
						$vars = array("type" => "checkbox", "value" => $value['moduleName'], "name" => "registerModules['$value[moduleLink]']", "id" => "registerModules['$value[moduleLink]']");
						$checkbox->set_vars($vars, yes);
						$field = $checkbox->fetch('form_input.tpl.php');

						$label = new Template('./kibu/templates/');
						$label->set_vars($vars, yes);
						$label->set('field', $field);
						$label->set('label', $value['moduleName']);
						echo $label->fetch('form_label_back.tpl.php');
				}
				$fields = ob_get_contents();
				ob_end_clean();
				$fieldset = new Template('./kibu/templates/');
				$fieldsetVars = array("legend" => "Unregistered Modules", "fields" => $fields);
				$fieldset->set_vars($fieldsetVars, yes);
				$fieldset = $fieldset->fetch('form_fieldset.tpl.php');
				//$tpl->set('body', $fieldset);
		}

		private function registerModule() {
				$this->setUnregisteredInfo();
				$this->registerModuleFields();
		}

		protected function generateModuleNum($rndNumLength = 5) {
			$rndNum = crypt(uniqid(rand(),1)); // generate a random id encrypt it and store it in $rndNum
			$rndNum = strtolower(strip_tags(stripslashes($rndNum))); // to remove any slashes that might have cropped up, and lower-case all
			$rndNum = str_replace(".","",$rndNum);// Removing '.' character
			$rndNum = strrev(str_replace("/","",$rndNum)); //  remove '/' and reverse string
			$this->_moduleNum = substr($rndNum,0,$rndNumLength); // take first ($rndNumLength) characters from the $rndNum
		}
	}
?>