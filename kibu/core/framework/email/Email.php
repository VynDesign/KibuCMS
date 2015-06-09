<?php

	require_once 'Html2Text.php';

	class Email extends html2text {
	
		protected $_textOnly;
		protected $_submit;
		protected $_from;
		protected $_to;
		protected $_subject;
		protected $_IPAddr;
		protected $_emailBody;
		protected $_textBody;
		protected $_htmlBody;
		protected $_mime_boundary;
		protected $_headers;
		protected $_msg;
		protected $_error = false;

		public function __construct($to, $textOnly = 'y') {
			$this->_submit = $_POST;
			$this->_textOnly = $textOnly;
			$this->_submit = $_REQUEST;
			$this->_from = $_REQUEST['from'];
			$this->_to = $to;
			$this->_subject = $_REQUEST['subject'];
			$this->_IPaddr = $_SERVER['REMOTE_ADDR'];
			$this->_htmlBody = $_REQUEST['emailbody'];
			if($this->scrubEmail()) {
				$this->setMimeBoundary();	
				$this->setHeaders();
				if($textOnly == 'n') {
					$this->setTextBody();
					$this->setEmailBody();
				}
				else {
					$this->_emailBody = $_POST['emailbody'];
				}
			}
		}

		protected function scrubEmail() {
			if((preg_match("/http/i", $this->_from)) || (preg_match("/http/i", $this->_subject)) || (preg_match("/http/i", $this->_emailBody))) {
				$this->_msg = "Malicious code content detected. Your IP Number of <strong>$IPAddr</strong> has been logged.";
				$this->_error = true;
			}
			elseif($this->_to == Null || $this->_from == Null || $this->_subject == Null || $this->_htmlBody == Null) {
				$this->_msg = "Your message could not be delivered because you neglected to fill out one or more required information fields. Please review the information below to ensure that all fields are filled out."; 
				$this->_error = true;
			}
			elseif(!Utility::validateEmailAddress($this->_from)) {
				$this->_msg = "The email address supplied appears to be invalid. Please review the information below to ensure that it is correct."; 
				$this->_error = true;
			}
			elseif(!Utility::validateEmailAddress($this->_to)) {
				$this->_msg = "The recpient's email address appears to be invalid. Please review the information below to ensure that it is correct."; 
				$this->_error = true;
			}
			else {
				return !$this->_error;
			}
		}
		//

		private function setMimeBoundary() {
			$uniqueVal = uniqid(time());
			$this->_mime_boundary = "==Multipart_Boundary_x".$uniqueVal."x";
		}

		protected function setHeaders() {
			ob_start();
			echo "From: ".$this->_from."\n";
			if($this->_textOnly == 'n') {
				echo "MIME-Version: 1.0\n";
				echo "Content-Type: multipart/alternative;\n";
				echo "\tboundary=\"".$this->_mime_boundary."\"\n"; 
			}
			$this->_headers = ob_get_contents();
			ob_end_clean();
		}

		protected function setTextBody() {
			parent::html2text($this->_htmlBody);
			$this->_textBody = parent::get_text();
		}

		protected function setEmailBody() {
				ob_start();
				echo "This is a multi-part message in MIME format.\n\n";
				echo "--".$this->_mime_boundary."\n";
				echo "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
				echo "Content-Transfer-Encoding: 7bit\n\n";
				echo "".$this->_textBody."\r\n";
				echo "--".$this->_mime_boundary."\n";
				echo "Content-Type: text/html; charset=\"iso-8859-1\"\n";
				echo "Content-Transfer-Encoding: 7bit\n\n";
				echo "<html><body>\n\n";
				echo "".$this->_htmlBody."\n\n";
				echo "</body></html>\n\n"; 
				echo "--".$this->_mime_boundary."--\n";
				$this->_emailBody = ob_get_contents();
				ob_end_clean();
		}			

		public function sendEmail() {
			$send = mail($this->_to, $this->_subject, $this->_emailBody, $this->_headers); // utilize php mail function
			if($send) { // if email generated and sent successfully
				return true;
			}
			else { // otherwise, not sent successfully
				return false;
			}
		}
		//

		public function getMsg() {
			return $this->_msg;
		}

		public function getError() {
			return $this->_error;
		}
	}
?>