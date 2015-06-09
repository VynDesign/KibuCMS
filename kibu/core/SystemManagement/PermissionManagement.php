<?php


	require_once './kibu/core/SystemManagement/SystemManagement.php';
	require_once './kibu/core/System/Utility.php';


	class PermissionManagement extends SystemManagement {

		protected $_allRoles;
		protected $_allAbilities;
		protected $_roleGUID;
		protected $_abilityGUID;
		protected $_userGUID;
		
		
		public function __construct() {
			parent::__construct();
			
			if(isset($_GET['role'])) {
				$this->_roleGUID = $_GET['role'];
			}
			
			if(isset($_GET['user'])) {
				$this->_userGUID = $_GET['user'];
			}
			
			if(isset($_GET['ability'])) {
				$this->_abilityGUID = $_GET['ability'];
			}
			
		}
		
		protected function _setAllAbilities() {
			$query = "SELECT permAbilityGUID, permAbilityDesc, permAbilityName FROM permissionsAbilities ORDER BY permAbilityName";
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				while($return = $this->_db->getAssoc()) {
					$this->_allAbilities[$return['permAbilityGUID']] = $return;
				}
			}
		}
		
		protected function _addRoleAbilities() {
			if(isset($this->_submit['addAbilities'])) {
				foreach($this->_submit['addAbilities'] as $abilityGUID => $abilityName) {
					//TODO implement transaction
					
					//$query = "INSERT INTO permissionsAbilitiesByRole SET permAbilityGUID = '" . $abilityGUID . "', permRoleGUID = '" . $this->_submit['roleGUID'] . "'";
					
					$table = "permissionsAbilitiesByRole";
					$data = array(
						'permAbilityGUID' => $abilityGUID,
						'permRoleGUID' => $this->_submit['roleGUID']
						
					);
					
					$this->_db->insert($table, $data);
					if($this->_db->getError()) {
						$this->_error = true;
						$this->_msg = "There was an error adding ".$abilityName."' to the specified role.";
						die;
					}
				}
			}
		}
		
		protected function _removeRoleAbilities() {
			if(isset($this->_submit['removeAbilities'])) {
				foreach($this->_submit['removeAbilities'] as $guid => $status) {
					$this->_submit['removeAbilities'][$guid] = "'".$guid."'";
				}
				$abilityGUIDString = Utility::concatArray($this->_submit['removeAbilities'], ", ");
				$query = "DELETE FROM permissionsAbilitiesByRole WHERE permAbilityGUID IN (" . $abilityGUIDString . ") AND permRoleGUID = '" . $this->_submit['roleGUID'] . "'";
				$this->_db->setQuery($query);
				if($this->_db->getError()) {
					$this->_error = true;
					$this->_msg = "There was an error removing the selected abilities from the specified role.";
					die;
				}
			}
		}
		
		protected function _setAllRoles() {
			$query = "SELECT permRoleGUID, permRoleDesc, permRoleName FROM permissionsRoles ORDER BY permRoleName";
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				while($return = $this->_db->getAssoc()) {
					$this->_allRoles[$return['permRoleGUID']] = $return;
				}
			}			
		}
		
		protected function _addUserRoles() {
			if(isset($this->_submit['addRoles'])) {
				foreach($this->_submit['addRoles'] as $roleGUID => $roleName) {
					//TODO Implement transaction
					
					//$query = "INSERT INTO permissionsRolesByUser SET permRoleGUID = '" . $roleGUID . "', userGUID = '" . $this->_userGUID . "'";
					
					$table = "permissionsRolesByUser";
					$data = array( 
						'permRoleGUID' => $roleGUID,
						'userGUID' => $this->_userGUID
					);
					
					//$this->_db->setQuery($query);
					$this->_db->insert($table, $data);
					if($this->_db->getError()) {
						$this->_error = true;
						$this->_msg = "There was an error adding ".$roleName."' to ".$this->_userName.".";
					}					
				}
			}			
		}
		
		protected function _removeUserRoles() {
			if(isset($this->_submit['removeRoles'])) {
				foreach($this->_submit['removeRoles'] as $guid => $status) {
					$this->_submit['removeRoles'][$guid] = "'".$guid."'";
				}				
				$roleGUIDString = Utility::concatArray($this->_submit['removeRoles'], ", ");
				$query = "DELETE FROM permissionsRolesByUser WHERE permRoleGUID IN (" . $roleGUIDString . ") AND userGUID = '" . $this->_userGUID . "'";
				$this->_db->setQuery($query);
				if($this->_db->getError()) {
					$this->_error = true;
					$this->_msg = "There was an error removing the selected roles from ".$this->_userName.".";
				}				
			}			
		}
	}
	
	
	
	
	class PermissionManagement_CreateAbility extends PermissionManagement {
		
		
		public function __construct() {
			parent::__construct();
			$this->_permAbility = "Create Permission Ability";
			
			$this->_formTpl = "permissions_edit_ability.tpl.php";			
			
			if(count($this->_submit)) {
				$this->_insertNewAbility();
			}
			
			$this->_setFormData();
			parent::_setFormBody();
						
		}
		
		private function _setFormData() {
			$this->_formData['permAbilityName'] = null;
			$this->_formData['permAbilityAbbrv'] = null;
			$this->_formData['permAbilityDesc'] = null;
			$this->_formData['permAbilityGUID'] = null;
			
			if(count($this->_submit) || $this->_error) {
				$this->_formData = $this->_submit;
			}
			
			$this->_formData['legend'] = "Create New User Ability";
		}
		
		private function _insertNewAbility() {
			$guid = Utility::guidGen();
			$this->_submit['permAbilityGUID'] = $guid;
			$query = "INSERT INTO permissionsAbilities SET 
					permAbilityName = '". $this->_submit['permAbilityName'] ."', 
					permAbilityAbbrv = '" . $this->_submit['permAbilityAbbrv'] . "', 
					permAbilityDesc = '" . $this->_submit['permAbilityDesc'] . "', 
					permAbilityGUID = '" . $guid . "'";
			
			$table = "permissionsAbilities";
			
			$data = array(
				'permAbilityName' => $this->_submit['permAbilityName'], 
				'permAbilityAbbrv' => $this->_submit['permAbilityAbbrv'], 
				'permAbilityDesc' => $this->_submit['permAbilityDesc'], 
				'permAbilityGUID' => $guid
			);
			
			$this->_db->insert($table, $data);
			if(!$this->_db->error) {
				$this->_msg = "New permission ability successfully added to the database.";
			}
			else {
				$this->_error = true;
				$this->_msg = "There was a problem adding new ability to the database.";
			}
		}
		
		
	}
	
	
	
	
	class PermissionManagement_CreateRole extends PermissionManagement {
		
		
		public function __construct() {
			parent::__construct();
			$this->_setAllAbilities();
			$this->_permAbility = "Create Permission Role";
			
			$this->_formTpl = "permissions_edit_role.tpl.php";
			
			if((count($this->_submit)) && $this->_checkExists()) {
				$this->_insertNewRole();
			}
			
			$this->_setFormData();
			parent::_setFormBody();
		}
		
		
		private function _setFormData() {
			$this->_formData['permRoleName'] = null;
			$this->_formData['permRoleAbbrv'] = null;
			$this->_formData['permRoleDesc'] = null;
			$this->_formData['permRoleGUID'] = null;
			if(count($this->_submit) || $this->_error) {
				$this->_formData = $this->_submit;
			}
			$this->_formData['unassignedAbilities'] = $this->_allAbilities;
			$this->_formData['assignedAbilities'] = null;
			$this->_formData['legend'] = "Create New Role";			
		}
		
		protected function _setAllAbilities() {
			parent::_setAllAbilities();
			foreach($this->_allAbilities as $abilityGUID => $abilityData) {
				$this->_allAbilities[$abilityGUID] = $abilityData['permAbilityName'];
			}
		}		
		
		private function _checkExists() {
			$query = "SELECT COUNT(permRoleGUID) AS rows FROM permissionsRoles WHERE permRoleName = '".$this->_submit['permRoleName']."' OR permRoleAbbrv = '".$this->_submit['permRoleAbbrv']."'";
			$this->_db->setQuery($query);
			if($this->_db->getNumRows > 0) {
				$numrows = $this->_db->getAssoc();
				if($numrows['rows'] > 0) {
					$this->_error = true;
					$this->_msg = "A Role by that name or abbreviation already exists. Please resubmit the form with a different role name and/or abbreviation.";
					return false;
				} 
				else {
					return true;
				}
			}
			return false;
		}
		
		private function _insertNewRole() {
			$this->_submit['roleGUID'] = Utility::guidGen();
			$this->_submit['permRoleGUID'] = $guid;
//			$query = "INSERT INTO permissionsRoles SET 
//					permRoleName = '". $this->_submit['permRoleName'] ."', 
//					permRoleAbbrv = '" . $this->_submit['permRoleAbbrv'] . "', 
//					permRoleDesc = '" . $this->_submit['permRoleDesc'] . "', 
//					permRoleGUID = '" . $this->_submit['roleGUID'] . "'";
			
			$table = "permissionsRoles";
			
			$data = array(
				'permRoleName' => $this->_submit['permRoleName'], 
				'permRoleAbbrv' => $this->_submit['permRoleAbbrv'], 
				'permRoleDesc' => $this->_submit['permRoleDesc'], 
				'permRoleGUID' => $this->_submit['roleGUID']
			);

			$this->_db->insert($table, $data);
			if(!$this->_db->error) {
				$this->_msg = "New permission role successfully added to the database.";
				parent::_addRoleAbilities();				
			}
			else {
				$this->_error = true;
				$this->_msg = "There was a problem adding new role to the database.";
			}
		}
		
	}
	
	
	
	class PermissionManagement_AllAbilities extends PermissionManagement {
		
		
		public function __construct() {
			parent::__construct();
			$this->_permAbility = "View All Abilities";
			
			$this->_setFormData();
			
			parent::_setFormBody();
		}
		
		
		private function _setFormData() {
			parent::_setAllAbilities();			
			$this->_formTpl = "permissions_all_abilities.tpl.php";
			$this->_formData['legend'] = "All Abilities";
			$this->_formData['abilities'] = $this->_allAbilities;						
		}
		
	}	
		
	
	
	
	
	class PermissionManagement_AllRoles extends PermissionManagement {
		
		
		public function __construct() {
			parent::__construct();
			$this->_permAbility = "View All Roles";

			$this->_setFormData();
			
			parent::_setFormBody();
		}
		
		private function _setFormData() {
			parent::_setAllRoles();			
			$this->_formTpl = "permissions_all_roles.tpl.php";
			$this->_formData['legend'] = "All Roles";
			$this->_formData['roles'] = $this->_allRoles;
			

		}
		
		
		
	}
	
	
	
	
	
	class PermissionManagement_ModifyRole extends PermissionManagement {
		
		private $_roleData;
		private $_assignedAbilities;
		private $_unassignedAbilities;
		
		
		public function __construct() {
			parent::__construct();	
			$this->_permAbility = "Modify Permission Role";
			
			if(isset($this->_roleGUID)) {
				$this->_formTpl = "permissions_edit_role.tpl.php";				
				$this->_setRoleData();
				$this->_setAssignedAbilities();
				$this->_setAllAbilities();				
				$this->_setUnassignedAbilities();
			}
			else {
				$this->_formTpl = "permissions_choose_role.tpl.php";
				parent::_setAllRoles();
			}			
			
			if(count($this->_submit)) {
				$this->_modifyRole();
			}
			
			$this->_setFormData();
			
			parent::_setFormBody();
		}
		
		private function _setRoleData() {
			$query = "SELECT * FROM permissionsRoles WHERE permRoleGUID = '" . $this->_roleGUID . "'";
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				$this->_roleData = $this->_db->getAssoc();
			}
		}
		
		private function _setAssignedAbilities() {
			$query = "SELECT permissionsAbilities.permAbilityGUID, permissionsAbilities.permAbilityName FROM permissionsAbilities, permissionsAbilitiesByRole WHERE permissionsAbilitiesByRole.permRoleGUID = '".$this->_roleGUID."' AND permissionsAbilities.permAbilityGUID = permissionsAbilitiesByRole.permAbilityGUID ORDER BY permissionsAbilities.permAbilityName";			
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				while($return = $this->_db->getAssoc()) {
					$this->_assignedAbilities[$return['permAbilityGUID']] = $return['permAbilityName'];
				}
			}			
		}
		
		protected function _setAllAbilities() {
			parent::_setAllAbilities();
			foreach($this->_allAbilities as $abilityGUID => $abilityData) {
				$this->_allAbilities[$abilityGUID] = $abilityData['permAbilityName'];
			}
		}
		
		private function _setUnassignedAbilities() {
			if(count($this->_assignedAbilities)) {
				$this->_unassignedAbilities = array_diff($this->_allAbilities, $this->_assignedAbilities);			
			}
			else {
				$this->_unassignedAbilities = $this->_allAbilities;
			}
		}
		
		private function _setFormData() {
			$this->_formData = $this->_roleData;
			if(count($this->_submit) || $this->_error) {
				$this->_formData = $this->_submit;
			}
			$this->_formData['assignedAbilities'] = $this->_assignedAbilities;
			$this->_formData['unassignedAbilities'] = $this->_unassignedAbilities;
			$this->_formData['legend'] = "Modify Role";				
		}
		
		
		private function _modifyRole() {
			$this->_submit['roleGUID'] = $this->_roleGUID;
//			$query = "UPDATE permissionsRoles SET 
//					permRoleName = '". $this->_submit['permRoleName'] ."', 
//					permRoleAbbrv = '" . $this->_submit['permRoleAbbrv'] . "', 
//					permRoleDesc = '" . $this->_submit['permRoleDesc'] . "', 
//					permRoleGUID = '" . $this->_submit['roleGUID'] . "'
//				WHERE permRoleGUID = '".$this->_roleGUID."'";
			
			$table = "permissionsRoles";
			
			$data = array(
				'permRoleName' => $this->_submit['permRoleName'], 
				'permRoleAbbrv' => $this->_submit['permRoleAbbrv'], 
				'permRoleDesc' => $this->_submit['permRoleDesc'], 
				'permRoleGUID' => $this->_submit['roleGUID']
			);
			
			$where = "permRoleGUID = '".$this->_roleGUID."'";
			
			$this->_db->update($table, $data, $where);
			if(!$this->_db->error) {
				$this->_msg = "Permission role successfully updated in the database.";
				parent::_addRoleAbilities();
				parent::_removeRoleAbilities();
			}
			else {
				$this->_error = true;
				$this->_msg = "There was a problem updating role in the database:" . $this->_db->getError();
			}
		}		
	}

	

	
	class PermissionManagement_ModifyAbility extends PermissionManagement {
		
		private $_abilityData;
		
		
		public function __construct() {
			parent::__construct();
			$this->_permAbility = "Modify Permission Ability";
			$this->_formTpl = "permissions_edit_ability.tpl.php";
			
			$this->_setAbilityData();
			
			if(count($this->_submit)) {
				$this->_updateAbility();
			}
			
			$this->_setFormData();
			parent::_setFormBody();
						
		}
		
		private function _setAbilityData() {
			$query = "SELECT * FROM permissionsAbilities WHERE permAbilityGUID = '" . $this->_abilityGUID . "'";
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				$this->_abilityData = $this->_db->getAssoc();
			}
		}	
		
		private function _setFormData() {
			$this->_formData = $this->_abilityData;

			if(count($this->_submit) || $this->_error) {
				$this->_formData = $this->_submit;
			}
			
			$this->_formData['legend'] = "Modify User Ability";
		}
		
		private function _updateAbility() {
			$this->_submit['abilityGUID'] = $this->_abilityGUID;
//			$query = "UPDATE permissionsAbilities SET 
//					permAbilityName = '". $this->_submit['permAbilityName'] ."', 
//					permAbilityAbbrv = '" . $this->_submit['permAbilityAbbrv'] . "', 
//					permAbilityDesc = '" . $this->_submit['permAbilityDesc'] . "'
//				WHERE
//					permAbilityGUID = '" . $this->_abilityGUID . "'";
			
			$table = "permissionsAbilities";
			
			$data = array(
				'permAbilityName' => $this->_submit['permAbilityName'], 
				'permAbilityAbbrv' => $this->_submit['permAbilityAbbrv'], 
				'permAbilityDesc' => $this->_submit['permAbilityDesc']
			);
			
			$where = "permAbilityGUID = '" . $this->_abilityGUID . "'";
			
			$this->_db->update($table, $data, $where);
			if(!$this->_db->error) {
				$this->_msg = "New permission ability successfully updated in the database.";
			}
			else {
				$this->_error = true;
				$this->_msg = "There was a problem updating ability in the database.";
			}
		}
	}

	
	
	
	class PermissionManagement_UserRoles extends PermissionManagement {
		
		private $_assignedRoles;
		private $_unassignedRoles;
		protected $_userName;
		
		public function __construct() {
			parent::__construct();
			$this->_permAbility = "Manage User Roles";
			$this->_setAllRoles();
			$this->_setAssignedRoles();
			$this->_setUnassignedRoles();
			$this->_setUserData();
			
			if(count($this->_submit)) {
				$this->_modifyUserRoles();
			}
			
			$this->_formTpl = "permissions_assign_user_role.tpl.php";
			$this->_setFormData();
			parent::_setFormBody();
			
		}
		
		protected function _setAllRoles() {
			parent::_setAllRoles();
			foreach($this->_allRoles as $roleGUID => $roleData) {
				$this->_allRoles[$roleGUID] = $roleData['permRoleName'];
			}
		}		
		
		
		private function _setAssignedRoles() {
			$query = "SELECT permissionsRoles.permRoleGUID, permissionsRoles.permRoleName FROM permissionsRoles, permissionsRolesByUser WHERE permissionsRolesByUser.userGUID = '".$this->_userGUID."' AND permissionsRoles.permRoleGUID = permissionsRolesByUser.permRoleGUID ORDER BY permissionsRoles.permRoleName";			
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				while($return = $this->_db->getAssoc()) {
					$this->_assignedRoles[$return['permRoleGUID']] = $return['permRoleName'];
				}
			}			
		}
		
		
		private function _setUnassignedRoles() {
			if(count($this->_assignedRoles)) {
				$this->_unassignedRoles = array_diff($this->_allRoles, $this->_assignedRoles);
		 	}
	 		else {
				$this->_unassignedRoles = $this->_allRoles;
			}
		}
		
		private function _setUserData() {
			$query = "SELECT userName FROM userRecords WHERE userGUID = '".$this->_userGUID."'";
			$this->_db->setQuery($query);
			$return = $this->_db->getAssoc();
			$this->_userName = $return['userName'];
		}
		
		private function _modifyUserRoles() {
			$this->_submit['userGUID'] = $this->_userGUID;
			parent::_addUserRoles();
			parent::_removeUserRoles();
			if(!$this->_error) {
				$this->_msg = "User roles successfully updated in the database for ".$this->_userName.".";
			}
		}
		
		private function _setFormData() {
			if(count($this->_submit) || $this->_error) {
				$this->_formData = $this->_submit;
			}
			$this->_formData['userName'] = $this->_userName;
			$this->_formData['assignedRoles'] = $this->_assignedRoles;
			$this->_formData['unassignedRoles'] = $this->_unassignedRoles;
			$this->_formData['legend'] = "Modify User Roles";				
		}
	}
	
?>
