<?php
	/**
	 * A class designed to facilitate output of site navigation hierarchy
	 *
	 *
	 * @package Kibu
	 * @author Vyn Raskopf
	 * @copyright Kibu 2010
	 * @version 1.0.0.2
	 */

	require_once './kibu/core/class/Url.php';
	require_once './kibu/core/class/Template.php';

	class Navigation extends URL {

		private $_allNodes = array();
		private $_globalNav;
		public $_currentSectionID;
		private $_currentNode;
		private $_currentNav;
		private $_getHiddenSections;
		private $_getHiddenPages;
		private $_navigationType;
		private $_siteConfigID;

		protected $_globalNavNodes;
		protected $_selectedNode;
		protected $_selectedNodeParent;

		public $_path;
		public $_query;
		public $_fragment;
		public $_filename;
		public $_extension;
		public $_section;

		public function __construct($siteConfig, $currentSectionID, $navigationType = 'Top', $getHiddenSections = false, $getHiddenPages = false) {
			parent::__construct();
			global $content;
			global $constants;
			$this->_siteConfigID = $siteConfig['siteConfigID'];
			$this->_navigationType = $navigationType;
			$this->_getHiddenSections = $getHiddenSections;
			$this->_getHiddenPages = $getHiddenPages;
			$this->_setSelectedNodes();
			$this->setGlobalNav($this->_navigationType, $this->_getHiddenSections);
			$this->setCurrentNode($currentSectionID, $this->_getHiddenPages);
			$this->setCurrentNav();
		}

		protected function _getVar($var) {
			return $this->$var;
		}

		protected function _setSelectedNodes() {
			$this->_selectedNode = parent::getVar('_filename');
			$this->_selectedNodeParent = parent::getVar('_section');
		}

		protected function _getSelectedNode() {
			return $this->_selectedNode;
		}

		protected function _getSelectedNodeParent() {
			return $this->_selectedNodeParent;
		}

		private function setAllNodes() {
			global $db;
			$query = "SELECT navigationTypes.*, navigationSectionsOrder.sectionOrderNum, navigationSections.sectionName, navigationSections.sectionFullName, contentRecords.contentTitle, contentRecords.titleClean
						FROM navigationTypes, navigationSectionsOrder, navigationSections
						LEFT JOIN contentRecords ON navigationSections.sectionID = contentRecords.sectionID
								AND navigationSections.sectionNum = navigationSectionsOrder.sectionNum
								AND navigationTypes.navigationTypeID = navigationSectionsOrder.navigationTypeID
						ORDER BY navigationSectionsOrder.sectionOrderNum ASC";
			$db->setQuery($query);
			while($assoc = $db->getAssoc()) {
				foreach($assoc as $section) {
					echo $section;
					foreach($section as $sectionName) {
						echo $sectionName['contentTitle'].'<br />';
					}
				}
				$this->_allNodes[] = $assoc;
			}
			print_r($this->_allNodes);

		}

		public function setGlobalNav($getHidden = false) {
			global $content;
			global $db;
			$query = "SELECT navigationTypes.*, navigationSectionsOrder.sectionOrderNum, navigationSections.*, contentRecords.titleClean, contentRecords.contentRecordNum
						FROM navigationTypes, navigationSectionsOrder, navigationSections, contentRecords
						WHERE navigationSectionsOrder.sectionNum = navigationSections.sectionNum
							AND contentRecords.sectionID = navigationSections.sectionID
							AND contentRecords.siteConfigID = $this->_siteConfigID
							AND navigationTypes.navigationTypeID = navigationSectionsOrder.navigationTypeID
							AND navigationTypes.navigationTypeName = '$this->_navigationType'
							AND contentRecords.contentRecordNum = navigationSections.landingPageRecordNum";
			if($getHidden == false) {
					$query .= " AND navigationSections.sectionVisible = 'y'";
			}
			$query .= " GROUP BY navigationSections.sectionID ORDER BY navigationSectionsOrder.sectionOrderNum ASC";
			$query = $db->setQuery($query);
			while($result = $db->getAssoc($query)) {
				$globalNavNodes[] = $result;
			}
			$this->_globalNavNodes = $globalNavNodes;
		}

		protected function getGlobalNavNodes() {
			return $this->_globalNavNodes;
		}


		protected function setCurrentNode($sectionID, $getHidden = false) {
			global $db;
			$query = "SELECT contentRecords.titleClean, contentRecords.contentTitle, contentRecords.isVisible, contentRecords.contentRecordNum, navigationSections.*
						FROM navigationSections, contentRecords WHERE navigationSections.sectionID = '" . $sectionID . "'
							AND contentRecords.sectionID = navigationSections.sectionID
							AND contentRecords.siteConfigID = $this->_siteConfigID";
			if($getHidden == false) {
					$query .= " AND isVisible = 'vis'";
			}
			$query .= " ORDER BY contentRecords.orderNum ASC";
			$query = $db->setQuery($query);
			if($db->getNumRows() > '1') {
				while($nav = $db->getAssoc($query)) {
					$currentNode[] = $nav;
				}
				$this->_currentNode = $currentNode;
			}
			else {
				$this->_currentNode = NULL;
			}
		}

		protected function getCurrentNode(){
			return $this->_currentNode;
		}

		protected function setCurrentNav() {
			if(is_array($this->_currentNode)) {
				foreach($this->_currentNode as $key => $node) {
					if(parent::getVar('_filename') == $node['titleClean']) {
						$this->_currentNode[$key]['class'] = "localnavselected";
					}
					else {
						$this->_currentNode[$key]['class'] = "localnav";
					}
				}
				$currentNavTpl = new Template('./kibu/templates/');
				$currentNavTpl->set('localNode', $this->_currentNode);
				$this->_currentNav = $currentNavTpl->fetch('local_nav.tpl.php');
			}
			else {
				$this->_currentNav = null;
			}
		}

		public function getGlobalNav($navigationTypeID = '1') {
			$globalNav = "<ul id=\"globalNav\">\n";
			foreach($this->_globalNavNodes as $node => $result) {
				if($result['sectionVisible'] == 'y'){
						if(parent::getVar('_section') == $result['sectionName']) {
						$class = "globalnavselected";
					}
					else {
						$class = "globalnav";
					}
					$globalNav .= "\t\t\t<li class=\"$class\"><a class=\"".$class."link\" href=\"/".$result['sectionName']."/".$result['titleClean'].".html\">".$result['sectionFullName']."</a></li>\n";
				}
			}
			$globalNav .= "</ul>";
			$this->_globalNav = $globalNav;
			return $this->_globalNav;
		}

		public function getCurrentNav() {
			return $this->_currentNav;
		}
	}
?>