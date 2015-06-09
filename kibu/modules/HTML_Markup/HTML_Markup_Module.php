<?php


	require_once './kibu/core/Content/Module.php';
	require_once './kibu/core/Content/interface/IModuleEdit.php';

	class HTML_Markup_Module extends Module {

		
		protected $htmlMarkup;


		public function __construct($requestedModule) {
			parent::__construct($requestedModule);
			$this->htmlMarkup = $requestedModule['assetBody'];
			$this->_params['htmlMarkup'] = $this->htmlMarkup;
		}
		
		
		
	}
	
	
	
	
	class HTML_Markup_Module_Edit extends HTML_Markup_Module implements IModuleEdit {
		
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
