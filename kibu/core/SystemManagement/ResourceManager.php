<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceManager
 *
 * @author vyn
 */

	require_once './kibu/core/SystemManagement/SystemManagement.php';
	require_once './kibu/core/framework/file/Upload.php';

	class ResourceManager extends SystemManagement {

		protected $_formTpl;
		protected $_formData;
		protected $_startDir = "./kibu/site_resources";
		protected $_startFolder;
		protected $_startFolderDir;
		protected $_startFolderParent;
		protected $_currentDir;
		protected $_currentFolder;
		protected $_parentDir;
		protected $_parentFolder;
		protected $_parentFolderParent;
		protected $_currentDirPath;
		protected $_dirStructure = array();

		public function GetFileList() {
			return $this->_dirStructure;
		}		
		
		
		public function __construct($args = null) {
			parent::__construct();
			$this->_permAbility = "Manage Resources";			
			$this->_nextStep = 'close';
			if($args != null) {
				if(isset($args['currentDir'])) {
					$this->_currentDir = $args['currentDir'];
				}
				if(isset($args['mode'])) {
					$this->_mode = $args['mode'];
					$this->_formData['mode'] = $this->_mode;
				}
			}
			
			$this->_setDirs();
			
			if(isset($_POST['createFolder'])) {
				$this->_createFolder();
			}
			
			if(isset($_POST['upload'])) {
				$this->_uploadFile();
			}
			
			$this->_setCurDirContents();
			$this->_setFormTpl();
			parent::_setFormBody();
		}

		protected function _setFormTpl() {
			$this->_formTpl = "resource_manager_browse.tpl.php";
		}
		
		private function _setCurDirContents() {
			$this->_dirStructure = $this->_setDirStructure($this->_currentDirPath, false);
			$this->_formData['folders'] = null;
			$this->_formData['files'] = null;
			foreach($this->_dirStructure as $key => $value) {
				if(is_dir($key)) {
					$folder = array_pop(explode("/", $key));
					$this->_formData['folders'][$this->_currentDir."/".$folder] = $folder;
				}
				else {
					$this->_formData['files'][substr($key, 1)] = $value;
				}
			}
		}
		
		private function _setDirs() {			
			$this->_startFolderDir = dirname($this->_startDir);
			
			$this->_startFolder = array_pop(explode("/", $this->_startDir));
						
			$this->_startFolderParent = array_pop(explode("/",$this->_startFolderDir));
						
			$this->_currentFolder = substr($this->_currentDir, 1);
			
			if($this->_startFolder == $this->_currentFolder) {
				$this->_currentDir = null;
			}
			$this->_currentDirPath = $this->_startDir . $this->_currentDir;
						
			$this->_parentDir = dirname($this->_currentDirPath);
									
			$this->_parentFolder = str_replace($this->_startDir, "", $this->_parentDir);
						
			$this->_formData['currentDir'] = $this->_currentDir;			
			$this->_formData['parentDir'] = $this->_parentFolder;	
			$this->_formData['showUpOneLevel'] = false;			
			if("./".$this->_startFolderParent != $this->_parentFolder) {
				$this->_formData['showUpOneLevel'] = true;
			}
		}

		private function _setDirStructure($dir, $recursive) {
			$array_items = array();
			$handle = opendir($dir);
			if($handle) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						$fullFile = preg_replace("/\/\//si", "/", $dir . "/" . $file);	
						$array_items[$fullFile] = $file;				
						if (is_dir($fullFile)) {
							if($recursive) {
								$array_items[$fullFile] = $this->_setDirStructure($fullFile, $recursive);
							}
						}
					}
				}
				closedir($handle);				
			}
			return $array_items;
		}
		
		private function _createFolder() {
			$rmNewFolder = $_POST['rmNewFolder'];
			if(!file_exists($this->_currentDirPath."/".$rmNewFolder)) {
				if(mkdir($this->_currentDirPath."/".$rmNewFolder)) {
					$this->_msg = "New folder '" . $rmNewFolder . "' successfully created in '" . $this->_currentFolder . "'";
					return true;
				}
				else {
					$this->_error = true;
					$this->_msg = "There was a problem creating new folder '" . $rmNewFolder . "' in '" . $this->_currentFolder . "'";
				}
			}
			else {
				$this->_error = true;
				$this->_msg = "There is already a folder named '" . $rmNewFolder . "' in '" . $this->_currentFolder . "'";
			}
		}
		
		private function _uploadFile() { 
			$this->_upload = new Upload(substr($this->_currentDirPath."/", 1), "1000");
			if($this->_upload->getError()) {
				$this->_error = $this->_upload->getError();
				$this->_msg = $this->_upload->getRawMsg();
				return false;
			}
			else {
				$this->_msg = "The file '" . $this->_upload->getFileName() . "' uploaded successfully to '" . $this->_currentFolder . "'";
				return true;
			}			
		}		
	}
	
	
	
	/// special extender class to hook standard ResourceManager into CKEditor file browsing mechanics
	
	class ResourceManager_CKE extends ResourceManager {
		
		private $_ckEditorCallback;
		private $_ckEditorInstance;
		private $_ckEditorLangCode;
		
		public function __construct($args = null) {			
			if(isset($args['CKEditorFuncNum'])) {
				$this->_ckEditorCallback = $args['CKEditorFuncNum'];
				$this->_formData['callback'] = $this->_ckEditorCallback;
			}
			if(isset($args['CKEditor'])) {
				$this->_ckEditorInstance = $args['CKEditor'];
				$this->_formData['ckEditorInstance'] = $this->_ckEditorInstance;
			}
			if(isset($args['langCode'])) {	
				$this->_ckEditorLangCode = $args['langCode'];
				$this->_formData['langCode'] = $this->_ckEditorLangCode;
			}
			$this->_formData['CKEargs'] = "&amp;CKEditor=" . $this->_ckEditorInstance . "&amp;CKEditorFuncNum=" . $this->_ckEditorCallback . "&amp;langCode=" . $this->_ckEditorLangCode;
		
			//$this->_setFormData();
			parent::__construct($args);
		}
		
		
		protected function _setFormTpl() {
			$this->_formTpl = "resource_manager_CKE.tpl.php";
		}
		
	}

?>
