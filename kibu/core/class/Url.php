<?php
	/**
	 * A class designed to manipulate and utilize pieces of URL/URI
	 *
	 *
	 * @package kibu
	 * @author Vyn Raskopf
	 * @copyright kibu 2009
	 * @version 1.0.0
	 */

	class URL {

		public $_scheme;
		public $_host;
		public $_path;
		public $_query;
		public $_fragment;
		public $_filename;
		public $_extension;
		public $_baseURL;
		public $_referringURL;
		public $_section;
		public $_urlArray = array();
		public $_pathArray = array();
		public $_domainArray = array();
		public $_URLPath;
		public $_fullURL;
		public $_mode;
		public $siteConfig;
		public $configured;
		protected $_cookieDomain;

		/*
		 *
		 */

		public function __construct() {
			$baseURL = $_SERVER['HTTP_HOST']; // create variable that holds domain name information
			$URLPath = $_SERVER['REQUEST_URI']; // create variable that holds file path information
			$fullURL = "http://$baseURL"."$URLPath"; // create variable that holds both of the above (recreates entire URL in variable form)

			$this->_baseURL = $baseURL;
			$this->_URLPath = $URLPath;
			$this->_fullURL = $fullURL;
			$this->setURLArray();
			$this->setURLParts();
			$this->setPathArray();
			$this->setDomainArray();
			$this->setFilename();
			$this->setSection();
			$this->setExtension();
			$this->setMode();
			$this->setReferring();
			$this->getSiteConfig();
		}

		/*
		 *
		 */

		public function getSiteConfig(){
				global $db;
				$domainArrayRev = array_reverse($this->_domainArray);
				$baseDomain = $domainArrayRev[1];
				if($db->connected) {
						$query = "Select * FROM siteConfig WHERE siteBaseDomain = '$baseDomain'";
						$query = $db->setQuery($query);
						if($db->getNumRows() > 0) {
								$this->configured = true;
								$this->siteConfig = $db->getAssoc($query);
						}
						else {
								$this->configured = false;
						}
				}
		}

		public function setURLArray() {
			$url = parse_url($this->_fullURL);
			$this->_urlArray = $url;
		}



		public function getURLArray() {
			return $this->_urlArray;
		}

		/*
		 *
		 */

		public function setURLParts() {
			$urlArray = $this->_urlArray;
			foreach($urlArray as $key => $value) {
				$key = "_$key";
				$this->$key = $value;
			}
		}

		/*
		 *
		 */

		public function setDomainArray() {
			$this->_domainArray = explode('.', $this->_host);
		}

		public function getDomainArray() {
			return $this->_domainArray;
		}

		/*
		 *
		 */

		public function setPathArray() {
			$url = parse_url($this->_fullURL);
			$this->_pathArray = pathinfo($url['path']);
		}

		/*
		 *
		 */

		public function setFilename() {
			$patharray = $this->_pathArray;
			if(!isset($patharray['filename'])) { // fix for php < ver. 5.x.x, which first introduces 'filename' node to array generated by pathinfo()
				$patharray['filename'] = substr($patharray['basename'], 0, strrpos($patharray['basename'], '.'));
			}
			$this->_filename = $patharray['filename'];
		}

		/*
		 *
		 */

		public function setExtension() {
			$patharray = $this->_pathArray;
			if(isset($patharray['extension'])) {
				$this->_extension = $patharray['extension'];
			}
			else {
				$this->_extension = 'html';
			}
		}

		/*
		 *
		 */

		public function getBaseURL() {
			$this->_baseURL = $_SERVER['HTTP_HOST'];
		}

		/*
		 *
		 */

		public function setReferring() {
			if(isset($_SERVER['HTTP_REFERER'])) {
				$this->_referringURL = $_SERVER['HTTP_REFERER'];
			}
		}

		public function getReferring() {
			return $this->_referringURL;
		}

		/*
		 *
		 */

		public function setSections() {
			$patharray = $this->_Path;
			$this->_PathArray = array_reverse(explode('/', $patharray['dirname']));
		}

		/*
		 *
		 */

		public function setSection() {
			$patharray = $this->_pathArray;
			$this->_section = str_replace('/', '', $patharray['dirname']);
		}

		/*
		 *
		 */

		public function setMode() {
			if(isset($_GET['mode'])) {
				$this->_mode = $_GET['mode'];
			}
			else {
				$this->_mode = $this->_extension;
			}
		}

		public function getVar($var) {
			return $this->$var;
		}

		/*
		 *
		 */


		public function getMode() {
			return $this->_mode;
		}

		public function setCookieDomain() {
			if((isset($this->_domainArray[1])) && (isset($this->_domainArray[2]))) { // if live production site or beta site (testing for at least two distinct array nodes in the domain), build domain for use in cookie
				$cookieDomain = ".".$this->_domainArray[1].".".$this->_domainArray[2]."";
			}
			else {
				$cookieDomain = $this->_domainArray[0];
			}
			$this->_cookieDomain = $cookieDomain;
		}

		public function getCookieDomain() {
				return $this->_cookieDomain;
		}

		public function getCurPage() {
				$curPage = $this->_filename.'.'.$this->_extension;
				return $curPage;
		}
	}

?>