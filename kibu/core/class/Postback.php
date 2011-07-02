<?php

	class Postback {
		
		private $_error = false;
		private $_errorMsg;
		private $_msg;

		public function __construct($error, $errorMsg, $msg) {
			$this->_error = $error;
			$this->_errorMsg = $errorMsg;
			$this->_msg = $msg;
			$this->getMsg();
		}

		public function getMsg() {
			if($this->_error == true) {
				$this->_msg = "<div class=\"error message\">\n";
				$this->_msg .= "At least one error occurred trying to process form data\n";
				$this->_msg .= "<ul class=\"error\">\n";
				foreach($this->_errorMsg as $errorMsg) {
					$this->_msg .= "<li>".$errorMsg."</li>\n";
				}
				$this->_msg .= "</ul>\n";
				$this->_msg .="</div>\n";
			}
			elseif($this->_error == false) {
				$message = "<div class=\"message\">";
				$message .= $this->_msg;
				$message .= "</div>\n";
			}
			return $this->_msg;
		}
	}
?>