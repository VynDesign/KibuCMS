<?php

	require_once './kibu/core/Authentication/Authentication.php';

	class Permissions extends Authentication { 
		
		private $_abilities = array(); 
		
		public function __construct() {
			parent::__construct();
			$this->_setAbilities();
		}
		
		// populate roles with their associated permissions
		protected function _setAbilities() {
	        $query = "SELECT Abilities.permAbilityGUID, Abilities.permAbilityName
						FROM permissionsAbilities as Abilities, permissionsRoles as Roles, permissionsRolesByUser as RolesXUser, permissionsAbilitiesByRole as AbilitiesXRole
						WHERE Abilities.permAbilityGUID = AbilitiesXRole.permAbilityGUID
							AND Roles.permRoleGUID = AbilitiesXRole.permRoleGUID
							AND RolesXUser.permRoleGUID = Roles.permRoleGUID
							AND RolesXUser.userGUID = '" . $this->_userGUID . "'";
			
			$this->_db->setQuery($query);
			if($this->_db->getNumRows() > 0) {
				$this->_db->getAssocArray();
				foreach($this->_db->returnData as $key => $perm) {
					$this->_abilities[$perm['permAbilityGUID']] = $perm['permAbilityName'];
				}
			}
		}

		// check if user has a specific privilege
		public function hasAbility($ability, $guid = false) {
			foreach ($this->_abilities as $abilityGUID => $abilityName) {
				if(($guid && $ability == $abilityGUID) || (!$guid && $ability == $abilityName)) {
					return true;
				}
			}
			return false;
		}
	}
?>
