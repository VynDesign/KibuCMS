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

	require_once './kibu/core/System/Utility.php';
	require_once './kibu/core/SystemManagement/SystemManagement.php';
	require_once './kibu/core/framework/html/form/FormInputSelectOptionsCollection.php';

	class SiteSettings extends SystemManagement {

		//private $_siteTemplates = array();
		private $_siteSections;

		protected $_siteSettings;
		
		
		public function __construct() {
			parent::__construct();
			$this->_permAbility = "Modify Site";

			$this->_siteSettings = $this->_url->siteConfig;
			
			if(count($this->_submit) && isset($this->_submit['siteTitle'])) {
				$this->_nextStep = 'finish';
				$this->_saveSettings();
			}
			else {
				$this->_nextStep = 'save';
				$this->_getSiteTemplates($this->_siteSettings['templateMasterDefault']);
				$this->_getSections();
			}
			$this->_setFormData();	
			$this->_formTpl = 'site_settings.tpl.php';
			
			parent::_setFormBody();
		}
		
		protected function _getSiteTemplates() {
			parent::_getSiteTemplates();
			$options = new FormInputSelectOptionsCollection($this->_siteTemplates, $this->_siteSettings['templateMasterDefault']);
			$this->_siteTemplates = $options->GetMarkup();			
		}

		private function _getSections() {
			$siteSections = Utility::getSections();
			foreach($siteSections as $section) {
				$sectionOptions[$section['sectionID']] = $section['sectionFullName'];
			}
			$options = new FormInputSelectOptionsCollection($sectionOptions, $this->_siteSections['siteDefaultSectionNum']);
			$this->_siteSections = $options->GetMarkup();
		}

		private function _setFormData() {
			$this->_formData = $this->_siteSettings;
			$this->_formData['siteTemplates'] = $this->_siteTemplates;
			$this->_formData['siteSections'] = $this->_siteSections;
		}

		private function _saveSettings() {
			$table = "siteConfig";
			
			$data = array(
				'siteTitle' => $this->_submit['siteTitle'],
				'siteTagLine' => $this->_submit['siteTagLine'],
				'siteOwner' => $this->_submit['siteOwner'],
				'siteEmail' => $this->_submit['siteEmail'],
				'templateMasterDefault' => $this->_submit['templateMasterDefault'],
				'siteDefaultSectionNum' => $this->_submit['siteDefaultSectionNum']
			);
			
			$where = "siteConfig.siteConfigID = '".$this->_siteSettings['siteConfigID']."'";
			
			$this->_db->update($table, $data, $where);
			if($this->_db->getAffectedRows() > 0) {
				$this->_msg = "Site settings updated.";
			}
			else {
				$this->_error = true;
				$this->_msg = "Error encountered updating site settings: ".$this->_db->getError();
			}
		}
	}
	
	
	
	
	class SiteSettings_ServerSettings extends SiteSettings {
		
		
		public function __construct() {
			parent::__construct();
			$this->_permAbility = "Modify Server";
			$this->_formTpl = 'server_settings.tpl.php';

			if(count($this->_submit)) {
				$this->_nextStep = 'finish';
				$this->_saveSettings();
			}
			else {
				$this->_nextStep = 'save';
				$this->_warning = true;
				$this->_msg = "Changing these settings can possibly adversely affect the way your site works.";
			}
			
			parent::_setFormBody();
			
		}
		
		private function _saveSettings() {
			$table = "siteConfig";
			$data = array(
				'siteAddress' => $this->_submit['siteAddress'],
				'cookiePrefix' => $this->_submit['cookiePrefix']
			);
			$where = "siteConfig.siteConfigID = '".$this->_siteSettings['siteConfigID']."'";
			
			$this->_db->update($table, $data, $where);
			
			if($this->_db->getAffectedRows() > 0) {
				$this->_msg = "Server settings updated. You may need to make more modifications, such as applying new site templates.";
			}
			else {
				$this->_error = true;
				$this->_msg = "Error encountered updating site settings: ".$this->_db->getError();
			}			
		}		
	}
	
?>
