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

	require_once './kibu/core/framework/template/Template.php';
	require_once './kibu/core/Navigation/Url.php';
	require_once './kibu/core/framework/html/HtmlList.php';

	class Navigation extends HtmlList {

		private $_url;
		private $_allNodes = array();
		private $_globalNav;
		private $_currentNav;
		private $_getHiddenSections;
		private $_getHiddenPages;
		private $_navigationType;
		private $_siteConfigID;

		protected $section;
		protected $filename;
		protected $globalNode;
		protected $localNode;		
		protected $selectedNode;
		protected $selectedNodeParent;


		public function __construct($navigationType = 'Top', $getHiddenSections = false, $getHiddenPages = false) {
			parent::__construct();
			$this->_url = new URL_ext();
			$this->_siteConfigID = $this->_url->siteConfig['siteConfigID'];
			$this->_navigationType = $navigationType;
			$this->_getHiddenSections = $getHiddenSections;
			$this->_getHiddenPages = $getHiddenPages;
			$this->section = $this->_url->section;
			$this->filename = $this->_url->filename;
			$this->_setSelectedNodes();
			$this->setAllNodes();
			$this->setGlobalNav($this->_navigationType, $this->_getHiddenSections);
			$this->setCurrentNode($this->_url->section, $this->_getHiddenPages);
			$this->setCurrentNav();
		}
		
		public function __get($name) {
			return $this->$name;
		}

		protected function _setSelectedNodes() {
			$this->selectedNode = $this->_url->filename;
			$this->selectedNodeParent = $this->_url->section;
		}

		private function setAllNodes() {
			$query = "SELECT navigationTypes.*, navigationSectionsOrder.sectionOrderNum, navigationSections.*, contentRecords.contentRecordNum, contentRecords.contentTitle, contentRecords.titleClean, contentRecords.isVisible
						FROM navigationTypes, navigationSectionsOrder, navigationSections
						LEFT JOIN contentRecords ON navigationSections.sectionID = contentRecords.sectionID
							WHERE navigationSections.sectionNum = navigationSectionsOrder.sectionNum
								AND navigationTypes.navigationTypeID = navigationSectionsOrder.navigationTypeID
						ORDER BY navigationSectionsOrder.sectionOrderNum ASC, contentRecords.orderNum ASC";
			$this->_url->_db->setQuery($query);
			while($assoc = $this->_url->_db->getAssoc()) {
				$this->_allNodes[$assoc['sectionNum']] = $assoc;
//				foreach($assoc as $section) {
//					echo $section;
//					foreach($section as $sectionName) {
//						echo $sectionName['contentTitle'].'<br />';
//					}
//				}
//				$this->_allNodes[] = $assoc;
			}

		}

		public function setGlobalNav($getHidden = false) {
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
			$this->_url->_db->setQuery($query);
			while($result = $this->_url->_db->getAssoc()) {
				$globalNavNodes[] = $result;
			}
			$this->globalNode = $globalNavNodes;
		}

		public function getGlobalNavNodes() {
			return $this->globalNode;
		}


		protected function setCurrentNode($sectionName = null, $getHidden = false) {
			if($sectionName == null) {
				$sectionName = $this->section;
			}
			$query = "SELECT contentRecords.titleClean, contentRecords.contentTitle, contentRecords.isVisible, contentRecords.contentRecordNum, navigationSections.*
						FROM navigationSections, contentRecords WHERE navigationSections.sectionName = '" . $sectionName . "'
							AND contentRecords.sectionID = navigationSections.sectionID
							AND contentRecords.siteConfigID = $this->_siteConfigID";
			if($getHidden == false) {
					$query .= " AND isVisible = 'vis'";
			}
			$query .= " ORDER BY contentRecords.orderNum ASC";
			$this->_url->_db->setQuery($query);
			if($this->_url->_db->getNumRows() > 0) {
				while($nav = $this->_url->_db->getAssoc()) {
					$currentNode[] = $nav;
				}
				$this->localNode = $currentNode;
			}
			else {
				$this->localNode = NULL;
			}
		}

		public function getCurrentNode(){
			return $this->localNode;
		}

		protected function setCurrentNav() {
			if(is_array($this->localNode)) {
				foreach($this->localNode as $key => $node) {
					if($this->_url->filename == $node['titleClean']) {
						$this->localNode[$key]['class'] = "localnavselected";
					}
					else {
						$this->localNode[$key]['class'] = "localnav";
					}
				}
				$currentNavTpl = new Template('./kibu/core/templates/');
				$currentNavTpl->set('localNode', $this->localNode);
				$this->_currentNav = $currentNavTpl->fetch('local_nav.tpl.php');
			}
			else {
				$this->_currentNav = null;
			}
		}

		public function getGlobalNav($navigationTypeID = '1') {
			$globalNav = "<ul id=\"globalNav\">\n";
			foreach($this->globalNode as $node => $result) {
				if($result['sectionVisible'] == 'y'){
						if($this->section == $result['sectionName']) {
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