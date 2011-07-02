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

	 require_once './kibu/core/class/Cookie.php';
	 require_once './kibu/core/class/Template.php';
	 
	class Authentication {
		
		protected $_userName = 'Guest';
		protected $_password = '';
		protected $_userID = '0';
		protected $_userLevel = '0';
		protected $_userInfo = array();
		protected $_pageLevel = '0';
		protected $_msg;
		protected $_permissions;
		protected $_pathArray;

		public $cookiePrefix;
		public $uCookie;
		public $pCookie;
		public $uCookieSuffix;
		public $pCookieSuffix;
		public $reauthenticate = true;


		public function __construct() {
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
						WHERE contentRecords.titleClean = '". $url->_filename ."'
								AND navigationSections.sectionName = '" . $url->_section . "'";
				global $db;
				$db->setQuery($query);
				$assoc = $db->getAssoc();
				$this->_pageLevel = $assoc['visitorAuthLevel'];
				if($this->reauthenticate == true) { // if we have to reauthenticate for this page...
						header("Location:http://".$_SERVER['HTTP_HOST']."/home/login.html?redirect=" . $url->_URLPath.""); // redirect to system login page
				}
		}

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
				else {
					$this->reauthenticate = false;
				}
			}
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
			global $db;
		   	$userQuery = $db->setQuery($query);
		   	$this->_userInfo = $db->getAssoc($userQuery);
				$this->_userID = $this->_userInfo['userID'];
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
				if(isset($_POST['userName'])) {
					$this->_userName = $_POST['userName']; // store the POST variable username as a more manageable variable
				}
				elseif(isset($this->uCookie)) {
					$this->_userName = $_COOKIE[$this->uCookie];
				}
				$this->_password = (md5($_POST['password'])); // store and "md5" encode the POST variable password as a more manageable variable
				$checkPass = $this->checkPass();
				if($this->_userName == NULL | $_POST['password'] == NULL | !$checkPass) {  // This makes sure they filled all the required fields and the userName has a member record
					$msg = "\t\t\t<p class=\"message\">An error occurred submitting your login:</p>\n\t\t\t<ul>";
					if($this->_userName == NULL | $_POST['password'] == NULL) { // if required field(s) not filled out
						$msg .="<li class=\"message\">You failed to supply information for a required field.</li>\n";	
					}
					elseif(!$checkPass) { // if the password or userName are wrong
						$msg .="<li class=\"message\">You submitted an invalid username or password.<br />Forgot your login credentials?</a> <a href=\"/registration/confirm.html?action=resetpassword\">Reset your password</a>!</li>\n";
					}
					$msg .= "\t\t\t</ul>\n";
					$this->_msg = $msg;
					return false;
				}
				else {
					$this->_userName = stripslashes($this->_userName);
					$lastIPAddr = $_SERVER['REMOTE_ADDR'];
					$curDate = date('Y-m-d');
					$curTime = date('H:i:s');
					$lastActive = mysql_query("UPDATE userRecords SET lastActiveDate = '".$curDate."', lastActiveTime = '".$curTime."', lastIPAddr = '".$lastIPAddr."' WHERE userName = '".$this->_userName."' AND password = '".$this->_password."'");
					$uCookie = new Cookie($this->uCookieSuffix); // set username cookie name
					$pCookie = new Cookie($this->pCookieSuffix); // set password cookie name
					$uCookie->bakeCookie('userName', $this->_userName);
					$pCookie->bakeCookie('password', $this->_password);
					$this->uCookie = $uCookie;
					$this->pCookie = $pCookie;
					if($redirect) {
						header("Location:$redirect");
					}
					return true;
				}
		}		
		//

		// check password. a simple function to check whether a userName and password match up
		function checkPass() {
			global $db;
			$query = "SELECT userID, userLevelNum FROM userRecords WHERE userName = '".$this->_userName."' AND password = '".$this->_password."'";
			$db->setQuery($query);
			$numrows = $db->getNumRows();
			if($numrows > '0') { // if $numrows is greater than 0 (it should only be 1, but just to be sure...)
				$userInfo = $db->getAssoc();
				return $userInfo;
			}
			else { // if $numrows is 0, userName and password don't match up.
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
		        $check = mysql_query("SELECT password, userLevelNum FROM userRecords WHERE userName = '$userCookie'") or die(mysql_error()); // select the password and user level that corresponds to this userName cookie in the DB or die trying
		        while($info = mysql_fetch_assoc($check)) {
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
				$messageTpl = new Template('./kibu/templates/');
				if((isset($_COOKIE[$this->uCookie])) && (isset($_COOKIE[$this->pCookie])) && ($this->checkPass())) {
						$messageTpl->set("curPage", $url->_path);
						$messageTpl->set("userName", $this->_userName);
						$tplFile = "welcome_message.tpl.php";
				}
				else {
						$messageTpl->set("curPage", $url->_path);
						$tplFile = "welcome_login.tpl.php";
				}
				return $messageTpl->fetch($tplFile);

		}

		public function setPageLevel($pageLevel) {
			$this->_pageLevel = $pageLevel;
		}

		public function getMsg() {
			return $this->_msg;
		}
	}
?>