<?php
/**
 * Description of Reorder
 *
 * @author vyn
 */

require_once './kibu/core/class/Template.php';
require_once './kibu/core/class/Utility.php';

class Reorder {

		private $_mode;
		private $_sectionID;
		private $_siteConfigID;
		private $_form;
		private $_itemsToReorder;
		private $_itemCount;
		private $_reorderOptions;
		private $_nextStep;
		private $_db;
		private $_auth;
		private $_submit;
		private $_msg;

		public function  __construct() {
				global $db;
				$this->_db = $db;

				global $auth;
				$this->_auth = $auth;
				
				$this->_checkAuth();

				if(isset($_GET['sectionID'])) {
						$this->_sectionID = $_GET['sectionID'];
						$this->_getPagesInfo();
				}
				if(isset($_GET['siteConfigID'])) {
						$this->_siteConfigID;
						$this->_getSectionsInfo();
				}

				$this->_setReorderOptions();
				
				$this->_mode = $_GET['mode'];

				if(isset($_POST['reorder'])) {
						$this->_submit = $_POST;
						$this->_doReorder();
				}
				else {
						$this->_nextStep = 'finish';
						$this->_reorderForm();
				}
		}

		private function _checkAuth() {
				if($this->_auth->getUserLevel() < 100) {
						header("Location:/");
				}
		}

		public function outputFormBody() {
				return $this->_form;
		}

		public function getMsg() {
				return $this->_msg;
		}

		public function getNextStep() {
				return $this->_nextStep;
		}


		private function _getSectionsInfo() {
				$sections = Utility::getSections();
				foreach($sections as $section) {
						$this->_itemsToReorder[$section['sectionNum']]['name'] = $section['sectionFullName'];
						$this->_itemsToReorder[$section['sectionNum']]['orderNum'] = $section['sectionOrderNum'];
						$this->_itemsToReorder[$section['sectionNum']]['isVisible'] = $section['sectionVisible'];
				}
				$this->_itemCount = count($this->_itemsToReorder);
		}

		private function _getPagesInfo() {
				$pages = Utility::getSectionPages($this->_sectionID);
				foreach($pages as $page) {
						$this->_itemsToReorder[$page['contentRecordNum']]['name'] = $page['contentTitle'];
						$this->_itemsToReorder[$page['contentRecordNum']]['orderNum'] = $page['orderNum'];
						$this->_itemsToReorder[$page['contentRecordNum']]['isVisible'] = $page['isVisible'];
				}
				$this->_itemCount = count($this->_itemsToReorder);
		}

		private function _setReorderOptions(){
				for($option = 1; $option <= $this->_itemCount; $option++) {
						$this->_reorderOptions[$option] = $option;
				}
		}

		private function _reorderForm() {
				$reorderTpl = new Template('./kibu/templates/');
				$reorderTpl->set("reorderName", $this->_mode);
				$reorderTpl->set("itemsToReorder", $this->_itemsToReorder);
				$reorderTpl->set("reorderOptions", $this->_reorderOptions);
				$this->_form = $reorderTpl->fetch('reorder_page_section.tpl.php');
		}

		private function _doReorder() {
				foreach($this->_submit['reorder'] as $itemNum => $itemOrder) {
						if($this->_mode == 'pages') {
								$query = "UPDATE contentRecords SET orderNum = '".$itemOrder."' WHERE contentRecordNum = '".$itemNum."' LIMIT 1";
						}
						elseif($this->_mode == 'sections') {
								$query = "UPDATE navigationSectionsOrder SET sectionOrderNum = '".$itemOrder."' WHERE sectionNum = '".$itemNum."' LIMIT 1";
						}
						$this->_db->setQuery($query);
				}
				$this->_msg = "Reorder of ".$this->_mode." successful.";
		}
}
?>
