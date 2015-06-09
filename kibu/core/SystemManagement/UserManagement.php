<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserManagement
 *
 * @author vyn
 */

	require_once './kibu/core/SystemManagement/SystemManagement.php';

	class UserManagement extends SystemManagement {
	
		private $_query;
		protected $_formData;

		public function __construct() {
			parent::__construct();
		}
	}
	
	
	class UserManagement_List extends UserManagement {

		private $_filter = "Registered";
		private $_chosenAction;
		private $_dataCount;
		
		public function __construct() {
			$this->_permAbility = "View User List";
			parent::__construct();			
			if(isset($_GET['filter'])) {
				$this->_filter = $_GET['filter'];
			}
						
			if((isset($this->_submit['confirmReverify'])) || isset($this->_submit['confirmDelete'])) {
				$this->_confirmAction();
			}
			elseif(isset($this->_submit['action'])) {
				$this->_chosenAction = $this->_submit['action'];
				
				if($this->_chosenAction == 'reverify') {
					$this->_reverifySelected();
				}
				elseif($this->_chosenAction == 'delete') {
					$this->_deleteSelected();
				}
			}
			else {	
				$this->_setUserList();
			}
			parent::_setFormBody();				
		}
		
		private function _setQuery() {
			if($this->_filter == 'Unverified') {
				$this->_query = "SELECT * FROM userRecords, userLevels WHERE userRecords.userLevelNum = userLevels.levelNum AND emailVerified = 'n' ORDER BY userName ASC";
			}
			elseif($this->_filter == "Verified") {
				$this->_query = "SELECT * FROM userRecords, userLevels WHERE userRecords.userLevelNum = userLevels.levelNum AND emailVerified = 'y' ORDER BY userName ASC";
			}
			else {
				$this->_query = "SELECT * FROM userRecords, userLevels WHERE userRecords.userLevelNum = userLevels.levelNum ORDER BY userName ASC";
			}
		}
		
		protected function _setUserList() {
			$this->_setQuery();			
			$this->_nextStep = 'finish';
			$this->_db->setQuery($this->_query);
			$this->_db->getAssocArray();
			$this->_formData['filter'] = $this->_filter;
			$this->_formData['userList'] = $this->_db->returnData;
			$this->_formTpl = "user_list.tpl.php";			
		}
		
		private function _confirmAction() {
			if(isset($this->_submit['confirmReverify'])) {
				$this->_chosenAction = 'reverify';
				if(isset($this->_submit['reverify'])) {
					$this->_dataCount = count($this->_submit['reverify']);
				}
			}
			elseif(isset($this->_submit['confirmDelete'])) {
				$this->_chosenAction = 'delete';			
				if(isset($this->_submit['delete'])) {
					$this->_dataCount = count($this->_submit['delete']);	
				}
			}			
			
			if(!$this->_dataCount) {
				$this->_error = true;
				$this->_msg = "There were no user records selected to " .$this->_chosenAction . ". Please make your selections in the form below and resubmit using the appropriate button.";
				$this->_setUserList();
			}
			else {
				$this->_warning = true;
				$this->_formTpl = "user_list_confirm_action.tpl.php";
				$this->_nextStep = $this->_chosenAction;
				$this->_formData['action'] = $this->_chosenAction;
				
				if(isset($this->_submit['confirmReverify'])) {
					$this->_msg = "You have selected " . $this->_dataCount . " user records for reverification. This process will generate a new verification string (meaning the original string sent at the time each user registered will not be recognized) and send an email containing that string and a verification link to each user selected. If selected accounts have previously been verified, they will be set to unverified.<br /><br /> To proceed, click 'Submit' below. To exit this process and leave user records unchanged, click 'Cancel'";
					$keys = Utility::concatArray($this->_submit['reverify'], ", ", true);
					$this->_formData['data'] = $keys;
				}
				elseif(isset($this->_submit['confirmDelete'])) {
					$this->_msg = "You have selected " . $this->_dataCount . " user records for deletion. This cannot be undone.<br /><br /> To proceed, click 'Submit' below. To exit this process and leave user records unchanged, click 'Cancel'.";
					$keys = Utility::concatArray($this->_submit['delete'], ", ", true);				
					$this->_formData['data'] = $keys;
				}
			}
		}
		
		private function _reverifySelected() {
			$this->_nextStep = 'finish';			
			$query = "SELECT userGUID, userName, emailAddress FROM userRecords WHERE userGUID IN (" . $this->_submit['data'] . ") AND userLevelNum < 10000";
			$this->_db->setQuery($query);
			$this->_db->getAssocArray();
			$users = $this->_db->returnData;
			
			$updateCount = 0;
			foreach($users as $user) {
				//TODO implement transaction
				$user['authcode'] = uniqid(time());
				//$update = "UPDATE userRecords SET emailVerifyString = '" . $user['authcode'] . "' WHERE userGUID = '" . $user['userGUID'] . "'";
				
				$table = "userRecords";
				$data = array('emailVerifyString' => $user['authcode']);
				$where = "userGUID = '" . $user['userGUID'] . "'";
				
				$this->_db->update($table, $data, $where);
				
				if($this->_db->getAffectedRows() > 0) {
					$this->_sendReverification($user);
					$updateCount ++;
				}
				else {
					$this->_error = true;
					$this->_msg = "An error ocurred updating user record for " . $user['userName'] . ": " . $this->_db->getError();
				}
			}
			
			$this->_msg = $updateCount . " user record(s) sent reverification email.";
			$this->_db->returnData = null;
			
			$this->_setUserList();
		}
		
		private function _deleteSelected() {
			$this->_nextStep = 'finish';
			$this->_query = "DELETE FROM userRecords WHERE userGUID IN (" . $this->_submit['data'] . ") AND userLevelNum < 10000";
			$this->_db->setQuery($this->_query);
			$affectedRows = $this->_db->getAffectedRows();
			if($affectedRows > 0) {
				$this->_msg = $affectedRows ." user records deleted.";
			}	
			else {
				$this->_msg = "No user records deleted.";
			}
			$this->_setUserList();
		}
		
		
		private function _sendReverification($user) {
			$siteConfig = $this->_url->siteConfig;
			$emailBodyTpl = new Template('./kibu/core/templates/');
			$emailBodyTpl->set("formType", "registration");
			$emailBodyTpl->set("userName", $user['userName']);
			$emailBodyTpl->set("site", $_SERVER['HTTP_HOST']);
			$emailBodyTpl->set("authCode", $user['authcode']);
			$emailBody = $emailBodyTpl->fetch('registration_reverify_email.tpl.php');
			$headers = "From: ".$siteConfig['siteAddress']."";
			$headers .= "<".$siteConfig['siteEmail'].">\n";
			$headers .= "Reply-To:".$siteConfig['siteEmail']."\n";
			$headers .= "Return-Path:".$siteConfig['siteEmail'];
			$send = mail($user['emailAddress'], 'Registration reverification email from '.$_SERVER['HTTP_HOST'].'', $emailBody, $headers).""; // utilize php mail function
			if($send) {
				return true;
			}	
			else {
				$this->_error = true;
				$this->_msg = "An error ocurred sending reverification email to " . $emailAddress;
			}
		}
		
	}
	

	class UserManagement_Search extends UserManagement {




	}

	
	
	
	class UserManagement_Purge extends UserManagement {

		private $_purgeStartDate;
		private $_purgeEndDate;
		private $_purgeStartDateFmt;
		private $_purgeEndDateFmt;

		public function __construct() {
			$this->_permAbility = "Purge Users";
			parent::__construct();
			if($this->_submit['nextStep'] == 'purge') {
				if($this->_setPurgeDates()) {
					$this->_purgeUnverified();
				}
			}				
			else {
				$this->_warning = true;
				$this->_msg = "Select a start and end date to determine a timespan in which to delete all user records that have not been verified. Start date is automatically set to the earliest join date on record. End date is set to one month ago today. Deletions will be made from the beginning of the selected start date through the end of the selected end date.";				
				$this->_setFormData();
				parent::_setFormBody();
			}
		}

		
		private function _setFormData() {
			$query = "SELECT MIN(joinDate) AS joinDate FROM userRecords";
			$this->_db->setQuery($query);
			$return = $this->_db->getAssoc();
			$minJoinDate = strtotime($return['joinDate']);
						
			$this->_formData['purgeStartDateM'] = date('m', $minJoinDate);
			$this->_formData['purgeStartDateD'] = date('d', $minJoinDate);
			$this->_formData['purgeStartDateY'] = date('Y', $minJoinDate);
			$this->_formData['purgeEndDateM'] = date('m') - 1;
			$this->_formData['purgeEndDateD'] = date('d');
			$this->_formData['purgeEndDateY'] = date('Y');			
			$this->_formData['monthOpts'] = Utility::dateMonthAbbrvArray();
			$this->_formData['dayOpts'] = Utility::dateDayArray();
			$this->_formData['yearOpts'] = Utility::pastDateYearArray();
			$this->_nextStep = "purge";
			
			$this->_formTpl = "user_purge.tpl.php";
		}
		
		private function _setPurgeDates() {
			$this->_purgeStartDate = $this->_submit['purgeStartDate']['Y']."-".$this->_submit['purgeStartDate']['M']."-".$this->_submit['purgeStartDate']['D'];
			$this->_purgeEndDate = $this->_submit['purgeEndDate']['Y']."-".$this->_submit['purgeEndDate']['M']."-".$this->_submit['purgeEndDate']['D'];
			
			if($this->_purgeEndDate < $this->_purgeStartDate) {
				$this->_error = true;
				$this->_msg = "Purge end date cannot fall before purge start date. Please make the appropriate changes below and resubmit the form.";
				$this->_setFormData();
				parent::_setFormBody();
				return false;
			}
			else {
				$this->_purgeStartDateFmt = date('D, M j, Y', strtotime($this->_purgeStartDate));
				$this->_purgeEndDateFmt = date('D, M j, Y', strtotime($this->_purgeEndDate));
				return true;
			}
			
		}


		private function _purgeUnverified() {
			$this->_nextStep = "finish";
			$query = "DELETE FROM userRecords WHERE joinDate >= '".$this->_purgeStartDate."' AND joinDate <= '".$this->_purgeEndDate."' AND emailVerified = 'n'";
			$this->_db->setQuery($query);
			if($this->_db->getAffectedRows() > 0) {
				$this->_msg = $this->_db->getAffectedRows() ." user records purged between ".$this->_purgeStartDateFmt." and ".$this->_purgeEndDateFmt."";
			}

		}	

	}

	class UserManagement_Details extends UserManagement {
		
		private $_userGUID;
		private $_userDetails;
		private $_rawPass;
		private $_sendEmail;
		
		public function __construct() {
			$this->_permAbility = "Modify User Details";
			parent::__construct();
			if(isset($_GET['userGUID'])) {
				$this->_userGUID = $_GET['userGUID'];
				$this->_setUserDetails();
				$this->_setFormData();
				parent::_setFormBody();
			}
			if(count($this->_submit)) {
				$this->_updateUserDetails();
				
			}
		}
		
		private function _setUserDetails() {
			$this->_query = "SELECT * FROM userRecords WHERE userGUID = '".$this->_userGUID."' ORDER BY userName ASC";			
			$this->_db->setQuery($this->_query);
			$this->_userDetails = $this->_db->getAssoc();
		}
		
		private function _setFormData() {
			$this->_formData = $this->_userDetails;
			$levelOpts = new FormInputSelectOptionsCollection(Utility::getUserLevels(), $this->_formData['userLevelNum']);
			$this->_formData['userLevelOpts'] = $levelOpts->GetMarkup();
			$this->_formTpl = "user_details_edit.tpl.php";
		}

		private function _updateUserDetails() {
			if($this->_checkPass()) {
				$table = "userRecords";
				
				$data = array(
					"userName" => $this->_submit['userName'],
					"password" => $this->_submit['password'],
					"emailAddress" => $this->_submit['emailAddress'],
					"userLevelNum" => $this->_submit['userLevel'],
					"forcePWChange" => $this->_checkForcePWChange()
				);
								
				$where = "userGUID = '".$this->_userGUID ."'";
								
				$this->_db->update($table, $data, $where);
				
				if(!$this->_db->error) {
					$this->_msg = "User '".$this->_submit['userName']."' updated successfully.";	
				}
				else {
					$this->_error = true;
					$this->_msg = "An error occurred updating user '".$this->_submit['userName']."': ".$this->_db->getError();
				}
				
			}
		}
		
		private function _checkForcePWChange() {
			$forcePWChange = false;			
			$currentStatus = (bool)$this->_userDetails['forcePWChange'];
			if(isset($this->_submit['forcePWChange'])) {
				$forcePWChange = true;
			}
			if($currentStatus != $forcePWChange) {
				return (int)$forcePWChange;
			}
			else {
				return (int)$currentStatus;
			}
		}
		
		private function _checkPass() {
			$passWds = $this->_submit['password'];
			if($passWds[0] != $passWds[1]) {
				$this->_error = true;
				$this->_msg = "Password and related confirmation field did not match.";
				return false;
			}			
			elseif($passWds[0] == null) {
				$this->_submit['password'] = $this->_userDetails['password'];
			}
			else {
				$this->_sendEmail = true;				
				$this->_rawPass = $passWds[0];
				$this->_submit['password'] = md5($passWds[0]);
			}
			return true;			
			
		}
		
		private function _sendEmail() {
			$pass = $this->_rawPass;
		}
		
	}

?>
