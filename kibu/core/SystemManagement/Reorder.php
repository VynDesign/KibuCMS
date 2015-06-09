<?php
/**
 * Description of Reorder
 *
 * @author vyn
 */

	require_once './kibu/core/SystemManagement/SystemManagement.php';

	class Reorder extends SystemManagement {

		private $_sectionID;
		private $_siteConfigID;
		private $_itemsToReorder;
		private $_itemCount;
		private $_reorderOptions;

		public function  __construct() {
			parent::__construct();
			parent::_checkAuth();

			if(isset($_GET['sectionID'])) {
				$this->_sectionID = $_GET['sectionID'];
				$this->_getPagesInfo();
			}
			if(isset($_GET['siteConfigID'])) {
				$this->_siteConfigID;
				$this->_getSectionsInfo();
			}

			$this->_mode = $_GET['mode'];

			if(isset($_POST['reorder'])) {
				$this->_submit = $_POST;
				$this->_doReorder();
				$this->_nextStep = 'finish';
			}
			else {
				$this->_nextStep = 'save';
				$this->_reorderForm();
			}
		}


		private function _getSectionsInfo() {
			$sections = Utility::getSections();
			foreach($sections as $section) {
				$this->_itemsToReorder[$section['sectionNum']]['name'] = $section['sectionFullName'];
				$this->_itemsToReorder[$section['sectionNum']]['orderNum'] = $section['sectionOrderNum'];
				$this->_itemsToReorder[$section['sectionNum']]['isVisible'] = $section['sectionVisible'];
			}
			$this->_itemCount = count($this->_itemsToReorder);
			$this->_formatNames();
			$this->_setReorderOptions();
		}

		private function _getPagesInfo() {
			$pages = Utility::getSectionPages($this->_sectionID);
			foreach($pages as $page) {
				$this->_itemsToReorder[$page['contentRecordNum']]['name'] = $page['contentTitle'];
				$this->_itemsToReorder[$page['contentRecordNum']]['orderNum'] = $page['orderNum'];
				$this->_itemsToReorder[$page['contentRecordNum']]['isVisible'] = $page['isVisible'];
			}
			$this->_itemCount = count($this->_itemsToReorder);
			$this->_formatNames();
			$this->_setReorderOptions();			
		}
		
		private function _formatNames() {
			foreach($this->_itemsToReorder as $item => $data) {
				if($data['isVisible'] == 'n' || $data['isVisible'] == 'invis' || $data['isVisible'] == 'inac'){
					$this->_itemsToReorder[$item]['name'] = "<em>".$data['name']." (hidden)</em>";
				}
				
			}
			
		}

		private function _setReorderOptions(){
			for($option = 1; $option <= $this->_itemCount; $option++) {
				$this->_reorderOptions[$option] = $option;
			}
			
			foreach($this->_itemsToReorder as $item => $data) {
				$orderOpts = new FormInputSelectOptionsCollection($this->_reorderOptions, $data['orderNum']);
				$this->_itemsToReorder[$item]['orderOptions'] = $orderOpts->GetMarkup();
			}
		}
		
		
		
		
		protected function _setZoneOrderOptions() {
			foreach($this->_zoneAssets as $zone => $assets) {				
				$optionCount = count($assets);
				foreach($assets as $asset) {
					for($i = 1; $i <= count($assets); $i++) {
						$this->_zoneAssets[$zone][$asset['assetID']]['orderOptions'][$i] = $i;
					}
				}			
			}
			
			foreach($this->_zoneAssets as $zone => $assets) {
				foreach($assets as $asset) {
					$options = new FormInputSelectOptionsCollection($asset['orderOptions'], $asset['assetOrderNum']);
					$this->_zoneAssets[$zone][$asset['assetID']]['orderOptions'] = $options->GetMarkup();
				}
			}			
		}		

		private function _reorderForm() {			
			$reorderTpl = new Template('./kibu/core/SystemManagement/templates/');
			$reorderTpl->set("reorderName", $this->_mode);
			$reorderTpl->set("itemsToReorder", $this->_itemsToReorder);
			$reorderTpl->set("reorderOptions", $this->_reorderOptions);
			$this->_form = $reorderTpl->fetch('reorder_page_section.tpl.php');
		}

		private function _doReorder() {
			foreach($this->_submit['reorder'] as $itemNum => $itemOrder) {
				//TODO implement transaction
				if($this->_mode == 'pages') {
					//$query = "UPDATE contentRecords SET orderNum = '".$itemOrder."' WHERE contentRecordNum = '".$itemNum."' LIMIT 1";
					$table = "contentRecords";
					$data = array('orderNum' => $itemOrder);
					$where = "contentRecordNum = '".$itemNum."' LIMIT 1";
				}
				elseif($this->_mode == 'sections') {
					//$query = "UPDATE navigationSectionsOrder SET sectionOrderNum = '".$itemOrder."' WHERE sectionNum = '".$itemNum."' LIMIT 1";
					$table = "navigationSectionsOrder";
					$data = array('sectionOrderNum' => $itemOrder);
					$where = "sectionNum = '".$itemNum."' LIMIT 1";
				}
				//$this->_db->setQuery($query);				
				$this->_db->update($table, $data, $where);
			}
			if(!$this->_db->error) {
				$this->_msg = "Reorder of ".$this->_mode." successful.";
			}
			else {
				$this->_error = true;
				$this->_msg = "Reorder of ".$this->_mode." failed: " . $this->_db->getError();
				
			}
				
		}
}
?>
