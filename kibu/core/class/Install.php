<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Install
 *
 * @author vyn
 */

require_once './kibu/core/class/Template.php';
require_once './kibu/core/class/Date.php';
require_once './kibu/core/class/Form.php';

class Install {

		private $_oDb;
		private $_oUrl;
		private $_oDate;
		private $_oForm;

		private $_required;
		private $_matching;
		private $_submit;
		private $_error;
		private $_errorMsg;
		private $_msg;
		private $_formTpl;
		private $_formBody;
		private $_pageTpl;
		private $_pageTitle;
		private $_pageText;
		private $_pageContent;
		private $_pageContentTitle;

		public $siteConfig;

		public function __construct($db, $url) {
				$this->_oDb = $db;
				$this->_oUrl = $url;
				$this->_oDate = new Date();
				$this->_oForm = new Form();
				
				if(isset($_POST['submit'])) {
						$this->_submit = $_POST;
				}

				if(isset($this->_submit['dbUser'])) {
						$this->_testDBConnection();
				}

				if(!$this->_oDb->connected) {
						$this->_pageContentTitle = "Step 1: Database Configuration";
						$this->siteConfig = null;
						$this->_setDBConfig();
						$this->_formBody();
				}

				elseif(!$this->_oUrl->configured) {
						$this->_pageContentTitle = "Step 2: Site Configuration";
						$this->_msg = "Domain not configured.<br />";
				}

				if($this->_oUrl->configured && $this->_oDb->connected) {
						//echo "No content found.<br />";
				}

				$this->_pageTitle = "Kibu Installer - ".$this->_pageContentTitle;

				$this->_getMsg();
				$this->_formWrapper();
				$this->_outputPage();
		}

		private function _setDBConfig() {

				$this->_pageText = "
						<p>
								The first thing we need to do is hook up to the database. As every web hosting company sets up their MySQL server slightly differently, creating
								the database needs to be done outside of this installer. Don't worry, we'll take care of all the really technical bits like creating
								tables and inserting data - you just need to create the empty database and make note of the Username, Password, Host and Database Name and
								insert them into the form below. This will allow us to configure Kibu to read from and write to the database.
						</p>
				";
				$this->_required = array('dbUser', 'dbPass', 'dbHost', 'dbName');
				$this->_matching = array('dbPass');

				$this->_oForm->setRequired($this->_required);
				$this->_oForm->setMatching($this->_matching);
				
				if(isset($this->_submit['submit'])) {
						$this->_formData = $this->_submit;
				}
				else {
						$this->_formData['dbUser'] = null;
						$this->_formData['dbPass'] = null;
						$this->_formData['dbHost'] = null;
						$this->_formData['dbName'] = null;
				}
				$this->_formData['pageTitle'] = $this->_pageContentTitle;
				$this->_formTpl = "install_dbconfig.tpl.php";
		}

		private function _testDBConnection() {
				if($this->_oForm->getError() == false) {
						$linkID = @mysql_connect($this->submit['dbHost'], $this->_submit['dbUser'], $this->_submit['dbPass'][0]);

						if (!$linkID) {
								$this->_error = true;
								$this->_errorMsg[] = "Could not connect to server: <b>".$this->_submit['dbHost']."</b>.";
						}
						elseif(!@mysql_select_db($this->_submit['dbName'], $linkID)) { //no database
								$this->_error = true;
								$this->_errorMsg[] = "Could not open database: <b>".$this->_submit['dbName']."</b>.";
						}
						else {
								$ths->_error = false;
								$this->_msg = "connection successful!";
						}
				}
		}

		private function _formBody() {
				$tpl = new Template('./kibu/templates/');
				$tpl->set_vars($this->_formData, true);
				$this->_formBody = $tpl->fetch($this->_formTpl);
		}

		private function _formWrapper() {
				$formVars['ID'] = 'installer';
				$formVars['name'] = 'installer';
				$formVars['class'] = 'installer';
				$formVars['action'] = $this->_oUrl->_URLPath;
				$formVars['method'] = 'post';
				$formVars['msg'] = $this->_msg;
				$formVars['formExtra'] = null;

				$formBtns = new Template('./kibu/templates/');

						$btnVars = array(
								'nextStep' => null,
								'submitButtonID' => 'submit',
								'submitButtonName' => 'submit',
								'submitButtonVal' => 'Submit',
								'submitBtnExtra' => null,
								'resetButtnID' => 'reset',
								'resetButtonName' => 'reset',
								'resetButtonVal' => 'Cancel',
								'resetBtnExtra' => null
						);
						$formBtnsTpl = "form_submit_2btn.tpl.php";

				$formBtns->set_vars($btnVars, true);
				$formSubmit = $formBtns->fetch('form_submit_2btn.tpl.php');

				$formTpl = new Template('./kibu/templates/');
				$formTpl->set_vars($formVars, true);
				$formTpl->set('formBody', $this->_formBody);
				$formTpl->set('formSubmit', $formSubmit);
				$this->_formBody = $formTpl->fetch('form_wrapper.tpl.php');
		}

		private function _pageContent() {
				$pageContentTpl = new Template('./kibu/templates/');
				$pageContentTpl->set('pageTitle', $this->_pageContentTitle);
				$pageContentTpl->set('pageText', $this->_pageText);
				$pageContentTpl->set('formBody', $this->_formBody);
				$this->_pageContent[1] = $pageContentTpl->fetch('install_pageContent.tpl.php');
		}

		private function _setDBTables() {
				$sql = "

						CREATE TABLE `contentAssetTypeParams` (
								`assetTypeParamID` smallint(6) NOT NULL AUTO_INCREMENT,
								`siteModuleID` smallint(6) NOT NULL,
								`assetParamName` varchar(200) NOT NULL,
								`defaultValue` varchar(200) NOT NULL,
								PRIMARY KEY (`assetTypeParamID`)
						) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `contentAssetTypes` (
  `assetTypeID` smallint(11) NOT NULL AUTO_INCREMENT,
  `assetDisplayID` smallint(11) NOT NULL,
  `assetModuleID` smallint(11) NOT NULL,
  `assetEditID` smallint(11) NOT NULL,
  `assetAltDisplayID` smallint(6) NOT NULL,
  `assetTypeName` varchar(200) NOT NULL,
  `assetTypeNameClean` varchar(200) NOT NULL,
  `assetTypeDesc` varchar(300) NOT NULL,
  PRIMARY KEY (`assetTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `contentRecordAssetParams` (
  `assetParamID` smallint(6) NOT NULL AUTO_INCREMENT,
  `contentRecordAssetID` smallint(6) NOT NULL,
  `assetTypeParamID` smallint(6) NOT NULL,
  `assetParamVal` varchar(200) NOT NULL,
  PRIMARY KEY (`assetParamID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `contentRecordAssets` (
  `assetID` smallint(11) NOT NULL AUTO_INCREMENT,
  `assetTypeID` smallint(11) NOT NULL,
  `contentRecordNum` varchar(200) NOT NULL,
  `assetCreateDate` date NOT NULL,
  `assetCreateTime` time NOT NULL,
  `assetEditDate` date NOT NULL,
  `assetEditTime` time NOT NULL,
  `assetName` varchar(100) NOT NULL,
  `assetBody` longtext NOT NULL,
  `contentZoneID` smallint(6) NOT NULL,
  `assetOrderNum` smallint(11) NOT NULL,
  `isVisible` enum('y','n') NOT NULL,
  PRIMARY KEY (`assetID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `contentRecords` (
  `contentID` smallint(11) NOT NULL AUTO_INCREMENT,
  `siteConfigID` smallint(11) NOT NULL,
  `contentRecordNum` varchar(200) NOT NULL,
  `contentTypeID` smallint(11) NOT NULL DEFAULT '1',
  `sectionID` smallint(11) NOT NULL DEFAULT '0',
  `sectionNum` varchar(200) NOT NULL,
  `ownerID` smallint(11) NOT NULL,
  `authorID` smallint(11) NOT NULL DEFAULT '0',
  `editorID` smallint(11) NOT NULL,
  `submitDate` date NOT NULL DEFAULT '0000-00-00',
  `submitTime` time NOT NULL DEFAULT '00:00:00',
  `editDate` date NOT NULL DEFAULT '0000-00-00',
  `editTime` time NOT NULL DEFAULT '00:00:00',
  `isVisible` enum('vis','invis','inac') NOT NULL,
  `metaKeywds` longtext NOT NULL,
  `metaDesc` longtext NOT NULL,
  `contentTitle` longtext NOT NULL,
  `titleClean` varchar(200) NOT NULL,
  `isSectionDefault` enum('y','n') NOT NULL,
  `orderNum` smallint(11) NOT NULL DEFAULT '0',
  `visitorAuthLevel` smallint(11) NOT NULL DEFAULT '0',
  `editorAuthLevel` smallint(11) NOT NULL,
  `siteTemplateID` smallint(6) NOT NULL,
  PRIMARY KEY (`contentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `contentTypes` (
  `contentTypeID` smallint(11) NOT NULL AUTO_INCREMENT,
  `contentTypeName` varchar(200) NOT NULL,
  PRIMARY KEY (`contentTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `navigationSections` (
  `sectionID` smallint(11) NOT NULL AUTO_INCREMENT,
  `sectionNum` varchar(200) NOT NULL,
  `sectionName` longtext NOT NULL,
  `sectionFullName` longtext NOT NULL,
  `sectionVisible` enum('y','n') NOT NULL,
  `landingPageRecordNum` varchar(200) NOT NULL,
  `siteConfigID` smallint(6) NOT NULL,
  PRIMARY KEY (`sectionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `navigationSectionsOrder` (
  `sectionOrderNum` mediumint(11) NOT NULL DEFAULT '0',
  `navigationTypeID` smallint(11) NOT NULL,
  `sectionOrderID` smallint(11) NOT NULL AUTO_INCREMENT,
  `sectionNum` varchar(200) NOT NULL,
  PRIMARY KEY (`sectionOrderID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `navigationTypes` (
  `navigationTypeID` smallint(11) NOT NULL AUTO_INCREMENT,
  `navigationTypeName` varchar(200) NOT NULL,
  PRIMARY KEY (`navigationTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `publishingLayouts` (
  `layoutID` smallint(11) NOT NULL AUTO_INCREMENT,
  `layoutName` varchar(200) NOT NULL,
  `layoutDesc` varchar(500) NOT NULL,
  PRIMARY KEY (`layoutID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `publishingLayoutsAssets` (
  `layoutAssetID` smallint(11) NOT NULL AUTO_INCREMENT,
  `layoutID` smallint(11) NOT NULL,
  `assetTypeID` smallint(11) NOT NULL,
  `assetOrderNum` smallint(11) NOT NULL,
  `assetName` varchar(200) NOT NULL,
  `contentZoneID` smallint(6) NOT NULL,
  PRIMARY KEY (`layoutAssetID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `siteConfig` (
  `siteConfigID` smallint(11) NOT NULL AUTO_INCREMENT,
  `siteTitle` varchar(250) NOT NULL DEFAULT '',
  `siteTagLine` text NOT NULL,
  `siteAddress` varchar(250) NOT NULL DEFAULT '',
  `siteBaseDomain` varchar(200) NOT NULL,
  `parentSite` varchar(250) NOT NULL DEFAULT '',
  `address` text NOT NULL,
  `cookiePrefix` varchar(100) NOT NULL,
  `siteLongDesc` text NOT NULL,
  `siteOwner` varchar(250) NOT NULL DEFAULT '',
  `siteEmail` varchar(200) NOT NULL DEFAULT '',
  `templateMasterDefault` smallint(11) NOT NULL,
  `defaultCookieTime` varchar(100) NOT NULL,
  `siteSectionDefaultID` smallint(6) NOT NULL,
  `siteDefaultSectionNum` varchar(200) NOT NULL,
  PRIMARY KEY (`siteConfigID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `siteModules` (
  `moduleID` smallint(11) NOT NULL AUTO_INCREMENT,
  `moduleName` varchar(200) NOT NULL,
  `moduleDesc` mediumtext NOT NULL,
  `moduleLink` varchar(200) NOT NULL,
  `moduleNum` varchar(5) NOT NULL,
  `moduleVersion` varchar(11) NOT NULL,
  `moduleEnabled` enum('y','n') NOT NULL,
  PRIMARY KEY (`moduleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `siteTemplateContentZones` (
  `contentZoneID` smallint(6) NOT NULL AUTO_INCREMENT,
  `contentZoneType` smallint(6) NOT NULL,
  `templateID` smallint(6) NOT NULL,
  `assetZoneNum` smallint(6) NOT NULL,
  `assetZoneName` varchar(200) NOT NULL,
  `assetZoneNameClean` varchar(200) NOT NULL,
  PRIMARY KEY (`contentZoneID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `siteTemplates` (
  `templateID` smallint(11) NOT NULL AUTO_INCREMENT,
  `templateName` varchar(200) NOT NULL,
  `templateLink` varchar(200) NOT NULL,
  `templateTypeID` smallint(11) NOT NULL,
  `templateDesc` mediumtext NOT NULL,
  PRIMARY KEY (`templateID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `siteTemplateTypes` (
  `templateTypeID` smallint(11) NOT NULL AUTO_INCREMENT,
  `templateTypeName` varchar(200) NOT NULL,
  PRIMARY KEY (`templateTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `siteTemplateZoneTypes` (
  `zoneTypeID` smallint(6) NOT NULL AUTO_INCREMENT,
  `zoneTypeName` varchar(200) NOT NULL,
  PRIMARY KEY (`zoneTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `userLevels` (
  `levelID` smallint(11) NOT NULL AUTO_INCREMENT,
  `levelNum` smallint(11) NOT NULL DEFAULT '0',
  `levelName` text NOT NULL,
  `levelDesc` text NOT NULL,
  PRIMARY KEY (`levelID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;


CREATE TABLE `userRecordPrefixes` (
  `namePrefixID` smallint(11) NOT NULL AUTO_INCREMENT,
  `namePrefixAbbrv` varchar(25) NOT NULL,
  PRIMARY KEY (`namePrefixID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `userRecords` (
  `userID` smallint(10) NOT NULL AUTO_INCREMENT,
  `userName` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `namePrefix` smallint(11) NOT NULL,
  `firstName` varchar(100) NOT NULL DEFAULT '',
  `lastName` varchar(100) NOT NULL DEFAULT '',
  `middleName` varchar(50) NOT NULL DEFAULT '',
  `nameSuffix` varchar(50) NOT NULL DEFAULT '',
  `emailAddress` varchar(100) NOT NULL DEFAULT '',
  `emailVerifyString` varchar(200) NOT NULL,
  `emailVerified` char(1) NOT NULL,
  `initIPAddr` varchar(50) NOT NULL DEFAULT '',
  `lastIPAddr` varchar(50) NOT NULL DEFAULT '',
  `joinDate` date NOT NULL DEFAULT '0000-00-00',
  `joinTime` time NOT NULL DEFAULT '00:00:00',
  `verifyDate` date NOT NULL,
  `verifyTime` time NOT NULL,
  `lastActiveDate` date NOT NULL DEFAULT '0000-00-00',
  `lastActiveTime` time NOT NULL DEFAULT '00:00:00',
  `userLevelNum` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;";

		}

		private function _outputPage() {
				$this->_pageContent();
				$this->_pageTpl = new Template('./kibu/templates/');
				$this->_pageTpl->set_vars($this->siteConfig); // set constants to Template class variables
				$this->_pageTpl->set('siteTitle', 'Kibu Installer');
				$this->_pageTpl->set('siteTagLine', 'It\'s pretty easy, really');
				$this->_pageTpl->set('siteOwner', 'you');
				$this->_pageTpl->set('year', $this->_oDate->__get('fullyear')); // get this year from Date class
				$this->_pageTpl->set('additionalHead', null);
				$this->_pageTpl->set('additionalFoot', null);
				$this->_pageTpl->set('globalNav', null);
				$this->_pageTpl->set('currentNav', null);
				$this->_pageTpl->set('pageContent', $this->_pageContent);
				$this->_pageTpl->set('globalContent', null);
				$this->_pageTpl->set('pageTitle', $this->_pageTitle);
				$this->_pageTpl->set('editorToolbar', null);
				$this->_pageTpl->set('welcomeMessage', null);
				$this->_pageTpl->set('bodyExtra', null);
				echo $this->_pageTpl->fetch('kibu_default.tpl.php'); // echo the results to output the assembled page, using the content template link from first query as master template.
		}

		private function _getMsg() {
				if($this->_oForm->getError()) {
						$this->_msg .= $this->_oForm->getMsg();
				}
		}

		public function getError() {
				if($this->_oForm->getError() == true) {
						$this->_error = $this->_oForm->getError();
				}
				return $this->_error;
		}

}
?>
