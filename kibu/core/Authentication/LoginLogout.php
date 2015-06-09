<?php

	require_once './kibu/core/framework/template/Template.php';
	require_once './kibu/core/Authentication/Authentication.php';
	require_once './kibu/core/Navigation/Url.php';
 
	class LoginLogout extends Authentication {

		public $submitRedirect;
		public $cancelRedirect;
		
		protected $_userName;

		private $_mode;
		private $_curPage;
		private $_form;
		private $_formData;
		private $_nextStep;
		private $_restrictedContent;

		public function __construct() {
			parent::__construct();
			$this->_mode = $_GET['mode'];
			$this->_curPage = $_GET['curPage'];
			if(isset($_GET['restrictedcontent'])) {
				$this->_restrictedContent = $_GET['restrictedcontent'];
			}
			if($this->_mode == 'login') {
				if($this->_restrictedContent == true) {
					$this->cancelRedirect = "/";
				}
				else {
					$this->cancelRedirect = $this->_curPage;
				}
				if(isset($_POST['submit'])) {
					$this->_submit = $_POST;
					if($this->setLogin()) {
						$this->submitRedirect = $this->_curPage;
						if((bool)$this->_userInfo['forcePWChange']) {
							$this->_nextStep = "close";
							$this->_warning = true;
							$this->_msg = "An action was performed on your account that requires you to change your password. It is recommended that you <a href=\"/modal.php?dir=Authentication&amp;class=Registration&amp;mode=changepassword&amp;curPage=".$this->_curPage."\" title=\"Change Password\">change it now</a>. You will continue to see this message each time you log in until you do so.";
						}
						elseif($this->_userInfo['emailVerified'] == 'n') {
							$this->_nextStep = 'close';
							$this->_warning = true;
							$this->_msg = "You are logged in, but our records indicate your email address has not yet been verified. Would you like to <a id=\"confirm\" href=\"/modal.php?dir=Authentication&amp;class=Registration&amp;mode=confirm&amp;curPage=".$this->_curPage."\" title=\"Verify Email\">Verify your email address</a> now? You will continue to receive this message and possibly have reduced user priveleges until you do so.";
						}
						else {
							$this->_nextStep = 'close';
							$this->_msg = "You are now logged in!";
						}
					}
					else {
						$this->_setVars();
						$this->_loginForm();
					}
				}
				else {
					$this->_setVars();
					$this->_loginForm();
				}
			}
			elseif($this->_mode == 'logout') {
				$this->_logout();
				$this->_msg = "You are now logged out!";
				$this->submitRedirect = $this->_curPage;
				$this->_nextStep = 'close';
			}
		}

		public function getOutput() {
			return $this->_form;
		}

		public function getNextStep() {
			return $this->_nextStep;
		}

		private function _loginForm() {
			$loginTpl = new Template('./kibu/core/Authentication/templates/');
			$loginTpl->set_vars($this->_formData, true);
			$this->_form = $loginTpl->fetch('login_form.tpl.php');
		}

		private function _logout() {
			$uCookie = new Cookie();
			$uCookie->_cookieName = $this->uCookie;
			$uCookie->eatCookie();
			$pCookie = new Cookie();
			$pCookie->_cookieName = $this->pCookie;
			$pCookie->eatCookie();				
		}

		private function _setVars() {
			if(count($this->_submit)) {
				$this->_formData = $this->_submit;
			}
			else {
				$this->_formData['userName'] = null;
				if($this->_restrictedContent == true) {
					$this->_warning = true;
					$this->_msg = "You have navigated to a restricted page. Please login with the appropriate credentials in order to view this page.";
				}
			}	
		}

		public function getTemplateVars() {
			return $this->_formData;
		}

	}
?>