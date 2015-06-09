<?php

	 require_once './kibu/core/framework/template/Template.php';
	require_once './kibu/core/System/Utility.php';
	require_once './kibu/core/framework/url/UrlQueryString.php';

	class Modal {
		
		private $_mode;
		private $_output;
		private $_msg;
		private $_submit;
		private $_nextStep;
		private $_classPath = "./kibu";
		private $_className;
		private $_classLink;
		private $_classObj;
		private $_includeClass;
		private $_queryString;
		private $_formTpl;
		private $_pageTpl;
		private $_pageMarkup;
		private $_isForm = true;

		public function __construct() {
			$this->_setVars();
			$this->_instantiateClass();
			if($this->_isForm) {
				$this->_modalFormWrapper();
			}
			$this->_modalPageWrapper();
		}

		private function _setVars() {
			$this->_queryString =  new UrlQueryString();

			$this->_mode = $this->_queryString->mode;
			if(isset($_POST['submit'])) {
				$this->_submit = $_POST;
			}


	//		if(isset($this->_submit)) {
	//			$this->_submit = array_merge($this->_queryString->GetParts(), $this->_submit);
	//		}
	//		else {
	//			$this->_submit = $this->_queryString->GetParts();
	//		}

			$this->_submit = (isset($this->_submit)) ? array_merge($this->_queryString->GetParts(), $this->_submit) : $this->_queryString->GetParts();

			//Utility::pre($this->_queryString->GetParts());

			$this->_setClassPath();
			$this->_setClassFile();

			require_once $this->_classPath.$this->_classLink.'.php';

			if((isset($this->_mode)) && class_exists($this->_className."_".$this->_mode)) {
				$this->_className .= "_" . $this->_mode;
			}
		}

		private function _setClassPath() {
			if($this->_queryString->dir) {
				$this->_classPath .= "/core/".$this->_queryString->dir."/";
			}
			elseif($this->_queryString->module) {
				$this->_classPath .= "/modules/".$this->_queryString->module."/";
			}

		}

		private function _setClassFile() {
			if($this->_queryString->class) {
				$this->_classLink = $this->_queryString->class;
				$this->_className = $this->_queryString->class;
			}
			elseif($this->_queryString->module) {
				$this->_includeClass = $this->_queryString->module;
				$this->_classLink = $this->_includeClass."_class";
				$this->_className = $this->_includeClass;						
			}		
		}

		private function _instantiateClass() {
			$this->_classObj = new $this->_className($this->_submit);
			if(property_exists($this->_classObj, 'isForm')) {
				$this->_isForm = $this->_classObj->isForm;
			}
			$this->_output = $this->_classObj->getOutput();
			//$this->_msg = $this->_classObj->getMsg();
			$this->_nextStep = $this->_classObj->getNextStep();		
		}


		private function _modalFormWrapper() {
			$formBtns = new Template('./kibu/core/Content/templates/');
			if((isset($this->_submit['nextStep']) && $this->_submit['nextStep'] == 'finish') || ($this->_nextStep == 'close' || $this->_nextStep == 'finish')) {
				$btnExtra = "onclick=\"parent.\$.fancybox.close();\"";
				if(isset($this->_classObj->submitRedirect)) {
					$btnExtra = "onclick=\"parent.location.replace('".$this->_classObj->submitRedirect."');\"";
				}
				$vars = array(
					'nextStep' => $this->_nextStep,
					'btnType' => 'reset',
					'btnID' => 'close',
					'btnName' => 'close',
					'btnVal' => 'Close',
					'btnExtra' => $btnExtra
					);
				$formSubmitTpl = "form_submit_1btn.tpl.php";
			}
			else {
				$resetBtnExtra = "onclick=\"parent.\$.fancybox.close();\"";
				if(isset($this->_classObj->cancelRedirect)) {
					$resetBtnExtra = "onclick=\"parent.location.replace('".$this->_classObj->cancelRedirect."');\"";
				}
				$vars = array(
					'nextStep' => $this->_nextStep,
					'submitButtonID' => $this->_mode,
					'submitButtonName' => 'submit',
					'submitButtonVal' => 'Submit',
					'submitBtnExtra' => null,
					'resetButtnID' => 'reset',
					'resetButtonName' => 'reset',
					'resetButtonVal' => 'Cancel',
					'resetBtnExtra' => $resetBtnExtra
				);
				$formSubmitTpl = "form_submit_2btn.tpl.php";
			}

			$formBtns->set_vars($vars, true);
			$formSubmit = $formBtns->fetch($formSubmitTpl);

			$formTpl = new Template("./kibu/core/Content/templates/");
			$formTpl->set("ID", $this->_mode);
			$formTpl->set("formBody", $this->_output);
			$formTpl->set("method", "post");
			$formTpl->set("action", $_SERVER['PHP_SELF']."?".$this->_queryString);
			$formTpl->set("class", "modal");
			$formTpl->set("name", $this->_mode);
			$formTpl->set("formExtra", null);
			$formTpl->set("msg", $this->_classObj->getMsg());
			$formTpl->set('formSubmit', $formSubmit);
			$this->_formTpl = $formTpl->fetch("form_wrapper.tpl.php");
		}

		private function _modalPageWrapper() {
			$this->_pageTpl = new Template('./kibu/core/Content/templates/');
			if($this->_isForm) {
				$this->_pageTpl->set('modalContent', $this->_formTpl);
			}
			else {
				$this->_pageTpl->set('modalContent', $this->_output);
			}
			$this->_pageMarkup = $this->_pageTpl->fetch('modal_wrapper.tpl.php');
		}

		public function GetModalContent() {
			return $this->_pageMarkup;
		}
	}

?>
