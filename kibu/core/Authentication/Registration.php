<?php

require_once './kibu/core/Authentication/Authentication.php';
require_once './kibu/core/framework/date/Date.php';
require_once './kibu/core/System/Utility.php';

class Registration extends Authentication {
		
		protected $_curDate;
		protected $_curTime;
		protected $_curPage;
		protected $_url;
		protected $_mode;
		protected $_form;
		protected $_nextStep;
		protected $_formData = array();
		protected $_emailAddress;
		protected $_authcode;
		public $submitRedirect;
		public $cancelRedirect;

		public function __construct() {
			parent::__construct();
			if(isset($_GET['mode'])) {
				$this->_mode = $_GET['mode'];
			}
			$date = new Date();
			$this->_curDate = $date->mysql;
			$this->_curTime = $date->time;
			$this->_curPage = $_GET['curPage'];
			$this->_url = new URL_ext();
			$this->setVars();
			
			if(isset($_POST['submit'])) {
				$this->checkRequired();
				$this->checkMatching();
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
					$this->_changePasswordForm();
				}
			}
		}

		public function getOutput() {
			return $this->_form;
		}

		public function getNextStep() {
			return $this->_nextStep;
		}

		private function _regForm() {
			$regFormTpl = new Template('./kibu/core/Authentication/templates/');
			$regFormTpl->set_vars($this->_formData, true);
			$this->_form = $regFormTpl->fetch('registration_form.tpl.php');
		}

		private function _confirmForm() {
			$confirmFormTpl = new Template('./kibu/core/Authentication/templates/');
			$confirmFormTpl->set_vars($this->_formData, true);
			$this->_form = $confirmFormTpl->fetch('registration_confirm_form.tpl.php');
		}

		private function _resetPasswordForm() {
			if(!isset($this->_submit['emailAddress'])) {
				$this->_warning = true;
				$this->_msg = "You are about to reset your password. For security purposes, passwords are not stored in an unencrypted state. This means, if it is found in our records, you will receive an email at the address specified below with a NEW PASSWORD, which will be a random string of characters. You will have the opportunity to change your password once you log in using this new password.";
			}
			$confirmFormTpl = new Template('./kibu/core/Authentication/templates/');
			$confirmFormTpl->set_vars($this->_formData, true);
			$this->_form = $confirmFormTpl->fetch('reset_password_form.tpl.php');
		}

		private function _changePasswordForm() {
			$changePassFormTpl = new Template('./kibu/core/Authentication/templates/');
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
				$this->_required = array('curPass', 'newPass');
				$this->_matching = array('newPass');
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
				elseif(is_array($this->getUserInfo())) {
					$this->_formData = $this->getUserInfo();
					$this->_formData['formName'] = 'updateInfo';
					$this->_formData['formValue'] = 'Update Info';
					$this->_formData['emailAddress'] = array($this->_formData['emailAddress'], $this->_formData['emailAddress']);
				}
				else {
					$this->_formData['userName'] = null;
					$this->_formData['password'] = null;
					$this->_formData['emailAddress'] = null;
				}
				$this->_required = array('userName', 'password', 'emailAddress', 'firstName', 'lastName');
				$this->_matching = array('password', 'emailAddress');
			}
			$this->_formData['curPage'] = $this->_url->getCurPage()."?".$this->_url->query;
		}
		

		protected function doRegistration() {
			if($this->uNameCheck() && $this->emailCheck() && !$this->_error) {
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
				$this->_msg = "The username submitted is already in our system.";
				return false;
			}
			return true;
		}

		protected function emailCheck() {
			if(!Utility::validateEmail($this->_submit['emailAddress'][0])) {
				$this->_error = true;
				$this->_msg = "That email address appears to be invalid. Please review below and submit again.";
				return false;
			}
			else {
				global $db;
				$query = "SELECT userID FROM userRecords WHERE emailAddress = '".$this->_submit['emailAddress'][0]."'";
				$query = $db->setQuery($query);
				$numrows = $db->getNumRows();
				if($numrows > '0') {
					$this->_error = true;
					$this->_msg = "The email address submitted is already in our system.";
					return false;
				}
				return true;
			}
		}

		protected function register() {
			$userName = addslashes($this->_submit['userName']); // store the POST variable username as a more manageable variable
			$password = (md5($this->_submit['password'][0])); // store and "md5" encrypt the POST variable password as a more manageable variable
			$password2 = (md5($this->_submit['password'][1]));
			$emailAddress = $this->_submit['emailAddress'][0];
			$emailAddress2 = $this->_submit['emailAddress'][1];
			$initIPAddr = $_SERVER['REMOTE_ADDR'];
			$userGUID = Utility::guidGen();
			$this->_authcode = uniqid(time());
//			$memberInsert = "INSERT INTO userRecords SET
//					userName = '$userName',
//					password = '$password',
//					emailAddress = '$emailAddress',
//					initIPAddr = '$initIPAddr',
//					joinDate = '$this->_curDate',
//					joinTime = '$this->_curTime',
//					emailVerified = 'n',
//					emailVerifyString = '$this->_authcode',
//					userLevelNum = '0'"; // create new member insert query
			$table = "userRecords";
			
			$data = array( 
				'userName' => $userName,
				'password' => $password,
				'userGUID' => $userGUID,
				'emailAddress' => $emailAddress,
				'initIPAddr' => $initIPAddr,
				'joinDate' => $this->_curDate,
				'joinTime' => $this->_curTime,
				'emailVerified' => 'n',
				'emailVerifyString' => $this->_authcode,
				'userLevelNum' => '0'
			);
			
			$this->_db->insert($table, $data);
			
			if($this->_db->getAffectedRows() > 0) { // if new member insert query executed successfully
				if(!isset($this->_submit['authcode'])) {
					$siteConfig = $this->_url->siteConfig;
					$emailBodyTpl = new Template('./kibu/core/Authentication/templates/');
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
					$send = mail($emailAddress, 'Registration confirmation email from '.$_SERVER['HTTP_HOST'].'', $emailBody, $headers).""; // utilize php mail function
					if($send) {
						$this->submitRedirect = $this->_curPage;
						$this->_nextStep = 'close';
						$this->_setConfirmCookie();
						$this->_msg = "Your registration has been processed, but your account is not yet verified. You will shortly receive an email at the address you supplied containing your login information and a verification code. The next time you log in you will be prompted to verify your email address.</p>\n"; // output email/reg success message
					}
					else { // otherwise, if send of email was unsuccessful
						$this->_error = true;
						$this->_msg= "There was a problem sending your registration confirmation email. Your registration may have already been processed. Please contact us."; // output email error message
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
				$this->_msg = "There was a problem processing your registration. Please try again, and contact us if you continue to have problems: ". $this->_db->getError(); // output registration error message
			}
			$this->setVars();
		}
		//

		protected function checkAuthcode() {
			global $db;
			$query = "SELECT * FROM userRecords WHERE emailVerifyString = '".$this->_submit['authCode']."'"; // query database for applicable user record
			$this->_db->setQuery($query);
			$userNumRows = $this->_db->getNumRows(); // check to make sure this authcode exists (should be 1)
			$userArray = $this->_db->getAssoc(); // create array from query
			if($userNumRows == 0) {
				$this->_error = true;
				$this->_msg = "The system could not find that authorization code. Please ensure that you have copied or typed the entire string of characters sent to you in the confirmation email.";
				return false;
			}
			elseif($userArray['emailAddress'] != $this->_submit['emailAddress']) {
				$this->_error = true;
				$this->_msg = "That email address was not recognized. Please verify the spelling of the email address you've submitted, correct it if necessary, and resubmit the form.";
			}
			elseif($userArray['emailVerified'] == 'y') {
				$this->_error = true;
				$this->_msg = "According to our records, this email address has already been verified and the related account activated. If you have forgotten your password you may have a new one sent to you via our <a href=\"/modal.php?class=Registration&amp;mode=resetpassword&amp;curPage=".$this->_curPage."\" title=\"Reset Password\">Reset Password</a> form.";
				return false;
			}
			else {
				return true;
			}
		}

		//
		protected function confirmReg() {
			//$query = "UPDATE userRecords SET userLevelNum = '1', emailVerified = 'y', verifyDate = '$this->_curDate', verifyTime = '$this->_curTime' WHERE emailVerifyString = '".$this->_submit['authCode']."'";
			
			$table = "userRecords";
			
			$data = array(
				'userLevelNum' => 1,
				'emailVerified' => 'y',
				'verifyDate' => $this->_curDate, 
				'verifyTime' => $this->_curTime	
			);
			
			$where = "emailVerifyString = '".$this->_submit['authCode']."'";
			
			$this->_db->update($table, $data, $where);
			
			if($this->_db->getAffectedRows() > 0) { // otherwise, update query did not execute successfully, output error message
				$this->_setConfirmCookie(false);
				$this->_error = false;
				$this->_msg = "Your email address and account have been verified. You may now use your username and password to login in to the site.";
				$this->_nextStep = 'close';
				$this->submitRedirect = $this->_curPage;				
			}
			else { // if update query executed successfully, ouptut success message
				$this->_error = true;
				$this->_msg = "There was a problem authenticating your email address and account information. Contact the administrator in order to rectify the situation.";
		
			}
		}
		//

		private function _setConfirmCookie($bake = true) {
			$this->_confirmCookie = new Cookie("regconfirm");
			$this->_confirmCookie->_cookiePrefix = $this->_url->siteConfig['cookiePrefix'];
			if($bake) {
				$this->_confirmCookie->bakeCookie("regconfirm", "confirm");			
			}
			else {
				$this->_confirmCookie->eatCookie();
			}
		}

		//
		private function _resetPassword() {
			global $db;
			if(isset($this->_submit['emailAddress'])) {
				if(!Utility::validateEmail($this->_submit['emailAddress'])) {
					$this->_error = true;
					$this->_msg = "The email address submitted does not seem to be of a valid format. Please double-check the email address supplied and submit the form again.";
				}
				else {
					$emailAddress = $this->_submit['emailAddress'];
					if($this->setUserByEmail($emailAddress)) { // if the number of rows that contain that email address is '0' (i.e. not in the database), output error message
						$oldHashPass = $this->_userInfo['password'];
						$newPassword = Utility::generateRandStr(8); // generate a new random password using generateRandStr() method from '/kibu/core/System/Utility.php'
						$newHashPassword = md5($newPassword); // create hashed password to insert into database
						$forceChange = 1;
					}
					else {
						$this->_error = true;
						$this->_msg = "The email address you entered doesn't appear to be in our system. Please try again below.";
					}
				}
			}
			elseif(isset($this->_submit['curPass'])) {
				$oldPassword = $this->_submit['curPass'];
				$newPassword = $this->_submit['newPass'][0];
				$oldHashPass = md5($oldPassword);
				if($oldHashPass != $this->_password) {
					$this->_error = true;
					$this->_msg = "The password you entered doesn't appear to be valid. Please try again below.";
				}
				else {
					$newHashPassword = md5($newPassword);
					$forceChange = 0;
					$emailAddress = $this->_userInfo['emailAddress'];
				}
			}
			if(!$this->_error) { // otherwise, we continue...
				//$setNewPassword = "UPDATE userRecords SET password = '".$newHashPassword."', forcePWChange = '".$forceChange."' WHERE userID = '".$this->_userID."'"; // create sql update of new encrypted password
				
				$table = "userRecords";
				
				$data = array(
					'password' => $newHashPassword,
					'forcePWChange' => $forceChange
				);
				
				$where = "userID = '".$this->_userID."'";
				
				$this->_db->update($table, $data, $where);
				
				if($this->_db->getAffectedRows() > 0) { // if the sql statement is successfully executed
					$siteConfig = $this->_url->siteConfig;
					$emailBodyTpl = new Template('./kibu/core/Authentication/templates/');
					if($this->_mode == 'resetpassword') {
						$emailBodyTpl->set("formType", "reset password");
					}
					elseif($this->_mode == 'changepassword') {
						$emailBodyTpl->set("formType", "change password");
					}
					$emailBodyTpl->set("userName", $this->_userName);
					$emailBodyTpl->set("password", $newPassword);
					$emailBodyTpl->set("site", $_SERVER['HTTP_HOST']);
					$emailBodyTpl->set("authCode", "auth");
					$emailBody = $emailBodyTpl->fetch('registration_confirm_email.tpl.php');
					$headers = "From: ".$siteConfig['siteAddress']."";
					$headers .= "<".$siteConfig['siteEmail'].">\n";
					$headers .= "Reply-To:".$siteConfig['siteEmail']."\n";
					$headers .= "Return-Path:".$siteConfig['siteEmail'];
					$send = mail($emailAddress, "Reset password email from ".$_SERVER['HTTP_HOST']."", $emailBody, $headers); // utilize php mail function
					if($send) {
						$this->_msg = "Your password has been successfully changed. Watch your email for the confirmation containing your username and new password.";
					}
					else {
						$this->_error = true;
						$this->_msg = "There was a problem emailing your new password to the address supplied. Please contact the administrator for assistance.";
					}
				}
				else { // if the email encountered a problem, put old password back and output error message
					$unresetPassword = "UPDATE userRecords SET password = '".$oldHashPass."' WHERE userID = '".$this->_userID."'";
					
					$data = array('password' => $oldHashPass);
					
					$this->_db->update($table, $data, $where);
					
					if($this->_db->getAffectedRows() > 0) { // if the old password sql is executed correctly
						$this->_error = true;
						$this->_msg = "There was a problem emailing your credentials. Your original password has been restored. Please try again and contact the administrator for assistance if the problem persists.";
					}
					else { // if everything totally fails for some reason, output catastrohpic failure message
						$this->_error = true;
						$this->_msg = "There were multiple problems with resetting your password. Please contact the administrator for assistance.";
					}
				}
			}
		}
		//

		public function getTemplateVars() {
			return $this->_formData;
		}

	}
?>