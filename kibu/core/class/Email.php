<?php

	require_once './kibu/core/class/Html2Text.php';

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
		protected $_error;

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

		// check for email address validity
		public function validateEmailAddress() {			if(!ereg("^[^@]{1,64}@[^@]{1,255}$", $this->_from)) { // check that there's one '@' symbol, and that the lengths are right				return false; // invalid email: wrong number of characters in one section, or wrong number of @ symbols.			}			// Split it into sections to make life easier			$email_array = explode("@", $this->_from); // disassemble email address at the '@' symbol, we get two parts in an array - one part before the '@', one after			$local_array = explode(".", $email_array[0]); // disassemble the first part of the array at any '.'			for($i = 0; $i < sizeof($local_array); $i++) { // loop through $local_array for illegal characters				if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) { // if illegal characters found					return false; // invalid email: illegal characters in local part				}			}			if(!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name				$domain_array = explode(".", $email_array[1]); // explode domain (second part of email address) at any '.'				if(sizeof($domain_array) < 2) { // if parts are less than 2 (should be at least 'domain.tld')					return false; // invalid email: Not enough parts to domain				}				for($i = 0; $i < sizeof($domain_array); $i++) { // loop through $domain_array for illegal characters					if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) { // if illegal characters found						return false; // invalid email: illegal characters in domain					}				}			}			return true; // if it passes all inspections, this is a valid email		}
		//

		protected function scrubEmail() {
			if((preg_match("/http/i", $this->_from)) || (preg_match("/http/i", $this->_subject)) || (preg_match("/http/i", $this->_emailBody))) {
				$this->_msg = "Malicious code content detected. Your IP Number of <strong>$IPAddr</strong> has been logged.";
				$this->_error = 'y';
			}
			elseif($this->_to == Null | $this->_from == Null | $this->_subject == Null | $this->_htmlBody == Null) {
				$this->_msg = "Your message could not be delivered because you neglected to fill out one or more required information fields. Please review the information below to ensure that all fields are filled out."; 
				$this->_error = 'y';
			}
			elseif(!$this->validateEmailAddress($this->_from)) {
				$this->_msg = "The email address supplied appears to be invalid. Please review the information below to ensure that it is correct."; 
				$this->_error = 'y';
			}
			elseif(!$this->validateEmailAddress($this->_to)) {
				$this->_msg = "The recpient's email address appears to be invalid. Please review the information below to ensure that it is correct."; 
				$this->_error = 'y';
			}
			else {
				return true;
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
			if($this->_textOnly == 'n') {				echo "MIME-Version: 1.0\n";
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
				echo "This is a multi-part message in MIME format.\n\n";				echo "--".$this->_mime_boundary."\n";
				echo "Content-Type: text/plain; charset=\"iso-8859-1\"\n";				echo "Content-Transfer-Encoding: 7bit\n\n";
				echo "".$this->_textBody."\r\n";
				echo "--".$this->_mime_boundary."\n";
				echo "Content-Type: text/html; charset=\"iso-8859-1\"\n";				echo "Content-Transfer-Encoding: 7bit\n\n";
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