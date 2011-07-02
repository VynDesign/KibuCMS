<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SiteSettings
 *
 * @author vyn
 */

require_once('./kibu/core/class/Template.php');
require_once('./kibu/core/class/Utility.php');

class SiteSettings {

		private $_siteSettings;
		private $_siteTemplates = array();
		private $_submit;
		private $_form;
		private $_mode;
		private $_siteSections;
		private $_nextStep = null;
		private $_msg;
		private $_auth;
		private $_db;
		private $_url;

		public function __construct() {
				global $url;
				$this->_url = $url;
				$this->_siteSettings = $this->_url->siteConfig;

				global $db;
				$this->_db = $db;

				global $auth;
				$this->_auth = $auth;

				$this->_checkAuth();

				$this->_mode = $_GET['mode'];
				
				if(isset($_POST['submit'])) {
						$this->_submit = $_POST;
						$this->_saveSettings();
				}
				else {
						$this->_nextStep = 'finish';
						$this->_getSiteTemplates();
						$this->_getSections();
						$this->_applyTemplate();
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
		
		private function _getSiteTemplates() {
				global $db;
				$query = "SELECT siteTemplates.templateID, siteTemplates.templateName FROM siteTemplates, siteTemplateTypes
						WHERE
								siteTemplateTypes.templateTypeName = 'Site'
						AND
								siteTemplates.templateTypeID = siteTemplateTypes.templateTypeID
						ORDER BY
								templateID ASC";

				$db->setQuery($query);
				while($result = $db->getAssoc()){
						$this->_siteTemplates[$result['templateID']] = $result['templateName'];
				}
		}

		private function _getSections() {
				$util = new Utility();
				$siteSections = $util->getSections();
				foreach($siteSections as $section) {
						$this->_siteSections[$section['sectionNum']] = $section['sectionFullName'];
				}
		}

		private function _applyTemplate() {
				$siteSettingsTpl = new Template('./kibu/templates/');
				$siteSettingsTpl->set_vars($this->_siteSettings, true);
				$siteSettingsTpl->set('siteTemplates', $this->_siteTemplates);
				$siteSettingsTpl->set('siteSections', $this->_siteSections);
				$this->_form = $siteSettingsTpl->fetch('site_settings.tpl.php');
		}

		private function _saveSettings() {
				global $db;
				$query = "UPDATE siteConfig
						SET
								siteTitle = '".$this->_submit['siteTitle']."',
								siteTagLine = '".$this->_submit['siteTagLine']."',
								siteAddress = '".$this->_submit['siteAddress']."',
								cookiePrefix = '".$this->_submit['cookiePrefix']."',
								siteOwner = '".$this->_submit['siteOwner']."',
								siteEmail = '".$this->_submit['siteEmail']."',
								templateMasterDefault = '".$this->_submit['templateMasterDefault']."',
								siteDefaultSectionNum = '".$this->_submit['siteDefaultSectionNum']."'

						WHERE
								siteConfig.siteConfigID = '".$this->_siteSettings['siteConfigID']."'";
				$this->_db->setQuery($query);
				$this->_msg = "Site settings updated. If you have changed the server settings, you may need to make more modifications, such as applying new site templates.";
		}

}
?>
