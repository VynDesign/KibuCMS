<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'kibu/core/class/Authentication.php';
require_once 'kibu/core/class/Database.php';
require_once 'kibu/core/class/Url.php';
require_once 'kibu/core/class/Template.php';

class Modal {
		
		private $_mode;
		private $_formBody;
		private $_msg;
		private $_submit;
		private $_nextStep;
		private $_classPath;
		private $_className;
		private $_classLink;
		private $_classObj;
		private $_includeClass;
		private $_fullQueryString;
		
		public function __construct() {
				if(isset($_POST['submit'])) {
						$this->_submit = $_POST;
				}
				if(isset($_GET['class'])) {
						$this->_classPath = "./kibu/core/class/";
						$this->_classLink = $_GET['class'];
						$this->_className = $_GET['class'];
				}
				elseif(isset($_GET['module'])) {
						$this->_includeClass = $_GET['module'];
						$this->_classPath = "./kibu/modules/".$_GET['module']."/";
						$this->_classLink = $_GET['module']."_class";
						$this->_className = $_GET['module'];
				}
				$this->_mode = $_GET['mode'];
				$this->_fullQueryString =  $_SERVER['QUERY_STRING'];

				require_once $this->_classPath.$this->_classLink.'.php';
				$this->_classObj = new $this->_className(null, null);
				$this->_formBody = $this->_classObj->outputFormBody();
				$this->_msg = $this->_classObj->getMsg();
				$this->_nextStep = $this->_classObj->getNextStep();
				$this->_modalFormWrapper();
		}

		private function _modalFormWrapper() {
				$formBtns = new Template('./kibu/templates/');
				if($this->_submit['nextStep'] == 'finish' || $this->_nextStep == 'close') {
						$btnExtra = "onclick=\"Modalbox.hide(";
						if(isset($this->_classObj->submitRedirect)) {
								$btnExtra .= "{afterHide: function() { location.replace('".$this->_classObj->submitRedirect."');} }";
						}
						$btnExtra .= ");\"";
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
						$resetBtnExtra = "onclick=\"Modalbox.hide(";
						if(isset($this->_classObj->cancelRedirect)) {
								$resetBtnExtra .= "{afterHide: function() { location.replace('".$this->_classObj->cancelRedirect."');} }";
						}
						$resetBtnExtra .= ");\"";
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

				$formExtra = "onsubmit=\"Modalbox.show('". $_SERVER['PHP_SELF']."?".$this->_fullQueryString."', {title: 'Sending status'";

				$formExtra .= ", params: Form.serialize('".$this->_mode."'), method:'post'";

				$formExtra .= ", overlayClose: false}); return false;\"'";

				$formTpl = new Template("./kibu/templates/");
				$formTpl->set("ID", $this->_mode);
				$formTpl->set("formBody", $this->_formBody);
				$formTpl->set("method", "post");
				$formTpl->set("action", $_SERVER['PHP_SELF']."?".$this->_fullQueryString);
				$formTpl->set("class", "modal");
				$formTpl->set("name", $this->_mode);
				$formTpl->set("formExtra", $formExtra);
				$formTpl->set("msg", $this->_msg);
				$formTpl->set('formSubmit', $formSubmit);
				echo $formTpl->fetch("form_wrapper.tpl.php");
		}
}

$db = new Database(); // instantiate Database class, connect to database
$url = new URL(); // instantiate URL class
$auth = new Authentication(); // instantiate Authentication class
$modal = new Modal(); // instantiate Modal class to run the requested process in a modal window

?>
