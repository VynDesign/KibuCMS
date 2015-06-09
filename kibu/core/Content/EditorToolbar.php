<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './kibu/core/Authentication/Authentication.php';
require_once './kibu/core/framework/data/Cookie.php';

/**
 * Description of EditorToolbar
 *
 * @author vyn
 */
class EditorToolbar {

		private $_auth;
		private $_showToolbarCookie;
		private $_showToolbar;
		private $_toolbar;
		private $_contentRecord;
		private $_contentLink;
		private $_mode = 'html';

		public function  __construct($contentRecord) {
			if(isset($_GET['mode'])) {
				$this->_mode = $_GET['mode'];
			}
			$this->_auth = new Authentication();
			$this->_contentRecord = $contentRecord;
			$this->_contentLink = "/".$contentRecord['sectionName']."/".$contentRecord['titleClean'].".html";
			if($this->_auth->getUserLevel() < 100) {
				return false;
			}
			else {
				$this->_setToolbar();
			}
		}

		private function _setToolbar() {
			if($this->_mode == 'edit') {
				$editLinkText = 'Cancel Edit';
				$mode = 'html';
			}
			else {
				$editLinkText = 'Edit Content';
				$mode = 'edit';
			}
			
			$vars['contentLink'] = $this->_contentLink;
			$vars['editLinkText'] = $editLinkText;
			$vars['mode'] = $mode;
			$vars['contentRecordNum'] = $this->_contentRecord['contentRecordNum'];
			$vars['sectionID'] = $this->_contentRecord['sectionID'];
			$vars['siteConfigID'] = $this->_contentRecord['siteConfigID'];
			
			$toolbarTpl = new Template('./kibu/core/Content/templates/');
			$toolbarTpl->set_vars($vars);
			$this->_toolbar = $toolbarTpl->fetch('editor_toolbar.tpl.php');
		}

		private function _showHide() {
			$this->_showToolbarCookie = new Cookie('showToolbar');
			if(!$this->_showToolbarCookie->isCookieSet()) {
				$this->_showToolbarCookie->bakeCookie('showToolbar', 'hide');
			}
			$this->_showToolbar = $this->_showToolbarCookie->getCookieValue();
			echo $this->_showToolbar;
		}

		public function outputToolbar() {
			return $this->_toolbar;
		}
}
?>
