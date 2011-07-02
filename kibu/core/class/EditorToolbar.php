<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './kibu/core/class/Authentication.php';
require_once './kibu/core/class/Cookie.php';

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
		private $_mode = null;

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
				$this->_toolbar = "<div class=\"editorToolbar\">\n";
				$this->_toolbar .= "<ul id=\"tabs\">\n";
				$this->_toolbar .= "<lh>Admin Toolbar</lh>\n";
				$this->_toolbar .= "<li><a href=\"#pageoptions\">Page Options</a>\n";
				$this->_toolbar .= "</li>\n";

				$this->_toolbar .= "<li><a href=\"#sectionoptions\">Section Options</a>\n";
				$this->_toolbar .= "</li>\n";

				$this->_toolbar .= "<li><a href=\"#siteoptions\">Site Options</a>\n";
				$this->_toolbar .="</li>\n";
				$this->_toolbar .= "</ul>\n";

				$this->_toolbar .= "<div class=\"panel\" id=\"pageoptions\">\n";
				if((isset($_GET['mode'])) && $_GET['mode'] == 'edit') {
						$this->_toolbar .= "<a href=\"".$this->_contentLink."?mode=html\">Cancel Edit</a>";
				}
				else {
						$this->_toolbar .= "<a href=\"".$this->_contentLink."?mode=edit\">Edit Content</a>";
				}
				$this->_toolbar .= " | <a href=\"/modal.php?class=PageSettings&amp;mode=pagesettings&amp;recordNum=".$this->_contentRecord['contentRecordNum']."\" title=\"Modify Page Settings\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Modify Page Settings</a>\n";
				$this->_toolbar .= " | <a href=\"/modal.php?class=ContentAssets&amp;mode=addcontent&amp;recordNum=".$this->_contentRecord['contentRecordNum']."\" title=\"Add Content\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Add Content</a>\n";
				$this->_toolbar .= " | <a href=\"/modal.php?class=ContentAssets&amp;mode=reordercontent&amp;recordNum=".$this->_contentRecord['contentRecordNum']."\" title=\"Reorder Content\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Reorder Content</a>\n";
				$this->_toolbar .= "</div>\n";

				$this->_toolbar .= "<div class=\"panel\" id=\"sectionoptions\">\n";
				$this->_toolbar .= "<a href=\"/modal.php?class=PageSettings&amp;mode=createpage\" title=\"Create New Page\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Create New Page</a>";
				$this->_toolbar .= " | <a href=\"/modal.php?class=SectionSettings&amp;mode=sectionsettings&amp;sectionID=".$this->_contentRecord['sectionID']."\" title=\"Modify Section Settings\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Modify Section Settings</a>";
				$this->_toolbar .= " | <a href=\"/modal.php?class=Reorder&amp;mode=pages&amp;sectionID=".$this->_contentRecord['sectionID']."\" title=\"Reorder Pages\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Reorder Pages</a>";
				$this->_toolbar .= "</div>\n";

				$this->_toolbar .= "<div class=\"panel\" id=\"siteoptions\">\n";
				$this->_toolbar .= "<a href=\"/modal.php?class=ContentAssets&amp;mode=editglobal\" title=\"Edit Global Content\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Edit Global Content</a>";
				$this->_toolbar .= " | <a href=\"/modal.php?class=ContentAssets&amp;mode=addglobal\" title=\"Add Global Content\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Add Global Content</a>\n";
				$this->_toolbar .= " | <a href=\"/modal.php?class=SectionSettings&amp;mode=createsection\" title=\"Create New Section\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Create New Section</a>";
				$this->_toolbar .= " | <a href=\"/modal.php?class=SiteSettings&amp;mode=sitesettings\" title=\"Modify Site Settings\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Modify Site Settings</a>";
				$this->_toolbar .= " | <a href=\"/modal.php?class=Reorder&amp;mode=sections&amp;siteConfigID=".$this->_contentRecord['siteConfigID']."\" title=\"Reorder Sections\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700, overlayClose: false}); return false;\">Reorder Sections</a>";

				$this->_toolbar .= "</div>\n";
				$this->_toolbar .= "</div>\n";
		}

		private function _showHide() {
				$this->_showToolbarCookie = new Cookie('showToolbar');
				if(!$this->_showToolbarCookie->isCookieSet()) {
						$this->_showToolbarCookie->bakeCookie('showToolbar', 'hide');
				}
				$this->_showToolbar = $this->_showToolbarCookie->getCookieValue();
				echo $this->_showToolbar;
		}

		public function outputJSLink() {
				return "<script type=\"text/javascript\" src=\"/kibu/core/util/JS/fabtabulous.js\"></script>\n";

		}

		public function outputToolbar() {
				return $this->_toolbar;
		}
}
?>
