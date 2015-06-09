<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SystemManagement
 *
 * @author vyn
 */

	require_once './kibu/core/Authentication/Authentication.php';
	require_once './kibu/core/framework/template/Template.php';
	require_once './kibu/core/Authentication/Permissions.php';
	require_once './kibu/core/framework/html/form/Form.php';
	require_once './kibu/core/framework/html/form/FormInputSelectOptionsCollection.php';

	abstract class SystemManagement extends Form {
	
		protected $_submit;
		protected $_form;
		protected $_formTpl;
		protected $_tplDir = './kibu/core/SystemManagement/templates/';
		protected $_formData;		
		protected $_mode;
		protected $_nextStep = null;
		protected $_error;
		protected $_warning;
		protected $_db;
		protected $_url;
		protected $_auth;
		protected $_perms;
		protected $_permAbility;
		protected $_siteTemplates;
	
		public $isForm = true;
		public $requiresAuth = true;
	
		public function __construct() {
			
			parent::__construct();
			
			global $url;
			$this->_url = $url;
			
			$this->_mode = $this->_url->getMode();
						
			global $db;
			$this->_db = $db;
			
			global $auth;
			$this->_auth = $auth;

			$this->_perms = new Permissions();
			$this->_checkAuth();
			$this->_checkAbility();
			
			if(count($_POST)) {
				$this->_submit = $_POST;
			}
		}
		
		protected function _checkAuth() {
			if($this->_mode != 'html' && $this->requiresAuth && $this->_perms->getUserLevel() < 100) {
				header("Location:/");
			}
		}		
		
		protected function _checkAbility() {
			if((isset($this->_permAbility)) && ($this->_mode != 'html' && $this->requiresAuth)) {
				if($this->_perms->hasAbility($this->_permAbility)) {
					return true;
				}
				else {
					$this->_formData = null;
					$this->_error = true;
					$this->_msg = "You have insufficient permissions to perform the requested task.";
					$this->_nextStep = "close";
					$this->_tplDir = "./kibu/core/System/templates/";
					$this->_formTpl = "kibu_err_generic.tpl.php";				
					return false;
				}
			}
		}			
		
		protected function _setFormBody() {
 			$form = new Template($this->_tplDir);
			$form->set_vars($this->_formData);
			$this->_form = $form->fetch($this->_formTpl);
		}
		
		protected function _getSiteTemplates() {
			$query = "SELECT siteTemplates.templateID, siteTemplates.templateName 
				FROM siteTemplates, siteTemplateTypes
					WHERE siteTemplateTypes.templateTypeName = 'Site'
						AND siteTemplates.templateTypeID = siteTemplateTypes.templateTypeID
					ORDER BY templateID ASC";

			$this->_db->setQuery($query);
			while($result = $this->_db->getAssoc()){
				$this->_siteTemplates[$result['templateID']] = $result['templateName'];
			}			
		}		
		
		public function getOutput() {
			return $this->_form;
		}

		public function getNextStep() {
			return $this->_nextStep;
		}	
	}

?>
