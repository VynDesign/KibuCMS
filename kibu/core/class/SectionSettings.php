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

require_once './kibu/core/class/Template.php';
require_once './kibu/core/class/Utility.php';

 class SectionSettings {

		private $_mode;
		private $_db;
		private $_url;
		private $_auth;
		private $_form;
		private $_nextStep;
		private $_msg;
		private $_submit;
		private $_sectionID;
		private $_sectionPages;
		private $_sectionInfo;
		private $_sectionName;

		public function __construct() {

				global $db;
				$this->_db = $db;

				global $url;
				$this->_url = $url;

				global $auth;
				$this->_auth = $auth;

				$this->_checkAuth();
				
				if(isset($_GET['sectionID'])) {
						$this->_sectionID = $_GET['sectionID'];
				}

				$this->_mode = $_GET['mode'];

				if(isset($_POST['submit'])) {
						$this->_submit = $_POST;
						if($this->_checkSectionName()) {
								if($this->_mode == 'createsection') {
										$this->_nextStep = 'finish';
										$this->_createNewSection();
								}
								elseif($this->_mode == 'sectionsettings') {
										$this->_nextStep = 'finish';
										$this->_updateSection();
								}
						}
						else {
								$this->_formBody();
						}
				}
				else {
						$this->_formBody();
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
		
		private function _formBody() {
				$formTpl = new Template('./kibu/templates/');
			
				if($this->_mode == 'sectionsettings') {
						$this->_setSectionInfo();
						$this->_setPagesInfo();
						$tplVars = array(
										'sectionPages' => $this->_sectionPages,
										'landingPageRecordNum' => $this->_sectionInfo['landingPageRecordNum'],
										'sectionFullName' => $this->_sectionInfo['sectionFullName'],
										'sectionName' => $this->_sectionInfo['sectionName'],
										'sectionVisible' => $this->_sectionInfo['sectionVisible'],
										'sectionNum' => $this->_sectionInfo['sectionNum']
						);
						$templateFile = 'section_settings_edit.tpl.php';
				}
				elseif($this->_mode == 'createsection') {
						if(isset($this->_submit)) {
								$tplVars = $this->_submit;
						}
						else {
								$tplVars = array(
										'sectionPages' => null,
										'landingPageRecordNum' => null,
										'sectionFullName' => null,
										'sectionName' => null,
										'sectionVisible' => null,
										'sectionNum' => Utility::guidGen()
								);
						}
						$templateFile = 'section_settings_create.tpl.php';
				}

				$formTpl->set_vars($tplVars, true);
				$this->_form = $formTpl->fetch($templateFile);
		}

		private function _setSectionInfo() {
				$query = "SELECT * FROM navigationSections, navigationSectionsOrder
						WHERE navigationSections.sectionID = '".$this->_sectionID."'
								AND navigationSections.sectionNum = navigationSectionsOrder.sectionNum";
				$this->_db->setQuery($query);
				$this->_sectionInfo = $this->_db->getAssoc();
		}

		private function _setPagesInfo() {
				$query = "SELECT contentRecords.contentTitle, contentRecords.contentRecordNum, contentRecords.orderNum, contentRecords.isVisible
						FROM contentRecords
								WHERE contentRecords.sectionID = '".$this->_sectionID."'
										ORDER BY orderNum";
				$this->_db->setQuery($query);
				while($result = $this->_db->getAssoc()) {
						$this->_sectionPages[$result['contentRecordNum']] = $result['contentTitle'];
				}
		}

		protected function _createNewSection() {
				$query = "INSERT INTO navigationSections
						SET 
								sectionNum = '".$this->_submit['sectionNum']."',
								sectionName = '".$this->_sectionName."',
								sectionFullName = '".$this->_submit['sectionFullName']."',
								siteConfigID = '".$this->_url->siteConfig['siteConfigID']."',
								sectionVisible = 'y'";

					$sectionOrderNum = $this->_sectionCount() + 1;
					$query2 = "INSERT INTO navigationSectionsOrder
							SET
								sectionNum = '".$this->_submit['sectionNum']."',
								navigationTypeID = '1',
								sectionOrderNum = '".$sectionOrderNum."'";


					$this->_db->setQuery($query);
					$this->_db->setQuery($query2);
					
				$this->_msg = "Your new section has been created. You should now create a new page therein using the \"Create New Page\" option of the \"Section Options\" tab of the Admin Toolbar. It will automatically be set as the default landing page for the new section.";

		}

		protected function _updateSection() {
				$query = "UPDATE navigationSections
						SET
								sectionName = '".$this->_submit['sectionName']."',
								sectionFullName = '".$this->_submit['sectionFullName']."',
								sectionVisible = '".$this->_submit['sectionVisible']."',
								landingPageRecordNum = '".$this->_submit['landingPageRecordNum']."'
						WHERE
								sectionNum = '".$this->_submit['sectionNum']."'";
					$this->_db->setQuery($query);

				$this->_msg = "Your changes to this section have been saved.";

		}

		private function _checkSectionName() {
				if($this->_submit['sectionName'] != null) {
						$sectionName = $this->_submit['sectionName'];
						$sectionName = Utility::stripChars($sectionName);
				}
				else {
						$sectionName = $this->_submit['sectionFullName'];
						$sectionName = Utility::stripChars($sectionName);
				}
				if($this->_mode == 'sectionsettings') {
						$this->_submit['sectionName'] = $sectionName;
						return true;
				}
				elseif($this->_mode == 'createsection') {
						$checkNameQuery = "SELECT COUNT(sectionID) AS rowCount FROM navigationSections WHERE sectionName = '".$sectionName."' AND siteConfigID = '".$this->_url->siteConfig['siteConfigID']."'";
						$this->_db->setQuery($checkNameQuery);
						$result = $this->_db->getAssoc();
						if($result['rowCount'] > 0) {
								$this->_msg = "That section link is already registered in the database and would cause a conflict. Please change the value of Section Link to something else.";
								return false;
						}
						else {
								$this->_sectionName = $sectionName;
								return true;
						}
				}
		}

		private function _sectionCount() {
				$query = "SELECT COUNT(sectionID) AS rowCount FROM navigationSections WHERE siteConfigID = '".$this->_url->siteConfig['siteConfigID']."'";
				$this->_db->setQuery($query);
				$result = $this->_db->getAssoc();
				return $result['rowCount'];
		}
 }

?>
