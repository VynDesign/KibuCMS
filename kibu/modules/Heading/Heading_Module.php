<?php

	require_once './kibu/core/Content/Module.php';
	require_once './kibu/core/Content/interface/IModuleEdit.php';

	class Heading_Module extends Module {
		
		protected $headingTxt;


		public function __construct($requestedModule) {
			parent::__construct($requestedModule);
			$this->headingTxt = $requestedModule['assetBody'];			
			$this->_params['headingTxt'] = $this->headingTxt;
		}
		
	}
	
	class Heading_Module_Edit extends Heading_Module implements IModuleEdit {
		
		public function __construct($requestedModule) {
			parent::__construct($requestedModule);
		}
		
		public function getError() {
		
		}

		public function getNextStep() {
		
		}

		public function getSubmit() {
		
		}

		public function setEditParamOpts() {
		
		}
		
	}

?>
