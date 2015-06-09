<?php

	require_once './kibu/core/framework/date/Date.php';
	require_once './kibu/core/framework/data/Cookie.php';
	require_once './kibu/core/framework/template/Template.php';	
	require_once './kibu/core/Authentication/Authentication.php';
	require_once './kibu/core/Authentication/Permissions.php'; 	
	require_once './kibu/core/Content/Content.php';
	require_once './kibu/core/Content/Page.php';
	require_once './kibu/core/Content/EditorToolbar.php';
	require_once './kibu/core/Content/Module.php';		
	require_once './kibu/core/Navigation/Navigation.php';

	class Kibu {

		public $siteConfig;
		public $auth;
		public $moduleManagement;
		public $navigation;
		public $additionalHead;
		public $additionalFoot;

		protected $_template;
		protected $_contentProperties;
		protected $_constants;
		protected $_additionalHead;
		protected $_additionalFoot;
		protected $globalNav;
		protected $currentNav;
		protected $_content;
		protected $_pageContent;
		protected $_contentType;
		protected $_pageTitle;
		protected $_editorToolbar;
		protected $_bodyExtra;
		protected $db;
		protected $url;
		protected $util;
		protected $date;
		protected $Page;
		protected $tpl;

		public function __construct($db, $url) {
			$this->db = $db; // instantiate Database class, connect to database
			$this->url = $url; // instantiate URL class
			$this->date = new Date();
		}

		public function testConfig() {
			if (($this->db->configured) && ($this->url->configured && $this->db->connected)) {
				$query = "SELECT contentRecords.contentID FROM contentRecords, siteConfig WHERE contentRecords.siteConfigID = siteConfig.siteConfigID";
				$this->db->setQuery($query);
				if ($this->db->getNumRows() > 0) { return true; } 
				else { return false; }
			} 
			else { return false; }
		}

		public function setCore() {
			$this->auth = new Authentication(); // instantiate Authentication class
			$this->siteConfig = $this->url->siteConfig; // instantiate Constants class
			$this->Page = new Page($this->siteConfig); // instantiate Content class
			$this->_contentProperties = $this->Page->getContentProperties(); // generate array of content record values based on Content class
			if ($this->Page->getAuthLevel() > $this->auth->getUserLevel()) {
				header("Location:/");
			} 
			else {
				$this->_pageContent = $this->Page->GetPageOutput();
			}
			
			$this->setNavigation();
			$this->_editorToolbar = new EditorToolbar($this->Page->getContentProperties());
			$this->_pageTitle = $this->Page->pageTitle($this->siteConfig['siteAddress'], $this->siteConfig['siteTagLine']);
			
			$kibuCredit = new Template('./kibu/core/System/templates/');
			$this->_additionalFoot .= $kibuCredit->fetch('kibu_credit.tpl.php');
		}

		private function setNavigation() {
			$this->navigation = new Navigation(); // instantiate Navigation class
			$this->globalNav = $this->navigation->getGlobalNav(); // set globalNav variable
			$this->currentNav = $this->navigation->getCurrentNav(); // set currentNav variable
		}
		
		private function setMasterTemplate() {
			$this->tpl = new Template('./kibu/core/System/templates/'); // instantiate Template class
			
			$query = "SELECT siteTemplates.templateLink
				FROM siteTemplates, navigationSections
				WHERE navigationSections.sectionName = '".$this->Page->getSectionName()."'
				AND 
					(navigationSections.sectionSiteTemplate = siteTemplates.templateID
						OR 
					(siteTemplates.templateID = '".$this->siteConfig['templateMasterDefault']."' AND navigationSections.sectionSiteTemplate = 0))";
			$this->db->setQuery($query);
			$row = $this->db->getAssoc();
			$this->_template = $row['templateLink'];
		}

		public function override($var, $val) {
			$this->$var = $val;
		}

		public function additionalPageHead($item) {
			$this->_additionalHead .= $item;
		}

		public function getAdditionalPageHead() {
			return $this->_additionalHead;
		}

		public function additionalPageFoot($item) {
			$this->_additionalFoot .= $item;
		}

		public function getAdditionalPageFoot() {
			return $this->_additionalFoot;
		}

		public function outputPage() {	
			try {
				$this->setMasterTemplate();
				$this->tpl->set_vars($this->_contentProperties, true); // set contentArray Template class variables
				$this->tpl->set_vars($this->siteConfig); // set constants to Template class variables
				$this->tpl->set('year', $this->date->fullyear); // get this year from Date class
				$this->tpl->set('additionalHead', $this->_additionalHead);
				$this->tpl->set('additionalFoot', $this->_additionalFoot);
				$this->tpl->set('globalNav', $this->globalNav);
				$this->tpl->set('currentNav', $this->currentNav);
				$this->tpl->set('pageContent', $this->_pageContent);
				$this->tpl->set('pageTitle', $this->_pageTitle);
				$this->tpl->set('editorToolbar', $this->_editorToolbar->outputToolbar());
				$this->tpl->set('welcomeMessage', $this->auth->welcomeMessage());
				$this->tpl->set('bodyExtra', $this->_bodyExtra);
				echo $this->tpl->fetch($this->_template.".tpl.php"); // echo the results to output the assembled page, using the content template link from first query as master template.
			}
			catch(Exception $e) {
				
			}
		}
		
		
		
		
		
		
//		private function _contentAssetAltOutput($key) {
//			global $url;
//			$assetBody = '';
//			if ($key['isVisible'] == 'y' && $key['assetAltDisplayID'] != 0) {
//				if ($key['assetModuleID'] != 0) { // if there is a template assigned to this asset
//					$moduleLink = $key['moduleLink'];
//					$modulePath = "./kibu/modules/" . $moduleLink . '/'; // build directory out of composite properties
//					$moduletpl = new Template($modulePath); // instantiate new template object $module
//					if ($key['params'] != null) { // if $assetParams has a value
//						foreach ($key['params'] as $param) { // iterate through array
//							$moduletpl->set($param['assetParamName'], $param['assetParamValue']); // assign each value to its name for use in $module template
//						}
//					}
//					if (file_exists($modulePath . $moduleLink . '_class.php')) { // if there is a control file
//						require_once $modulePath . $moduleLink . '_class.php'; // include control file
//						$moduleClass = new $moduleLink($key, $key['params']);
//					}
//					if ($key['assetDisplayID'] != 0) {
//						$moduletpl->set_vars($key, true); // assign array of content asset values to $module template
//						$moduletpl->set_vars($moduleClass->getTemplateVars(), true);
//						if (method_exists($moduleClass, 'returnData')) {
//							$moduletpl->set('assetBody', $moduleClass->returnData());
//						}
//						$assetBody .= $moduletpl->fetch($this->_getAltDisplayTemplate($key['assetAltDisplayID']) . '.tpl.php'); // save template file to variable
//					}
//				} elseif ($key['assetModuleID'] == 0) {
//					$assetTpl = new Template('./kibu/core/templates/');
//					$assetTpl->set('assetBody', $key['assetBody']);
//					$assetBody .= $assetTpl->fetch($this->_getAltDisplayTemplate($key['assetAltDisplayID']) . '.tpl.php');
//				}
//			}
//			return $assetBody;
//		}
//
//		private function _getAltDisplayTemplate($assetAltDisplayID) {
//			global $db;
//			$query = "SELECT siteTemplates.templateLink
//							FROM siteTemplates, contentAssetTypes
//							WHERE contentAssetTypes.assetAltDisplayID = '" . $assetAltDisplayID . "'
//									AND siteTemplates.templateID = contentAssetTypes.assetAltDisplayID";
//			$db->setQuery($query);
//			$result = $db->getAssoc();
//			return $result['templateLink'];
//		}
		
	}
?>
