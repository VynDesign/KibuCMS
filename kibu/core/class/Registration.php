<?php

require_once './kibu/core/class/Form.php';
require_once './kibu/core/class/Authentication.php';
require_once './kibu/core/class/Url.php';
require_once './kibu/core/class/Utility.php';
require_once './kibu/core/class/Template.php';
require_once './kibu/core/class/Cookie.php';

class Registration extends Form {
		
		protected $_curDate;
		protected $_curTime;
		protected $_curPage;
		protected $_auth;
		protected $_url;
		protected $_mode;
		protected $_form;
		protected $_nextStep;
		protected $_formData = array();
		protected $_msg;
		protected $_error = false;
		protected $_errorMsg = array();
		protected $_required = array();
		protected $_matching = array();
		protected $_userName;
		protected $_password;
		protected $_emailAddress;
		protected $_authcode;
		public $submitRedirect;
		public $cancelRedirect;

		public function __construct() {
				if(isset($_GET['mode'])) {
						$this->_mode = $_GET['mode'];
				}
				$this->_curDate = date('Y-m-d');
				$this->_curTime = date('H:i:s');
				$this->_curPage = $_GET['curPage'];
				$this->_auth = new Authentication();
				$this->_url = new URL();
				parent::__construct();
				$this->setVars();
				$this->setRequired($this->_required);
				$this->setMatching($this->_matching);
				if(isset($_POST['submit'])) {
						$this->_submit = $_POST;
						if($this->_mode == 'register') {
								$this->doRegistration();
						}
						elseif($this->_mode == 'confirm') {
								$this->doConfirmation();
						}
						elseif($this->_mode == 'resetpassword') {
								if(!isset($this->_submit['emailAddress'])) {
										$this->_resetPasswordForm();
								}
								elseif(isset($this->_submit['emailAddress'])) {
										$this->_resetPassword();
										if($this->_error == false) {
												$this->_nextStep = 'close';
										}
										else {
												$this->_resetPasswordForm();
										}
								}
						}
						elseif($this->_mode == 'changepassword')
						{
								if(!isset($this->_submit['curPass'])) {
										$this->_changePasswordForm();
								}
								elseif(isset($this->_submit['curPass'])) {
										$this->_resetPassword();
										if($this->_error == false) {
												$this->_nextStep = 'close';
										}
										else {
												$this->_changePasswordForm();
										}
								}
						}
				}
				else {
						if($this->_mode == 'register') {
								$this->_regForm();
						}
						elseif($this->_mode == 'confirm') {
								$this->_confirmForm();
						}
						elseif($this->_mode == 'resetpassword') {
								$this->_resetPasswordForm();
						}
						elseif($this->_mode == 'changepassword')
						{

						}
				}
		}

		public function outputFormBody() {
				return $this->_form;
		}

		public function getNextStep() {
				return $this->_nextStep;
		}

		private function _regForm() {
				$regFormTpl = new Template('./kibu/templates/');
				$regFormTpl->set_vars($this->_formData, true);
				$this->_form = $regFormTpl->fetch('registration_form.tpl.php');
		}

		private function _confirmForm() {
				$confirmFormTpl = new Template('./kibu/templates/');
				$confirmFormTpl->set_vars($this->_formData, true);
				$this->_form = $confirmFormTpl->fetch('registration_confirm_form.tpl.php');
		}

		private function _resetPasswordForm() {
				if(!isset($this->_submit['emailAddress'])) {
						$this->_msg = "<div class=\"warning\">CAUTION: You are about to reset your password. For security purposes, passwords are not stored in an unencrypted state. This means, if it is found in our records, you will receive an email at the address specified below with a NEW PASSWORD, which will be a random string of characters. You will have the opportunity to change your password once you log in using this new password.</div>";
				}
				$confirmFormTpl = new Template('./kibu/templates/');
				$confirmFormTpl->set_vars($this->_formData, true);
				$this->_form = $confirmFormTpl->fetch('reset_password_form.tpl.php');
		}

		private function _changePasswordForm() {
				$changePassFormTpl = new Template('./kibu/templates/');
				$changePassFormTpl->set_vars($this->_formData, true);
				$this->_form = $changePassFormTpl->fetch('registration_change_pass.tpl.php');
		}

		private function setVars() {
				if($this->_mode == 'confirm') {
						$this->_required = array('emailAddress', 'authCode');
						$this->_formData['authCode'] = null;
						$this->_formData['emailAddress'] = null;
				}
				elseif($this->_mode == 'resetpassword') {
						$this->_required = array('emailAddress');
						if(isset($this->_submit['emailAddress'])) {
								$this->_formData = $this->_submit;
						}
						else {
								$this->_formData['emailAddress'] = null;
						}
				}
				elseif($this->_mode == 'changepassword')
				{
						$this->_required = array('curPass', 'password');
						$this->_matching = array('password');
				}
				else {
						if((isset($_POST['register']) || (isset($_POST['updateInfo'])))) {
								$this->_formData = $_POST;
								if(isset($_POST['register'])) {
										$this->_formData['formName'] = 'register';
										$this->_formData['formValue'] = 'Register';
								}
								elseif(isset($_POST['updateInfo'])) {
									$this->_formData['formName'] = 'updateInfo';
									$this->_formData['formValue'] = 'Update Info';
								}
						}
						elseif($this->_auth->getUserName() == 'Guest' || $this->_auth->getUserName() == null) {
								$this->_formData['userName'] = null;
								$this->_formData['password'] = null;
								$this->_formData['emailAddress'] = null;
						}
						elseif($this->_auth->getUserName() != 'Guest') {
								$this->_formData = $this->_auth->getUserInfo();
								$this->_formData['formName'] = 'updateInfo';
								$this->_formData['formValue'] = 'Update Info';
								$this->_formData['emailAddress'] = array($this->_formData['emailAddress'], $this->_formData['emailAddress']);
						}
						$this->_required = array('userName', 'password', 'emailAddress', 'firstName', 'lastName');
						$this->_matching = array('password', 'emailAddress');
				}
				$this->_setNamePrefixes();
				$this->_formData['namePrefixes'] = $this->_namePrefixes;
				$this->_formData['curPage'] = $this->_url->getCurPage()."?".$this->_url->_query;
				$this->_formData['message'] = $this->getMsg();
		}

		private function _setNamePrefixes() {
				global $db;
				$query = "SELECT * FROM userRecordPrefixes ORDER BY namePrefixID";
				$query = $db->setQuery($query);
				while ($result = $db->getAssoc()) {
						$this->_namePrefixes[$result['namePrefixID']] = $result['namePrefixAbbrv'];
				}
		}

		public function setRequired($required) {
				$this->_required = $required;
				parent::setRequired($this->_required);
		}
		
		public function setMatching($matching) {
				$this->_matching = $matching;
				parent::setMatching($this->_matching);
		}

		protected function doRegistration() {
				$this->uNameCheck();
				$this->emailCheck();
				if($this->_error != true) {
						$this->register();
				}
				else {
						$this->_formData = $this->_submit;
						$this->_regForm();
				}
		}

		protected function doConfirmation() {
				if($this->checkAuthcode()) {
						$this->confirmReg();
				}
				else {
						$this->_formData = $this->_submit;
						$this->_confirmForm();
				}
		}

		protected function uNameCheck() {
				global $db;
				$query = "SELECT userID FROM userRecords WHERE userName = '".$this->_submit['userName']."'";
				$query = $db->setQuery($query);
				$numrows = $db->getNumRows();
				if($numrows > '0') {
						$this->_error = true;
						$this->_errorMsg[] = "The username submitted is already in our system.";
				}
				$this->setVars();
				return $numrows;
		}

		protected function emailCheck() {
				global $db;
				$query = "SELECT userID FROM userRecords WHERE emailAddress = '".$this->_submit['emailAddress'][0]."'";
				$query = $db->setQuery($query);
				$numrows = $db->getNumRows();
				if($numrows > '0') {
						$this->_error = true;
						$this->_errorMsg[] = "The email address submitted is already in our system.";
				}
				$this->setVars();
				return $numrows;
		}

		protected function register() {
				$userName = addslashes($this->_submit['userName']); // store the POST variable username as a more manageable variable
				$password = (md5($this->_submit['password'][0])); // store and "md5" encrypt the POST variable password as a more manageable variable
				$password2 = (md5($this->_submit['password'][1]));
				$emailAddress = $this->_submit['emailAddress'][0];
				$emailAddress2 = $this->_submit['emailAddress'][1];
				$initIPAddr = $_SERVER['REMOTE_ADDR'];
				$this->_authcode = uniqid(time());
						$memberInsert = "INSERT INTO userRecords SET
								userName = '$userName',
								password = '$password',
								emailAddress = '$emailAddress',
								initIPAddr = '$initIPAddr',
								joinDate = '$this->_curDate',
								joinTime = '$this->_curTime',
								emailVerified = 'n',
								emailVerifyString = '$this->_authcode',
								userLevelNum = '0'"; // create new member insert query
				if(Utility::validateEmail($this->_submit['emailAddress'][0])) {
						if(mysql_query($memberInsert)) { // if new member insert query executed successfully
								if(!isset($this->_submit['authcode'])) {
										$siteConfig = $this->_url->siteConfig;
										$emailBodyTpl = new Template('./kibu/templates/');
										$emailBodyTpl->set("formType", "registration");
										$emailBodyTpl->set("userName", $userName);
										$emailBodyTpl->set("password", $this->_submit['password'][0]);
										$emailBodyTpl->set("site", $_SERVER['HTTP_HOST']);
										$emailBodyTpl->set("authCode", $this->_authcode);
										$emailBody = $emailBodyTpl->fetch('registration_confirm_email.tpl.php');
										$headers = "From: ".$siteConfig['siteAddress']."";
										$headers .= "<".$siteConfig['siteEmail'].">\n";
										$headers .= "Reply-To:".$siteConfig['siteEmail']."\n";
										$headers .= "Return-Path:".$siteConfig['siteEmail'];
										$send = mail($emailAddress, 'Registration confirmation email from '.$_SERVER['HTTP_HOST'].'', $emailBody, $headers); // utilize php mail function
										if($send) {
												$this->submitRedirect = $this->_curPage;
												$this->_nextStep = 'close';
												$this->_setConfirmCookie();
												$this->_confirmCookie->bakeCookie("regconfirm", "confirm");
												$this->_msg = "Your registration has been processed, but your account is not yet verified. You will shortly receive an email at the address you supplied containing your login information and a verification code. At that time, click on the 'Confirm Registration' link here and fill out the form to verify your account.</p>\n"; // output email/reg success message
										}
										else { // otherwise, if send of email was unsuccessful
												$this->_error = true;
												$this->_errorMsg[] = "There was a problem sending your registration confirmation email. Your registration may have already been processed. Please contact us."; // output email error message
										}
								}
								else {
										$this->submitRedirect = $this->_curPage;
										$this->_nextStep = 'close';
										$this->_msg = "Your registration has been processed. You may now log in at the top of the screen using the credentials you submitted"; // output confirmation success message
								}
						}
						else {
								$this->_error = true;
								$this->_errorMsg[] = "There was a problem processing your registration. Please try again, and contact us if you continue to have problems.". mysql_error(); // output registration error message
						}
				}
				else {
						$this->_error = true;
						$this->_errorMsg[] = "That email address appears to be invalid. Please review below and submit again.";
				}
				$this->setVars();
		}
		//

		protected function checkAuthcode() {
				global $db;
				$query = "SELECT * FROM userRecords WHERE emailVerifyString = '".$this->_submit['authCode']."'"; // query database for applicable user record
				$db->setQuery($query);
				$userNumRows = $db->getNumRows(); // check to make sure this authcode exists (should be 1)
				$userArray = $db->getAssoc(); // create array from query
				if($userNumRows == 0) {
						$this->_error = true;
						$this->_errorMsg[] = "The system could not find that authorization code. Please ensure that you have copied or typed the entire string of characters sent to you in the confirmation email.";
						return false;
				}
				elseif($userArray['emailAddress'] != $this->_submit['emailAddress']) {
						$this->_error = true;
						$this->_errorMsg[] = "That email address was not recognized. Please verify the spelling of the email address you've submitted, correct it if necessary, and resubmit the form.";
				}
				elseif($userArray['emailVerified'] == 'y')
				{
						$this->_error = true;
						$this->_errorMsg[] = "According to our records, this email address has already been verified and the related account activated. If you have forgotten your password you may have a new one sent to you via our <a href=\"/modal.php?class=Registration&amp;mode=resetpassword&amp;curPage=".$this->_curPage."\" title=\"Reset Password\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Reset Password</a> form.";
						return false;
				}
				else {
						return true;
				}
		}

		//
		protected function confirmReg() {
				global $db;
				$query = "UPDATE userRecords SET userLevelNum = '1', emailVerified = 'y', verifyDate = '$this->_curDate', verifyTime = '$this->_curTime' WHERE emailVerifyString = '".$this->_submit['authCode']."'";
				if(!mysql_query($query)) { // otherwise, update query did not execute successfully, output error message
						$this->_error = true;
						$this->_errorMsg[] = "There was a problem authenticating your email address and account information. Contact the administrator in order to rectify the situation.";
				}
				else { // if update query executed successfully, ouptut success message
						$this->_setConfirmCookie();
						$this->_confirmCookie->eatCookie();
						$this->_error = false;
						$this->_msg = "Your email address and account have been verified. You may now use your username and password to login in to the site.";
						$this->_nextStep = 'close';
						$this->submitRedirect = $this->_curPage;
				}
		}
		//

		private function _setConfirmCookie() {
				$this->_confirmCookie = new Cookie("regconfirm");
				$this->_confirmCookie->_cookiePrefix = $this->_url->siteConfig['cookiePrefix'];
		}

		//
		private function _resetPassword() {
				global $db;
				$this->_msg[] = null;
				if(isset($this->_submit['emailAddress'])) {
						if(!Utility::validateEmail($this->_submit['emailAddress'])) {
								$this->_error = true;
								$this->_errorMsg[] = "The email address submitted does not seem to be of a valid format. Please double-check the email address supplied and submit the form again.";
						}
						else {
								$emailAddress = $this->_submit['emailAddress'];
								$query = "SELECT userID, userName, password FROM userRecords WHERE emailAddress = '".$emailAddress."'"; // get userId, userName, and original password from database where the email address is the same as the posted form data
								$db->setQuery($query);
								$numRows = $db->getNumRows(); // number of rows that contain that email address (should be 1)
								if($numRows == '0') { // if the number of rows that contain that email address is '0' (i.e. not in the database), output error message
										$this->_error = true;
										$this->_errorMsg[] = "The email address you entered doesn't appear to be in our system. Please try again below.";
								}
								else {
										$array = $db->getAssoc(); // output array of the query
										$oldPassword = $array['password'];
										$newPassword = Utility::generateRandStr(8); // generate a new random password using generateRandStr() method from '/kibu/core/class/Utility.php'
										$newHashPassword = md5($newPassword); // create hashed password to insert into database
										$forceChange = 1;
								}
						}
				}
				elseif(isset($this->_submit['password'])) {
						$username = $this->_auth->getUserName();
						$oldPassword = $this->_submit['curPass'];
						$newPassword = $this->_submit['password'][0];
						$oldHashPass = md5($oldPassword);
						$query = "SELECT userID, userName, password, emailAddress FROM userRecords WHERE password = '".$oldHashPass."' AND userName = '".$username."'"; // get userId, userName, and original password from database where the email address is the same as the posted form data
						$db->setQuery($query);
						$numRows = $db->getNumRows();
						if($numRows == '0') {
								$this->_error = true;
								$this->_errorMsg[] = "The password you entered doesn't appear to be valid. Please try again below.";
						}
						else {
								$array = $db->getAssoc(); // output array of the query
								$oldPassword = $array['password'];
								$newHashPassword = md5($newPassword);
								$forceChange = 0;
								$emailAddress = $array['emailAddress'];
						}
				}
				if($this->_error != true) { // otherwise, we continue...
						$userID = $array['userID'];
						$userName = $array['userName']; // turn userName into variable
						$setNewPassword = "UPDATE userRecords SET password = '".$newHashPassword."', forcePWChange = '".$forceChange."' WHERE userID = '".$userID."'"; // create sql update of new encrypted password
						if(mysql_query($setNewPassword)) { // if the sql statement is successfully executed
								$siteConfig = $this->_url->siteConfig;
								$emailBodyTpl = new Template('./kibu/templates/');
								if($this->_mode == 'resetpassword') {
										$emailBodyTpl->set("formType", "reset password");
								}
								elseif($this->_mode == 'changepassword') {
										$emailBodyTpl->set("formType", "change password");
								}
								$emailBodyTpl->set("userName", $userName);
								$emailBodyTpl->set("password", $newPassword);
								$emailBodyTpl->set("site", $_SERVER['HTTP_HOST']);
								$emailBodyTpl->set("authCode", "auth");
								$emailBody = $emailBodyTpl->fetch('registration_confirm_email.tpl.php');
								$headers = "From: ".$siteConfig['siteAddress']."";
								$headers .= "<".$siteConfig['siteEmail'].">\n";
								$headers .= "Reply-To:".$siteConfig['siteEmail']."\n";
								$headers .= "Return-Path:".$siteConfig['siteEmail'];
								$send = mail($emailAddress, 'Reset password email from '.$_SERVER['HTTP_HOST'].'', $emailBody, $headers); // utilize php mail function
								if($send) {
										$this->_msg = "Your password has been successfully changed. Watch your email for the confirmation containing your username and new password.";
								}
								else {
										$this->_error = true;
										$this->_errorMsg[] = "There was a problem emailing your new password to the address supplied. Please contact the administrator for assistance.";
								}
						}
						else { // if the email encountered a problem, put old password back and output error message
								$unresetPassword = "UPDATE userRecords SET password = '".$oldPassword."' WHERE userID = '".$userID."'";
								if(mysql_query($unresetPassword)) { // if the old password sql is executed correctly
										$this->_error = true;
										$this->_errorMsg[] = "There was a problem emailing your credentials. Your original password has been restored. Please try again and contact the administrator for assistance if the problem persists.";
								}
								else { // if everything totally fails for some reason, output catastrohpic failure message
										$this->_error = true;
										$this->_errorMsg[] = "There were multiple problems with resetting your password. Please contact the administrator for assistance.";
								}
						}
				}
		}
		//


		public function getTemplateVars() {
				return $this->_formData;
		}

		public function getSubmit() {
				return $this->_submit;
		}

		public function getError() {
				if(parent::getError() == true) {
						$this->_error = parent::getError();
				}
				return $this->_error;
		}

		public function getMsg() {
				parent::getMsg();
				return $this->_msg;
		}
}
?>