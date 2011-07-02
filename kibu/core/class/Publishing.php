<?php

	/**
	 * A class designed to facilitate creating and editing pages and content
	 *
	 *
	 * @package Kibu
	 * @subpackage Kibu Publishing Module
	 * @author Vyn Raskopf
	 * @copyright Kibu 2010
	 * @version 1.0.0
	 */
	
	require_once './kibu/core/class/Content.php';	
	require_once './kibu/core/class/Module.php';
	require_once './kibu/core/class/Form.php';
	
	class Publishing extends Content {

		protected $_submit = array();
		protected $_files = array();
		protected $_error = false;
		protected $_msg = null;
		protected $_authLevel;
		protected $_curDate;
		protected $_curTime;
		protected $_contentRecordNum;
		protected $_contentAssets;
		protected $_moduleNum;
		protected $_publishingPermLevels;
		public $_baseURL;
		protected $_contentBody;


		public function __construct($siteConfig, $auth) {
			parent::__construct($siteConfig);
			global $url;
			$this->_contentRecordNum = parent::getContentRecordNum();
			$this->_contentAssets = parent::getContentAssets();
			$this->_submit = $_POST;
			$this->_files = $_FILES;
			$this->_curDate = date('Y-m-d');
			$this->_curTime = date('H:i:s');
			$this->_baseURL = $url->_baseURL;
			$this->_section = $url->_section;
			$this->_filename = $url->_filename;
			$this->_mode = $url->_mode;

			$this->getPermissions($auth);
			
			if(!empty($url)) {
				if(($url->_mode == 'edit') && (in_array($url->_mode, $this->_publishingPermLevels))) {
						global $tpl;
						$this->body = $this->editMode($tpl, $url, $auth);
				}
			}
			
			if(isset($_POST['publish'])) {
				$this->publishContent($auth->getUserID());
			}
		}

		/*
		 *
		 */
		private function getPermissions($auth) {
				//global $auth;
				global $db;
				$query = "SELECT publishingPermLevels.permLevelName, publishingPermLevels.permLevelID
						FROM publishingPermLevels, publishingPerms
						WHERE publishingPermLevels.permLevelID = publishingPerms.publishingPermLevelID
								AND publishingPerms.userLevelNum = ".$auth->getUserLevel()."";
				$db->setQuery($query);
				while($assoc = $db->getAssoc()) {
						$this->_publishingPermLevels[] = $assoc['permLevelName'];
				}
		}
		
	
		//
		private function publishContent($userID) {
			if(!empty($this->_submit['contentAsset'])) {
				$contentAsset = $this->_submit['contentAsset'];
			}
			if(!empty($_FILES['contentAsset'])) {
				$files = $_FILES['contentAsset'];
			}
			if(isset($this->_submit['publish'])) {	
				$contentID = $this->_submit['contentID'];
			    $this->_contentRecordNum = $this->_submit['contentRecordNum'];
				if(!empty($this->_submit['contentAsset'])) {
					foreach($this->_submit['contentAsset'] as $key => $value) {
						if((!empty($_FILES['file']['name'][$key])) && $_FILES['file']['name'][$key] != '') {
							$value = $this->fileHandler($key);
						}
						else {
							$value = $this->_submit['contentAsset'][$key];
						}
						if(isset($_POST['isVisible'][$key])) {
							$assetStatus = $this->_assetStatus($key);
							if($assetStatus == 'n') {
								$isVisible = 'y';
							}
							elseif($assetStatus == 'y') {
								$isVisible = 'n';
							}
						}
						else {
								$isVisible = $this->_assetStatus($key);
						}
						$assetUpdate = "UPDATE contentRecordAssets
								SET
										assetBody = '$value',
										assetEditDate = '".$this->_curDate."',
										assetEditTime = '".$this->_curTime."',
										isVisible = '$isVisible'
								WHERE
										assetID = '$key'
								AND
										contentRecordNum = '".$this->_contentRecordNum."'";
						if(!mysql_query($assetUpdate)){
							$this->_error = true;
							$this->_msg .= "There was a problem updating content block '$key'<br /><br />";
						}
						else {
							$this->_error = false;
						}		
					}
				}			
			}
			header("Location: http://".$this->_baseURL."/".$this->_section."/".$this->_filename.".html");
		}
		//

		protected function addContent($auth) {
			global $db;	
			$submit = $this->_submit;
			$this->reorderAssets($submit['assetOrderNum'], $submit['contentRecordNum']);
			$assetOrderNum = $submit['assetOrderNum'];
			$this->_contentRecordNum = $submit['contentRecordNum'];
			$this->addAsset($submit, $assetOrderNum +1, 'n');
		}


		private function _assetStatus($assetID) {
			global $db;
			$query = "SELECT isVisible FROM contentRecordAssets WHERE assetID = '".$assetID."'";
			$query = $db->setQuery($query);
			$result = $db->getAssoc($query);
			$status = $result['isVisible'];
			return $status;
		}

		//
		protected function addAsset($array, $assetOrderNum, $isVisible, $contentRecordNum = null) {
			if($contentRecordNum == NULL) {
				$contentRecordNum = $this->_contentRecordNum;
			}
			$addAsset = "INSERT INTO contentRecordAssets SET
					assetTypeID = '".$array['assetTypeID']."',
					assetName = '".$array['assetName']."',
					contentRecordNum = '".$contentRecordNum."',
					assetCreateDate = '".$this->_curDate."',
					assetCreateTime = '".$this->_curTime."',
					assetOrderNum = '".$assetOrderNum."',
					isVisible = '".$isVisible."'";
			if(mysql_query($addAsset)) {
				$this->_msg = 'Asset published successfully.';
				if($isVisible == 'n') {
					$this->_msg .= ' If you have just added this asset, you may need to set any applicable parameters and approve it before it will display to visitors. You may do so on the <a href="'.$this->_page.'">content edit mode</a> of this page.';
				}
			}			
		}
		//

		protected function updateAsset($contentRecordNum, $assetBody, $isVisible) {
			$updateAsset = "UPDATE contentRecordAssets SET
					assetBody = '".$assetBody."',
					assetEditDate = '".$this->_curDate."',
					assetEditTime = '".$this->_curTime."',
					isVisible = '".$isVisible."' 
					WHERE contentRecordNum = '".$contentRecordNum."'";
		}
/*	
		protected function publishAssetParam($assetParamID, $assetParamValue) {
			$paramUpdate = "INSERT INTO contentRecordAssetParams SET
					assetID = '".$assetID."',
					assetParamFieldID = '".$assetParamFieldID."',
					assetParamName = '".$assetParamName."',
					assetParamValue = '".$assetParamValue."'";
		}*/

		protected function approveAsset($isVisible, $assetID, $form) {
			if($isVisible == 'n') {
				$label = 'This content block is hidden. Check this box to show this content block.';
			}
			if($isVisible == 'y') {
				$label = 'This content block is visible. Check this box to hide this content block.';
			}
			echo $form->field('checkbox', "isVisible[$assetID]", $label, Null, Null);
			
		}
		
/*		protected function updateAssetParam($assetParamID, $assetParamValue) {
			$paramUpdate = "UPDATE contentRecordAssetParams SET
					assetParamName = '".$assetParamName."',
					assetParamValue = '".$assetParamValue."'
					WHERE assetParamID = '$assetKey' AND assetID = '$key'";
		}*/

		//
		private function fileHandler($assetID) {
			$files = $this->_files;
			$namereverse = array_reverse(explode('.', $files['file']['name'][$assetID]));
			$ext = $namereverse[0];
			if($ext == 'jpg' | $ext == 'jpeg' | $ext == 'png' | $ext == 'gif') {
				$dir = 'images';
			}
			elseif($ext == 'flv' | $ext == 'swf') {
				$dir = 'flash';
			}
			elseif($ext == 'wmv' | $ext == 'mov') {
				$dir = 'media';
			}
			elseif($ext == 'doc' | $ext == 'docx' | $ext == 'xls' | $ext == 'xlsx' | $ext == 'pdf') {
				$dir = 'docs';
			}
			else {
				$this->_msg = 'Unrecognized file type';
			}
			$uploadDir = 'site_resources/'.$dir.'/';
			$file = $files['file']['name'][$assetID];
			$tempLocation = $files['file']['tmp_name'][$assetID];
			$uploadLocation = $uploadDir . $file;
			$move = move_uploaded_file($tempLocation, $uploadLocation);
			if($move) {
				return $uploadLocation;
			}
			else {
				$this->_msg = 'Moving file '.$file.' to permanent location failed';
			}
		}

		public function getScriptOutput() {
				$script = "<script type=\"text/javascript\" src=\"/kibu/core/util/ckeditor/ckeditor.js\"></script>\n";
				return $script;
		}

		/*
		 * 
		 */
		public function bodyAssembler($auth, $url, $form) {
				$contentRecord = parent::getContentRecord();
				foreach($this->_contentAssets as $assetZone) {
								$assetBody = '';
								foreach($assetZone as $key) {
										if($key['isVisible'] == 'y') {
												$assetBody .= "<div class=\"contentblock ".$key['assetTypeNameClean']."\">\n";
												if($key['assetModuleID'] != 0) { // if there is a template assigned to this asset
														$bodymodule = new Module();
														$assetBody .= $bodymodule->loadModule($key, $key['params']);
												}
												elseif(($key['assetModuleID'] == 0) && ($key['assetDisplayID'] != 0)) {
														$assetTpl = new Template('./kibu/templates/');
														$assetTpl->set('assetBody', $key['assetBody']);
														$assetBody .= $assetTpl->fetch($key['templateLink'].'.tpl.php');
												}
												else {
														$assetBody .= $key['assetBody'];
												}
												$assetBody .= "</div>\n";
										}
								}
								$assetWrapper = "<div class=\"contentzone\">\n";
								$assetWrapper .= "woot";
								$assetWrapper .= $assetBody;
								$assetWrapper .= "</div>\n";

								$this->_contentBody[$key['assetZoneNum']] = $assetWrapper; // output assetBody
				}
			//	foreach($this->_contentAssets as $key) {
			//			print_r($key);
			//		//$params = $content->setContentAssetParams($key['assetTypeID']);
			//		$modulePath = './kibu/modules/';
			//		if((file_exists('./'.$modulePath.$key['moduleLink'].'/'.$key['moduleLink'].'.tpl.php')) && ($key['assetEditID'] == '0')) {
			//				$templatePath = $modulePath.$key['moduleLink'].'/';
			//				$assetInput = $key['moduleLink'];
			//		}
			//		else {
			//			$templatePath = './kibu/templates/';
			//			$assetInput = $this->getEditInput($key['assetTypeID']);
			//		}
			//		$body = new Template($templatePath); // instantiate new template object $body
			//		$body->set_vars($key, true); // assign array of content asset paramater values to $body template
			//		if(!isset($paramFields)) {
			//			$paramFields = Null;
			//		}
			//		$body->set('paramFields', $paramFields);
			//		ob_start();
			//		$this->approveAsset($key['isVisible'], $key['assetID'], $form);
			//		$approveAsset = ob_get_contents();
			//		ob_end_clean();
			//		$body->set('approveAsset', $approveAsset);
			//		echo $body->fetch($assetInput.'.tpl.php'); // output template file
			//	}
		}


		public function getContentBody() {
				return $this->_contentBody;
		}

		private function getEditInput($assetTypeID) {
			global $db;
			$query = "SELECT publishingEditFields.assetEditInput, publishingAssetEditTypes.assetEditID
						FROM publishingEditFields, publishingAssetEditTypes
						WHERE publishingEditFields.assetEditID = publishingAssetEditTypes.assetEditID
							AND publishingAssetEditTypes.assetTypeID = ".$assetTypeID."";
			$query = $db->setQuery($query);
			$assoc = $db->getAssoc($query);
			return $assoc['assetEditInput'];
		}

		//
		function getAssetFields() {
			$query = mysql_query("SELECT contentAssetTypes.*, contentAssetEditFields.assetEditInput
									FROM contentAssetTypes, contentAssetEditFields
									WHERE contentAssetEditFields.assetEditID = contentAssetTypes.assetEditID
									ORDER BY contentAssetTypes.assetTypeNameClean ASC");
			while($assetsArray = mysql_fetch_assoc($query)){
				$assets[$assetsArray['assetTypeName']][] = $assetsArray;
			}
			return $assets;
		}
		//
		
		
		public function editMode($tpl, $url, $auth) {
				global $kibu;
				global $content;
			if($url->_mode == 'edit') {
				//$kibu->additionalPageHead($this->getScriptOutput());

				ob_start();
        echo "<h3>Edit Mode</h3>";
				$message = $this->getMessage();
				echo $message;
				$form = new Form();
				if(($url->_mode == 'html') && (isset($_POST['pagesetup']))) {
					echo $this->bodyAssembler($auth, $url, $form);
					echo $form->beginFieldset("noborder center clear", Null);
					echo $form->input('submit', 'createpage', 'Create Page');
					echo $form->endFieldset();				
				}
				elseif($url->_mode == 'edit') {
					
		

						if(is_array($this->getContentAssets())) {
							echo $this->bodyAssembler($auth, $url, $form);
						}
						else {
							header("Location: http://".$url->_baseURL."/".$url->_section."/".$url->_filename.".".$url->_mode."?control=addcontent");
						}
					}
					echo $form->field('hidden', 'contentID', Null, Null, parent::getContentRecordValue('contentID'));
					echo $form->field('hidden', 'contentRecordNum', Null, Null, parent::getContentRecordValue('contentRecordNum'));

				$formBody = ob_get_contents();
				ob_end_clean();

				$formBtns = new Template('./kibu/templates/');
				$vars = array(
						'nextStep' => null,
						'submitButtonID' => 'publish',
						'submitButtonName' => 'publish',
						'submitButtonVal' => 'Publish Content',
						'submitBtnExtra' => null,
						'resetButtnID' => 'reset',
						'resetButtonName' => 'reset',
						'resetButtonVal' => 'Cancel Edit',
						'resetBtnExtra' => null
				);
				$formBtns->set_vars($vars, true);
				$formSubmit = $formBtns->fetch('form_submit_2btn.tpl.php');

				$formWrapper = new Template('./kibu/templates/');
				$formWrapper->set('class', 'publishform');
				$formWrapper->set('ID', 'publishform');
				$formWrapper->set('name','publishform');
				$formWrapper->set('method', 'post');
				$formWrapper->set('action', parent::getCurPage());
				$formWrapper->set('msg', null);
				$formWrapper->set('formBody', $formBody);
				$formWrapper->set('formSubmit', $formSubmit);

	
				$body = $formWrapper->fetch('form_wrapper.tpl.php');
				
				return $body;
			}
		}

		public function getMessage() {
			return $this->_msg;
		}

		public function getSubmit() {
			return $this->_submit;
		}
	}
?>