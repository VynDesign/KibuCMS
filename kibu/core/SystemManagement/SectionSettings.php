<?php
/**
 * A class designed to facilitate modification and creation of sections where content is published
 *
 * @package Kibu
 * @author Vyn Raskopf
 * @copyright Kibu 2010
 * @version 1.0.0.0
 * 
 */

	require_once './kibu/core/framework/template/Template.php';
	require_once './kibu/core/System/Utility.php';
	require_once './kibu/core/SystemManagement/SystemManagement.php';
	require_once './kibu/core/framework/html/form/FormInputSelectOptionsCollection.php';

	class SectionSettings extends SystemManagement {

		protected $_sectionName;
		protected $_sectionInfo;
		
		public function __construct() {
			parent::__construct();	
		}

		protected function _checkSectionName() {
			if($this->_submit['sectionName'] != null) {
				$sectionName = $this->_submit['sectionName'];
				$this->_sectionName = Utility::stripChars($sectionName);
			}
			else {
				$sectionName = $this->_submit['sectionFullName'];
				$this->_sectionName = Utility::stripChars($sectionName);
			}
		}

		protected function _sectionCount() {
			$query = "SELECT COUNT(sectionID) AS rowCount FROM navigationSections WHERE siteConfigID = '".$this->_url->siteConfig['siteConfigID']."'";
			$this->_db->setQuery($query);
			$result = $this->_db->getAssoc();
			return $result['rowCount'];
		}
		
		protected function _getSiteTemplates() {
			parent::_getSiteTemplates();
			$options = new FormInputSelectOptionsCollection($this->_siteTemplates);
			$options->Add(0, "Inherit Default");
			$options->SetSelected($this->_sectionInfo['sectionSiteTemplate']);
			$this->_siteTemplates = $options->GetMarkup();
		}		
	}
	
	
	
	class SectionSettings_CreateSection extends SectionSettings {
		
		public function __construct() {
			parent::__construct();
			$this->_permAbility = "Create Section";
			
			$this->_formTpl = 'section_settings_create.tpl.php';	
			
			if(isset($this->_submit) && $this->_checkSectionName()) {
				$this->_nextStep = 'finish';
				$this->_createNewSection();
			}
			$this->_getSiteTemplates();			
			$this->_setFormData();
			parent::_setFormBody();			
		}
		
		private function _setFormData() {
			if(isset($this->_submit)) {
				$this->_formData = $this->_submit;
			}
			else {
				$this->_formData['sectionPages'] = null;
				$this->_formData['landingPageRecordNum'] = null;
				$this->_formData['sectionFullName'] = null;
				$this->_formData['sectionName'] = null;
				$this->_formData['sectionVisible'] = null;
				$this->_formData['sectionNum'] = Utility::guidGen();
			}
			$this->_formData['siteTemplates'] = $this->_siteTemplates;							
		}
		
		protected function _checkSectionName() {
			parent::_checkSectionName();
			$query = "SELECT COUNT(sectionID) AS rowCount FROM navigationSections WHERE sectionName = '".$this->_sectionName."' AND siteConfigID = '".$this->_url->siteConfig['siteConfigID']."'";
			$this->_db->setQuery($query);
			$result = $this->_db->getAssoc();
			if($result['rowCount'] > 0) {
				$this->_error = true;
				$this->_msg = "That section link is already registered in the database and would cause a conflict. Please change the value of Section Link to another value and resubmit the form.";
				return false;
			}
			else {
				return true;
			}			
		}
		
		protected function _createNewSection() {
			//TODO implement transaction
//			$query = "INSERT INTO navigationSections
//					SET 
//						sectionNum = '".$this->_submit['sectionNum']."',
//						sectionName = '".$this->_sectionName."',
//						sectionFullName = '".$this->_submit['sectionFullName']."',
//						siteConfigID = '".$this->_url->siteConfig['siteConfigID']."',
//						sectionVisible = 'y'";

			$table = "navigationSections";
			
			$data = array(
				'sectionNum' => $this->_submit['sectionNum'],
				'sectionName' => $this->_sectionName,
				'sectionFullName' => $this->_submit['sectionFullName'],
				'siteConfigID' => $this->_url->siteConfig['siteConfigID'],
				'sectionVisible' => 'y'
			);
			
			$this->_db->insert($table, $data);
			
			if($this->_db->getAffectedRows() > 0) {
				$sectionOrderNum = parent::_sectionCount() + 1;
				$query2 = "INSERT INTO navigationSectionsOrder
						SET
							sectionNum = '".$this->_submit['sectionNum']."',
							navigationTypeID = '1',
							sectionOrderNum = '".$sectionOrderNum."'";
				
				$table2 = "navigationSectionsOrder";
			
				$data2 = array(
					'sectionNum' => $this->_submit['sectionNum'],
					'navigationTypeID' => '1',
					'sectionOrderNum' => $sectionOrderNum
				);
		
				$this->_db->insert($table2, $data2);
				
				if($this->_db->getAffectedRows() > 0) {
					$this->_msg = "Your new section has been created. You should now create a new page therein using the \"Create New Page\" option of the \"Section Options\" tab of the Admin Toolbar. It will automatically be set as the default landing page for the new section.";					
				}
				else {
					$this->_error = true;
					$this->_msg = "The new section has been created but an error occurred placing it in order with the rest of the sections. You may need to review the order of the navigation sections and make adjustments accordingly: ".$this->_db->getError();
				}
			}
			else {
				$this->_error = true;
				$this->_msg = "There was an error in the process of creating the new section. Please resubmit the form: ".$this->_db->getError();
			}
		}		
	}
	
	
	
	
	
	
	class SectionSettings_ModifySection extends SectionSettings {
			
		private $_sectionID;
		private $_sectionPages;

		public function __construct() {
			parent::__construct();
			$this->_permAbility = "Modify Section";
			
			$this->_formTpl = 'section_settings_edit.tpl.php';	
			
			if(isset($_GET['sectionID'])) {
				$this->_sectionID = $_GET['sectionID'];
			}
			
			if(isset($this->_submit) && $this->_checkSectionName()) {
				$this->_nextStep = 'finish';
				$this->_updateSection();
			}			
			
			$this->_setSectionInfo();
			$this->_setPagesInfo();	
			$this->_getSiteTemplates();			
			$this->_setFormData();
			parent::_setFormBody();			
		}

		private function _setFormData() {
			$this->_formData['sectionPages'] = $this->_sectionPages;
			$this->_formData['landingPageRecordNum'] = $this->_sectionInfo['landingPageRecordNum'];
			$this->_formData['sectionFullName'] = $this->_sectionInfo['sectionFullName'];
			$this->_formData['sectionName'] = $this->_sectionInfo['sectionName'];
			$this->_formData['sectionVisible'] = $this->_sectionInfo['sectionVisible'];
			$this->_formData['sectionNum'] = $this->_sectionInfo['sectionNum'];	
			$this->_formData['siteTemplates'] = $this->_siteTemplates;			
		}
		
		
		protected function _setSectionInfo() {
			$query = "SELECT * FROM navigationSections, navigationSectionsOrder
					WHERE navigationSections.sectionID = '".$this->_sectionID."'
						AND navigationSections.sectionNum = navigationSectionsOrder.sectionNum";
			$this->_db->setQuery($query);
			$this->_sectionInfo = $this->_db->getAssoc();
		}

		protected function _setPagesInfo() {
			$query = "SELECT contentRecords.contentTitle, contentRecords.contentRecordNum, contentRecords.orderNum, contentRecords.isVisible
					FROM contentRecords
						WHERE contentRecords.sectionID = '".$this->_sectionID."'
						ORDER BY orderNum";
			$this->_db->setQuery($query);
			while($result = $this->_db->getAssoc()) {
				$sectionPages[$result['contentRecordNum']] = $result['contentTitle'];
			}
			
			$options = new FormInputSelectOptionsCollection($sectionPages, $this->_sectionInfo['landingPageRecordNum']);
			$this->_sectionPages = $options->GetMarkup();			
		}	

		protected function _checkSectionName() {
			parent::_checkSectionName();
			$query = "SELECT COUNT(sectionID) AS rowCount FROM navigationSections WHERE sectionName = '".$this->_sectionName."' AND sectionID != '".$this->_sectionID."' AND siteConfigID = '".$this->_url->siteConfig['siteConfigID']."'";
			$this->_db->setQuery($query);
			$result = $this->_db->getAssoc();
			if($result['rowCount'] > 0) {
				$this->_error = true;
				$this->_msg = "That section link is already registered in the database for a different section and would cause a conflict. Please change the value of Section Link to another value and resubmit the form.";
				return false;
			}
			else {
				return true;
			}			
		}
		
		protected function _updateSection() {
//			$query = "UPDATE navigationSections
//				SET
//					sectionName = '".$this->_submit['sectionName']."',
//					sectionFullName = '".$this->_submit['sectionFullName']."',
//					sectionVisible = '".$this->_submit['sectionVisible']."',
//					landingPageRecordNum = '".$this->_submit['landingPageRecordNum']."'
//				WHERE
//					sectionNum = '".$this->_submit['sectionNum']."'";
			
			$table = "navigationSections";
			
			$data = array(
				'sectionName' => $this->_submit['sectionName'],
				'sectionFullName' => $this->_submit['sectionFullName'],
				'sectionVisible' => $this->_submit['sectionVisible'],
				'landingPageRecordNum' => $this->_submit['landingPageRecordNum'],
				'sectionSiteTemplate' => $this->_submit['sectionSiteTemplate']
			);
			
			$where = "sectionNum = '".$this->_submit['sectionNum']."'";
			
			$this->_db->update($table, $data, $where);
			
			if($this->_db->getAffectedRows() > 0) {
				$this->_msg = "Your changes to this section have been saved.";				
			}
			else {
				$this->_error = true;
				$this->_msg = "An error was encountered updating the settings for this section: " .$this->_db->getError();
			}
		}		
	}
	
?>
