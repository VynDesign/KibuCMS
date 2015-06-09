<?php

	require_once './kibu/core/framework/html/form/Form.php';

	class Upload extends Form {

		private $_uploadDir;
		private $_maxSize;	
		private $_allowedFileTypes = array();
		protected $_error = false;
		protected $_msg;
		protected $_submit;
		private $_fileInputID;
		private $_files;	
		private $_fileName;
		private $_fileTmp;
		private $_fileSize;
		private $_fileType;
		private $_fileError;
		private $_filePermMode = '0755';

		public function getFileName() {
			return $this->_fileName;
		}

		public function getRawMsg() {
			return $this->_msg;	
		}


		public function __construct($uploadDir, $maxSize, $allowedFileTypes = null) {
			parent::__construct();
			$this->_uploadDir = $uploadDir;
			$this->_maxSize = $maxSize;
			$this->_allowedFileTypes = $allowedFileTypes;

			if(count($_POST)) {
				$this->_submit = $_POST;
				$this->_files = $_FILES;			
				$this->_fileInputID = $this->_submit['fileInputID'];
				$this->_fileError = $this->_files[$this->_fileInputID]['error'];
				$this->_fileTmp = $this->_files[$this->_fileInputID]['tmp_name'];			

				if(!$this->_fileErr() && !$this->_fileEmpty()) {
					$this->_fileName = str_replace(" ", "_", $this->_files[$this->_fileInputID]['name']);
					$this->_fileSize = ($this->_files[$this->_fileInputID]['size'] / 1024);
					$this->_fileType = $this->_files[$this->_fileInputID]['type'];

					if($this->_dirExists() && $this->_allowedFileType() && $this->_fileSize() && $this->_overwriteFile()) {
						$this->_moveFile();
					}
				}
			}
		}

		private function _fileErr() {
			if ($this->_fileError > 0) {		
				switch($this->_fileError)
				{
					case '1':
						$this->_error = true;
						$this->_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2':
						$this->_error = true;
						$this->_msg = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3':
						$this->_error = true;
						$this->_msg = 'The uploaded file was only partially transferred';
						break;
					case '4':
						$this->_error = true;
						$this->_msg = 'No file was uploaded';
						break;

					case '6':
						$this->_error = true;
						$this->_msg = 'Missing a temporary folder';
						break;
					case '7':
						$this->_error = true;
						$this->_msg = 'Failed to write file to disk';
						break;
					case '8':
						$this->_error = true;
						$this->_msg = 'File upload stopped by extension';
						break;
					case '999':
					default:
						$this->_error = true;
						$this->_msg = 'An undetermined error ocurred';
				}

				$this->_msg = "There was an issue sending the file to the server: " . $this->_msg . ".";
			}	
			return $this->_error;
		}

		private function _fileEmpty() {
			if(empty($this->_fileTmp) || strtolower($this->_fileTmp) == 'none' || filesize($this->_fileTmp) == 0 || $this->_files[$this->_fileInputID]['size'] == 0) {
				$this->_error = true;
				$this->_msg = "There was no file attached or the file had no contents.";
			}
			return $this->_error;
		}

		private function _dirExists() {
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$this->_uploadDir)) {
				if(mkdir($_SERVER['DOCUMENT_ROOT'].$this->_uploadDir, $this->_filePermMode)) {
					return true;
				}
				$this->_error = true;
				$this->_msg = "There was a problem creating the destination folder " . $this->_uploadDir.".";
				return false;
			}
			return true;
		}

		private function _allowedFileType() {
			if(isset($this->_allowedFileTypes)) {
				$allowedtypes = '';
				foreach($this->_allowedFileTypes as $type) {
					$allowedtypes .= $type . ", ";
					if($type == $this->_fileType) {
						return true;
					}	
				}
				$this->_error = true;
				$this->_msg = "The file type " . $this->_fileType . " is not recognized or is not accepted in the location specified. File type(s) accepted: " . substr($allowedtypes, 0, -2) . ".";
				return false;		
			}
			return true;
		}

		private function _fileSize() {
			if($this->_fileSize > $this->_maxSize) {
				$this->_error = true;
				$this->_msg = "The size of the uploaded file (" . $this->_fileSize . ") exceeds the limit of " . $this->_maxSize. ".";
				return false;
			}
			return true;
		}

		private function _overwriteFile() {
			if ((!isset($this->_submit['overwriteFile']) && (file_exists(".".$this->_uploadDir.$this->_fileName)))) {
				$this->_error = true;
				$this->_msg = "A file by the name \"".$this->_fileName."\" already exists at the destination folder, and you have not opted to overwrite it. Please select a file of another name or check the 'Overwrite file if it already exists?' option in order to proceed.";
				return false;
			}	
			return true;
		}

		private function _moveFile() {
			$imageDir = substr($this->_uploadDir, 1);
			if(move_uploaded_file($this->_fileTmp, $_SERVER['DOCUMENT_ROOT'].$this->_uploadDir.$this->_fileName)) {
				$this->_msg = "The file \"<a href=\"" . $this->_uploadDir.$this->_fileName . "\">" . $this->_fileName . "</a>\" was uploaded successfully.";
				return true;
			}
			else {
				$this->_error = true;
				$this->_msg = "There was a problem moving the temporary file to its permanent location.";
			}
		}	
	}

?>
