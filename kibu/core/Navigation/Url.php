<?php

	//namespace kibu\core {
	 
	require_once './kibu/core/framework/url/Url.php';

	class URL_ext extends Url {

		public $mode;
		public $siteConfig;
		public $configured;
		public $section;
		protected $_db;
		protected $_cookieDomain;

		public function __construct() {
			parent::__construct();
			global $db;
			$this->_db = $db;
			$this->setMode();
			$this->getSiteConfig();	
			$sectionParts = $this->dirnameParts;
			$this->section = array_pop($sectionParts);
		}

		public function getSiteConfig(){
			if($this->_db->connected) {
				$query = "Select * FROM siteConfig WHERE siteBaseDomain = '$this->domain'";
				$this->_db->setQuery($query);
				if($this->_db->getNumRows() > 0) {
					$this->configured = true;
					$this->siteConfig = $this->_db->getAssoc();
				}
				else {
					$this->configured = false;
				}
			}
		}

		public function setMode() {
			if(isset($_GET['mode'])) { $this->mode = $_GET['mode']; }
			else { $this->mode = 'html'; }
		}

		public function getMode() {
			return $this->mode;
		}

		public function setCookieDomain() {
			if((isset($this->domainParts[1])) && (isset($this->domainParts[2]))) { // if live production site or beta site (testing for at least two distinct array nodes in the domain), build domain for use in cookie
				$cookieDomain = ".".$this->domainParts[1].".".$this->domainParts[2]."";
			}
			else {
				$cookieDomain = $this->domainParts[0];
			}
			$this->_cookieDomain = $cookieDomain;
		}

		public function getCookieDomain() {
			return $this->_cookieDomain;
		}

		public function getCurPage() {
			return $this->basename;
		}
	}
	//}
?>