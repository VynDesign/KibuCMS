<?php

/**
 * Description of PageSettings
 *
 * @author vyn
 */

require_once("./kibu/core/class/Template.php");
require_once("./kibu/core/class/Utility.php");

class PageSettings {

		protected $_submit;
		protected $_pageLayouts;
		protected $_layoutAssets;
		private $_form;
		private $_mode;
		private $_formData = array();
		private $_contentRecordNum;
		private $_nextStep = null;
		private $_pageTitle;
		private $_msg;
		private $_auth;
		private $_db;
		private $_url;
		private $_curDate;
		private $_curTime;
		private $_siteTemplates;
		private $_templateMasterDefault;

		public function  __construct() {
				$this->_setProps();
				$this->_pageTitle();
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

		private function _setProps() {
				$this->_curDate = date('Y-m-d');
				$this->_curTime = date('H:i:s');

				global $auth;
				$this->_auth = $auth;

				$this->_checkAuth();

				global $db;
				$this->_db = $db;

				global $url;
				$this->_url = $url;

				$this->_templateMasterDefault = $this->_url->siteConfig['templateMasterDefault'];

				$this->_getSiteTemplates();
				
				if(isset($_GET['mode'])) {
						$this->_mode = $_GET['mode'];
				}
				if(isset($_POST['submit'])) {
						$this->_submit = $_POST;
						if($this->_submit['nextStep'] == 'pagelayouts') {
								if($this->_checkReq()) {
										if($this->_createPage()) {
												$this->_pageLayouts();
												$this->_nextStep = 'finish';
										}
								}
								else {
										$this->_nextStep = 'pagelayouts';
										$this->_pageSettings();
								}
						}
						elseif($this->_submit['nextStep'] == 'finish') {
								if($this->_mode == 'createpage') {
										$this->publishLayoutAssets();
								}
								elseif($this->_mode == 'pagesettings'){
										$this->_savePageSettings();
								}
						}
				}
				elseif($this->_mode == 'createpage') {
						$this->_pageSettings();
						$this->_nextStep = "pagelayouts";
				}
				elseif($this->_mode == 'pagesettings') {
						$this->_contentRecordNum = $_GET['recordNum'];
						$this->_nextStep = 'finish';
						$this->_pageSettings();
				}
		}

		private function _checkAuth() {
				if($this->_auth->getUserLevel() < 100) {
						header("Location:/Home/Login.html");
				}
		}
		
		private function _pageSettings() {
				$settingsTpl = new Template("./kibu/templates/");
				if(($this->_mode == 'createpage') && (!isset($_POST['createpage']))) {
						$settingsTpl->set('contentTitle', Null);
						$settingsTpl->set('titleClean', Null);
						$settingsTpl->set('metaKeywds', Null);
						$settingsTpl->set('metaDesc', Null);
						$settingsTpl->set('isVisible', Null);
						$settingsTpl->set('sectionID', Null);
						$settingsTpl->set('visitorAuthLevel', Null);
						$settingsTpl->set('editorAuthLevel', Null);
						$settingsTpl->set('message', Null);
						$settingsTpl->set('siteTemplateID', $this->_templateMasterDefault);
						$settingsTpl->set('contentRecordNum', Utility::guidGen());
				}
				elseif($this->_mode == 'pagesettings') {
						$query = "SELECT * FROM contentRecords WHERE contentRecordNum = '".$this->_contentRecordNum."'";
						$this->_db->setQuery($query);
						$this->_formData = $this->_db->getAssoc();
						$settingsTpl->set_vars($this->_formData, true);
				}
				else {
						$settingsTpl->set_vars($this->_submit, true);
				}
				$settingsTpl->set('userLevels', Utility::getUserLevels());
				$settingsTpl->set('sections', Utility::getSections());
				$settingsTpl->set('siteTemplates', $this->_siteTemplates);
				$this->_form = $settingsTpl->fetch("page_settings.tpl.php");
		}

		private function _pageLayouts() {
				$this->setPageLayouts();
				$layoutsTpl = new Template("./kibu/templates/");
				$layoutsTpl->set("layoutOptions", $this->outputLayoutOptions());
				$layoutsTpl->set("javaScript", $this->outputJScript());
				$layoutsTpl->set('contentRecordNum', $this->_submit['contentRecordNum']);
				$this->_form = $layoutsTpl->fetch("page_layouts.tpl.php");
		}

		private function _pageTitle() {
				if($this->_mode == 'createpage') {
						$this->_pageTitle = 'Create Page';
				}
		}

		public function createMode($body, $auth) {
			// if we've been looped back to the 'create new page' form, repopulate the form from the submitted data using set_vars() method of the $body object in case of error
			if(isset($_POST['createpage'])) {
				$this->createPage($auth->getUserID());
				$message = $this->getMessage();
				$settingsTpl->set_vars($this->getSubmit(), true);
				$settingsTpl->set('message', $message);
			}
			// otherwise set all to 'null' to avoid warning messages
			else {
				$settingsTpl->set('contentTitle', Null);
				$settingsTpl->set('titleClean', Null);
				$settingsTpl->set('metaKeywds', Null);
				$settingsTpl->set('metaDesc', Null);
				$settingsTpl->set('isVisible', Null);
				$settingsTpl->set('sectionID', Null);
				$settingsTpl->set('siteTemplateID', $this->_templateMasterDefault);
				$settingsTpl->set('visitorAuthLevel', Null);
				$settingsTpl->set('editorAuthLevel', Null);
				$settingsTpl->set('message', Null);
			}
			$settingsTpl->set('curPage', $content->getCurPage());
			$settingsTpl->set('sections', $content->getSections());
			$settingsTpl->set('userLevels', $content->getUserLevels());
		}

		private function _checkReq() {
				if($this->_submit['contentTitle'] == null | $this->_submit['isVisible'] == null | $this->_submit['sectionID'] == null | $this->_submit['visitorAuthLevel'] == null | $this->_submit['editorAuthLevel'] == null){
						$this->_msg .= "The following required fields were not filled out:\n<ul class=\"message\">";
						if($this->_submit['contentTitle'] == null) {
								$this->_msg .= "<li>Content Title</li>\n";
						}
						if($this->_submit['isVisible'] == null) {
								$this->_msg .= "<li>Visibility</li>\n";
						}
						if($this->_submit['sectionID'] == null) {
								$this->_msg .= "<li>Site Section</li>\n";
						}
						if($this->_submit['visitorAuthLevel'] == null) {
								$this->_msg .= "<li>Viewer Authorization</li>\n";
						}
						if($this->_submit['editorAuthLevel'] == null){
								$this->_msg .= "<li>Editor Authorization</li>\n";
						}
						$this->_msg .="</ul>\n";
						return false;
				}
				else {
						return true;
				}
		}

		private function _createPage() {
				$curDate = $this->_curDate;
				$curTime = $this->_curTime;
				if($this->_submit['titleClean'] == Null) {
						$titleClean = Utility::stripChars($this->_submit['contentTitle']);
				}
				else {
						$titleClean = Utility::stripChars($this->_submit['titleClean']);
				}
				if($this->checkTitle($titleClean)) {
						$orderNum = $this->_checkSectionPages();
						$sql = "INSERT INTO contentRecords
								SET
										siteConfigID='".$this->_url->siteConfig['siteConfigID']."',
										contentTitle='".$this->_submit['contentTitle']."',
										titleClean='".$titleClean."',
										ownerID='".$this->_auth->getUserID()."',
										authorID='".$this->_auth->getUserID()."',
										submitDate='".$curDate."',
										submitTime='".$curTime."',
										metaKeywds='".$this->_submit['metaKeywds']."',
										metaDesc='".$this->_submit['metaDesc']."',
										isVisible='".$this->_submit['isVisible']."',
										sectionID='".$this->_submit['sectionID']."',
										visitorAuthLevel='".$this->_submit['visitorAuthLevel']."',
										editorAuthLevel='".$this->_submit['editorAuthLevel']."',
										contentRecordNum='".$this->_submit['contentRecordNum']."',
										orderNum='".$orderNum."',
										siteTemplateID='".$this->_submit['siteTemplateID']."'";
						if(mysql_query($sql)) {
								$this->_msg = "Your new page is registered. Choose a page layout below to finish creating the page.";
								return true;
						}
						else {
								$this->_msg = "There was a problem creating page: ".mysql_error()."";
								return false;
						}
				}
		}

		private function _checkSectionPages() {
				$query = "SELECT COUNT(contentRecordNum) as pageCount FROM contentRecords WHERE sectionID = '".$this->_submit['sectionID']."'";
				$this->_db->setQuery($query);
				$result = $this->_db->getAssoc();

				if($result['pageCount'] > 0) {
						$pageOrderNum = $result['pageCount'] + 1;
				}
				else {
						$pageOrderNum = 1;
						$setDefaultLanding = "UPDATE navigationSections
								SET landingPageRecordNum = '".$this->_submit['contentRecordNum']."'
										WHERE sectionID = '".$this->_submit['sectionID']."'";
						$this->_db->setQuery($setDefaultLanding);
				}
				return $pageOrderNum;
		}

		private function checkTitle($titleClean) {
				$sectionID = $this->_submit['sectionID'];
				$query = "SELECT contentID FROM contentRecords WHERE titleClean = '$titleClean' AND sectionID = '$sectionID'";
				$query = $this->_db->setQuery($query);
				$numrows = $this->_db->getNumRows($query);
				if($numrows == '0') {
						return true;
				}
				elseif($numrows > '0') {
						$this->_msg .= "There is already a page by the name \"".$titleClean."\" in that section. Please review the form below and make any necessary adjustments.";
						return false;
				}
		}

		private function _savePageSettings() {
				$contentRecordNum = $this->_submit['contentRecordNum'];
				$contentTitle = htmlentities($this->_submit['contentTitle']);
				if($this->_submit['titleClean'] == Null) {
						$titleClean = Utility::stripChars($this->_submit['contentTitle']);
				}
				else {
						$titleClean = Utility::stripChars($this->_submit['titleClean']);
				}
				$metaKeywds = $this->_submit['metaKeywds'];
		    $metaDesc = $this->_submit['metaDesc'];
		    $isVisible = $this->_submit['isVisible'];
		    $sectionID = $this->_submit['sectionID'];
		    $visitorAuthLevel = $this->_submit['visitorAuthLevel'];
		    $editorAuthLevel = $this->_submit['editorAuthLevel'];
				$siteTemplateID = $this->_submit['siteTemplateID'];

				$sql = "UPDATE contentRecords
						SET
								contentTitle='$contentTitle',
								titleClean='$titleClean',
								metaKeywds='$metaKeywds',
								metaDesc='$metaDesc',
								isVisible='$isVisible',
								sectionID='$sectionID',
								visitorAuthLevel='$visitorAuthLevel',
								editorAuthLevel='$editorAuthLevel',
								siteTemplateID='$siteTemplateID'
						WHERE
								contentRecordNum='$contentRecordNum'";
				if(mysql_query($sql)) {
						$this->_msg = "Page settings saved successfully.";
				}
				else {
						$this->_msg = "There was a problem saving page settings: ".mysql_error()."";
				}
		}

		private function stripChars($string) {
				$string = str_replace(' ', '', $string);
				$string = preg_replace('/[^a-zA-Z0-9 -]/s', '', $string);
				return $string;
		}

		private function setPageLayouts() {
			$query = "SELECT DISTINCT publishingLayouts.*
					FROM publishingLayouts, publishingLayoutsAssets, siteTemplateContentZones
						WHERE publishingLayoutsAssets.contentZoneID = siteTemplateContentZones.contentZoneID
								AND siteTemplateContentZones.templateID = '".$this->_submit['siteTemplateID']."'
								AND publishingLayouts.layoutID = publishingLayoutsAssets.layoutID
						ORDER BY publishingLayouts.layoutName ASC";
			$query = $this->_db->setQuery($query);
			while($assoc = $this->_db->getAssoc()) {
				$layouts[] = $assoc;
			}
			$this->_pageLayouts = $layouts;
		}


		public function outputLayoutOptions(){
			ob_start();
			foreach($this->_pageLayouts as $pageLayout) {
				echo "<option value=\"".$pageLayout['layoutID']."\">".$pageLayout['layoutName']."</option>\n";
			}
			$layoutOptions = ob_get_contents();
			ob_end_clean();
			return $layoutOptions;
		}


		public function outputJScript() {
			ob_start();
			echo "<script type=\"text/javascript\">\n";
			echo "\t\t\t function replaceText(ID,descriptionID) {\n";
			echo "\t\t\t\t var descriptions = new Array();\n";
			foreach($this->_pageLayouts as $pageLayout) {
				echo "\t\t\t\t descriptions[".$pageLayout['layoutID']."] = \"<strong>".$pageLayout['layoutName']."</strong><br />".$pageLayout['layoutDesc']."\";\n";
			}
			echo "\t\t\t\t if(descriptionID > '0') {\n";
			echo "\t\t\t\t\t document.getElementById(ID).innerHTML = descriptions[descriptionID];\n";
			echo "\t\t\t\t }\n";
			echo "\t\t\t\t else {\n";
			echo "\t\t\t\t\t document.getElementById(ID).innerHTML = descriptions[0];\n";
			echo "\t\t\t\t }\n";
			echo "\t\t\t}\n";
			echo "\t\t</script>\n";
			$script = ob_get_contents();
			ob_end_clean();
			return $script;
		}

		public function setPageLayoutAssets() {
				$query = "SELECT *
							FROM publishingLayoutsAssets, contentAssetTypes
							WHERE publishingLayoutsAssets.layoutID = '".$this->_submit['pageLayout']."'
								AND publishingLayoutsAssets.assetTypeID = contentAssetTypes.assetTypeID
							ORDER BY publishingLayoutsAssets.assetOrderNum";
				$query = $this->_db->setQuery($query);
				while($assetsArray = $this->_db->getAssoc()){
					$assets[$assetsArray['assetTypeName']] = $assetsArray;
				}
			$this->_layoutAssets = $assets;
		}

		private function publishLayoutAssets() {
			$this->setPageLayoutAssets();
			foreach($this->_layoutAssets as $asset) {
				$this->_addAsset($asset);
			}
			$sql = "SELECT
						contentRecords.titleClean,
						navigationSections.sectionName
					FROM
						contentRecords, navigationSections
					WHERE
						navigationSections.sectionID = contentRecords.sectionID
						AND contentRecords.contentRecordNum = '".$this->_submit['contentRecordNum']."'";
						$query = $this->_db->setQuery($sql);
						$result = $this->_db->getAssoc();
						echo "Your new page is finished. You may now <a href=\"/".$result['sectionName']."/".$result['titleClean'].".edit\">edit it</a> or <a href=\"/modal.php?mode=".$this->_mode."\" title=\"Create Page\" onclick=\"Modalbox.show(this.href, {title: this.title, width: 700}); return false;\">create another page</a>.";
		}

		private function _addAsset($array) {
				$assetCreateDate = $this->_curDate;
				$assetCreateTime = $this->_curTime;
				$query = "INSERT INTO contentRecordAssets SET
						assetTypeID = '".$array['assetTypeID']."',
						assetName = '".$array['assetName']."',
						contentRecordNum = '".$this->_submit['contentRecordNum']."',
						assetCreateDate = '".$assetCreateDate."',
						assetCreateTime = '".$assetCreateTime."',
						assetOrderNum = '".$array['assetOrderNum']."',
						contentZoneID = '".$array['contentZoneID']."'";

				$this->_db->setQuery($query);

				require_once './kibu/core/class/ContentAssets.php';
				$contentAssets = new ContentAssets();

				$query = "SELECT assetID
						FROM contentRecordAssets
								WHERE assetCreateDate = '".$assetCreateDate."'
										AND assetCreateTime = '".$assetCreateTime."'
										AND assetTypeID = '".$array['assetTypeID']."'";

				$this->_db->setQuery($query);
				$result = $this->_db->getAssoc();
				$contentAssets->_addAssetParams($result['assetID'], $array['assetTypeID']);
		}

		private function _getSiteTemplates() {
				global $db;
				$query = "SELECT siteTemplates.templateID, siteTemplates.templateName FROM siteTemplates, siteTemplateTypes
						WHERE
								siteTemplateTypes.templateTypeName = 'Site'
						AND
								siteTemplates.templateTypeID = siteTemplateTypes.templateTypeID
						ORDER BY
								templateID ASC";

				$db->setQuery($query);
				while($result = $db->getAssoc()){
						$this->_siteTemplates[$result['templateID']] = $result['templateName'];
				}
		}
}

?>
