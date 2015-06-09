<?php
	/**
	 * A class designed to set and retrieve user data
	 *
	 *
	 * @package kibu
	 * @author Vyn Raskopf
	 * @copyright kibu 2009
	 * @version 1.0.0
	 */

	require_once './kibu/core/framework/data/Cookie.php';
	require_once './kibu/core/framework/template/Template.php';
	require_once './kibu/core/framework/html/form/Form.php';
	 
	class Authentication extends Form {
		
		protected $_userName = 'Guest';
		protected $_password = '';
		protected $_userID = '0';
		protected $_userGUID;
		protected $_userLevel = '0';
		protected $_userInfo = array();
		protected $_pageLevel = '0';
		protected $_permissions;
		protected $_pathArray;

		public $cookiePrefix;
		public $uCookie;
		public $pCookie;
		public $uCookieSuffix;
		public $pCookieSuffix;
		public $reauthenticate = true;


		public function __construct() {
			parent::__construct();
			global $url;
			$this->setCookieNames($url->siteConfig);
			$this->setUserName();
			$this->setPassword();
			$this->setUserInfo();
			$this->setUserLevel();
			$this->setPermissions();
		}

		protected function setPermissions(){
			global $url;
			$query = "SELECT contentRecords.visitorAuthLevel, contentRecords.editorAuthLevel
					FROM contentRecords, navigationSections
					WHERE contentRecords.titleClean = '". $url->filename ."'
							AND navigationSections.sectionName = '" . $url->section . "'";
			$this->_db->setQuery($query);
			$assoc = $this->_db->getAssoc();
			$this->_pageLevel = $assoc['visitorAuthLevel'];
			if($this->reauthenticate == true) { // if we have to reauthenticate for this page...
				header("Location:http://".$_SERVER['HTTP_HOST']."/home/login.html?redirect=" . $url->path.""); // redirect to system login page
			}
		}

		/// TODO - rewrite authentication routines to utilize session cookie for additional security
		protected function setCookieNames($siteConfig) {
			$this->cookiePrefix = $siteConfig['cookiePrefix'];
			$this->uCookieSuffix = 'userName';
			$this->pCookieSuffix = 'password';			
		}
		
		protected function setUserName() {
			if(isset($_COOKIE[$this->cookiePrefix.'_userName'])) {
				$this->uCookie = $this->cookiePrefix.'_userName';
				$this->_userName = $_COOKIE[$this->uCookie];
			}
		}
		
		public function getUserName() {
			return $this->_userName;
		}
		
		public function getUserGUID() {
			return $this->_userGUID;
		}

		protected function setPassword() {
			if(isset($_COOKIE[$this->cookiePrefix.'_password'])) {
				$this->pCookie = $this->cookiePrefix.'_password';
				$this->_password = $_COOKIE[$this->pCookie];
			}	
			$this->reauthenticate = false;
		}

		public function reauthenticate() {
			if($this->_pageLevel > 0) {
				if((isset($_COOKIE[$this->uCookie])) && (isset($_COOKIE[$this->pCookie])) && (!$this->checkPass())) {
					global $pCookie;
					$pCookie->eatCookie();
					$this->reauthenticate = true;
				}
				elseif((isset($_COOKIE[$this->uCookie])) && (!isset($_COOKIE[$this->pCookie]))) {
					$this->reauthenticate = true;
				}
				elseif(!isset($_COOKIE[$this->uCookie])) {
					$this->reauthenticate = true;
				}
			}
			$this->reauthenticate = false;
		}

		public function getPassword() {
			return $this->_password;
		}

		private function setUserInfo() {
			if($this->_password == null) {
				$query = "SELECT userRecords.*, userLevels.levelName FROM userRecords, userLevels where userName = '" . $this->_userName . "' AND userLevels.levelNum = userRecords.userLevelNum";
			}
			else {
				$query = "SELECT userRecords.*, userLevels.levelName FROM userRecords, userLevels where userName = '" . $this->_userName . "' AND password = '" . $this->_password . "' AND userLevels.levelNum = userRecords.userLevelNum";
			}
		   	$this->_db->setQuery($query);
			if($this->_db->getNumRows() > '0') {
				$this->_userInfo = $this->_db->getAssoc();
				$this->_userID = $this->_userInfo['userID'];
				$this->_userGUID = $this->_userInfo['userGUID'];
			}
			else {
				$this->_userInfo = null;
			}
		}			
		
		protected function setUserByEmail($email) {
			$query = "SELECT userID, userName, password FROM userRecords WHERE emailAddress = '".$emailAddress."'"; // get userId, userName, and original password from database where the email address is the same as the posted form data
			$this->_db->setQuery($query);
			$this->_userInfo = $this->_db->getAssoc();
			if($this->_db->getNumRows() > '0') {
				$this->_userID = $this->_userInfo['userID'];
				return true;
			}
		}

		public function getUserInfo() {
			return $this->_userInfo;
		}

		public function setUserLevel() {
			$userInfo = $this->_userInfo;
			if(is_null($userInfo['userLevelNum'])) {
				$this->_userLevel = 0;
			}
			else {
				$this->_userLevel = $userInfo['userLevelNum'];
			}
		}

		public function getUserLevel() {
			return $this->_userLevel;
		}

		public function getUserID() {
			return $this->_userID;
		}

		//
		public function setLogin($redirect = null) {
			if(isset($this->uCookie)) {
				$this->_userName = $_COOKIE[$this->uCookie];
			}			
			elseif(isset($_POST['userName'])) {
				$this->_userName = $_POST['userName']; // store the POST variable username as a more manageable variable
			}
			if($this->checkPass()) {
				if($this->_userName == NULL) {  // This makes sure they filled all the required fields and the userName has a member record
					$this->_error = true;
					$this->_msg = "You failed to supply information for a required field.";	
					return false;
				}
				else {
					$this->_userName = stripslashes($this->_userName);
					$uCookie = new Cookie($this->uCookieSuffix); // set username cookie name
					$pCookie = new Cookie($this->pCookieSuffix); // set password cookie name
					$uCookie->bakeCookie('userName', $this->_userName);
					$pCookie->bakeCookie('password', $this->_password);
					$this->uCookie = $uCookie;
					$this->pCookie = $pCookie;
					$this->setUserInfo();					
					$lastIPAddr = $_SERVER['REMOTE_ADDR'];
					$curDate = date('Y-m-d');
					$curTime = date('H:i:s');					
					$this->_db->setQuery("UPDATE userRecords SET lastActiveDate = '".$curDate."', lastActiveTime = '".$curTime."', lastIPAddr = '".$lastIPAddr."' WHERE userName = '".$this->_userName."' AND password = '".$this->_password."'");
					if($redirect) {
						header("Location:$redirect");
					}
					return true;
				}
			}
		}		
		//

		// check password. a simple function to check whether a userName and password match up
		function checkPass() {
			if(isset($this->_submit['password'])) {
				if($this->_submit['password'] == null) {
					$this->_error = true;
					$this->_msg = "You failed to supply information for a required field";
					return false;
				}
				else {
					$this->_password = md5($this->_submit['password']);
				}
			}
			
			$query = "SELECT userID, userLevelNum FROM userRecords WHERE userName = '".$this->_userName."' AND password = '".$this->_password."'";
			$this->_db->setQuery($query);
			$numrows = $this->_db->getNumRows();
			if($numrows > '0') { // if $numrows is greater than 0 (it should only be 1, but just to be sure...)
				$this->_userInfo = $this->_db->getAssoc();
				return true;
			}
			else { // if $numrows is 0, userName and password don't match up.
				$this->_error = true;
				$this->_msg = "You submitted an invalid username or password.<br />Forgot your login credentials?</a> <a href=\"/modal.php?class=Registration&amp;mode=resetpassword&amp;curPage=\" title=\"Reset Password\">Reset your password</a>!";
				return false;
			}
		}
		//

		
		// Check login routine
		function checkLogin($authLevel, $userCookie, $passCookie) { // if a page is member-only this function is called to make sure the appropriate permission levels are upheld. accepts user level, userName cookie and password cookie as arguements.
			if($authLevel == '0') { // if user level of the page in question is 0, page is open to all visitors... 
				return true; // returns true and outputs the content of the page
			}
			elseif((!isset($userCookie)) || (!isset($passCookie))) { // if the cookie does not exist, no way to tell what visitor's user level is...
				return false; // returns false and outputs the login form
			}
		    elseif(isset($userCookie)) { // if the cookie does exist...
		        $check = $this->_db->setQuery("SELECT password, userLevelNum FROM userRecords WHERE userName = '$userCookie'"); // select the password and user level that corresponds to this userName cookie in the DB
		        while($info = $this->_db->getRow()) {
					if($passCookie != $info['password']) { // Checks if the cookie has the wrong password
						return false; // outputs the login form
					}
					elseif($passCookie == $info['password'] && $info['userLevelNum'] < $uLevel) { // if password cookie is the same as the password drawn from the database, but the userlevel is not high enough to view content...
						header("Location: http://".$_SERVER['HTTP_HOST']."/unauthorized/"); // redirect to unauthorized page.
					}
					else { // otherwise, everything checks out...
						return true; // so returns true and outputs the content of the page
					}
				}
			}
		}
		//

		public function welcomeMessage() {
			global $url;
			$messageTpl = new Template('./kibu/core/Authentication/templates/');
			$messageTpl->set("curPage", $url->path);
			if((isset($_COOKIE[$this->uCookie])) && (isset($_COOKIE[$this->pCookie])) && ($this->checkPass())) {
				$messageTpl->set("curPage", $url->path);
				$messageTpl->set("userName", $this->_userName);
				$tplFile = "welcome_message.tpl.php";
			}
			else {
				$tplFile = "welcome_login.tpl.php";
			}
			return $messageTpl->fetch($tplFile);
		}

		public function setPageLevel($pageLevel) {
			$this->_pageLevel = $pageLevel;
		}
	}
?>