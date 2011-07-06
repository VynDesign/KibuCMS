<?php

		require_once './kibu/core/class/Cookie.php';
		require_once './kibu/core/class/Template.php';
		require_once './kibu/core/class/Authentication.php';
		require_once './kibu/core/class/Url.php';
 
class LoginLogout {

		protected $_userName;

		private $_mode;
		private $_auth;
		private $_userInfo;
		private $_curPage;
		private $_form;
		private $_formData;
		private $_nextStep;
		private $_msg;
		private $_submit;
		private $_restrictedContent;
		public $submitRedirect;
		public $cancelRedirect;

		public function __construct() {
				$this->_mode = $_GET['mode'];
				$this->_curPage = $_GET['curPage'];
				if(isset($_GET['restrictedcontent'])) {
						$this->_restrictedContent = $_GET['restrictedcontent'];
				}
				$this->_auth = new Authentication();

				if($this->_mode == 'login') {
						if($this->_restrictedContent == true) {
							$this->cancelRedirect = "/";
						}
						else {
								$this->cancelRedirect = $this->_curPage;
						}
						if(isset($_POST['submit'])) {
								$this->_submit = $_POST;
								if($this->_auth->setLogin()) {
									$this->_userInfo = $this->_auth->getUserInfo();
									$this->submitRedirect = $this->_curPage;

									if($this->_userInfo['forcePWChange'] == true) {
											$this->_nextStep = "close";
											$this->_msg = "You are now logged in using your temporary randomly generated password. It is recommended that you <a href=\"/modal.php?class=Registration&amp;mode=changepassword&amp;curPage=".$this->_curPage."\" title=\"Change Password\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">change it to something you can more easily remember now</a>. You will continue to see this message each time you log in until you do so.";
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

		public function outputFormBody() {
				return $this->_form;
		}

		public function getNextStep() {
				return $this->_nextStep;
		}

		public function getMsg() {
				return $this->_msg;
		}

		private function _loginForm() {
				$loginTpl = new Template('./kibu/templates/');
				$loginTpl->set_vars($this->_formData, true);
				$this->_form = $loginTpl->fetch('login_form.tpl.php');
		}

		private function _logout() {
				$uCookie = new Cookie();
				$uCookie->_cookieName = $this->_auth->uCookie;
				$uCookie->eatCookie();
				$pCookie = new Cookie();
				$pCookie->_cookieName = $this->_auth->pCookie;
				$pCookie->eatCookie();				
		}

		private function _setVars() {
				if($this->_submit['submit']) {
						$this->_formData = $this->_submit;
						$this->_msg = $this->_auth->getMsg();
				}
				else {
						$this->_formData['userName'] = null;
						if($this->_restrictedContent == true) {
								$this->_msg = "<p>You have navigated to a restricted page. Please login with the appropriate credentials in order to view this page.</p>";
						}
				}
				
				$this->_formData['msg'] = $this->_msg;
		}

		public function getTemplateVars() {
				return $this->_formData;
		}

}
?>
