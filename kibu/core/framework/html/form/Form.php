<?php

	require_once './kibu/core/framework/html/HtmlTag.php';
	require_once './kibu/core/framework/html/form/Feedback.php';

	/**
	 * A class designed to generate html forms and cleanse data
	 *
	 *
	 * @package kibu
	 * @author Vyn Raskopf
	 * @copyright kibu 2009
	 * @version 1.0.0
	 */

	class Form extends HtmlTag {

		protected $_class;
		protected $_action;
		protected $_method;
		protected $_required = array();
		protected $_matching = array();
		protected $_submit = array();
		protected $_errorMsg = array();
		protected $_error = false;
		protected $_warning = false;
		protected $_msg;
		protected $_db;
		
		public $feedback;
 
		public function __construct() {
			global $db;
			$this->_db = $db;
			$this->feedback = new Feedback();
			if(count($_POST)){
				$this->_submit = $_POST;	
			}
		}

		public function setRequired($required) {
			$this->_required = $required;
			$this->checkRequired();
		}

		/**
		* Loads form fields that need to match
		*
		* @param array $matching
		* @access public
		* @return void
		*/		
		public function setMatching($matching) {
			$this->_matching = $matching;
			$this->checkMatching();
		}

		/**
		* Loops through each form field in the $_required array to check if it has a value
		* Sets any error messages as appropriate
		* Set up to loop through a two-dimensional array (array with possible arrays as values). Won't go deeper than that, though
		*
		* @access private
		* @return void
		*/
		public function checkRequired() {
			foreach($this->_required as $required) { // for each required field
				foreach($this->_submit as $key => $submit) { // for each posted value
					if($required == $key) { // if this is the required field that matches the posted data
						if(is_array($submit)) { // if the data is another array
							foreach($submit as $submit2) { // for each node in this array
								if($submit2 == '' || $submit2 == null) { // check if the submitted form field is empty or null
									$this->feedback->SetType(FeedbackTypes::Error); // if so, error is 'yes'
									$this->feedback->SetMsg('Required field "'.$required.'" not filled out.'); // add a node to the 'errorMsg' array
									$this->_error = true;
								}
							}
						}
						elseif($submit == '' | $submit == null) { // otherwise it's not an array and check if submitted form field is empty or null
							$this->feedback->SetType(FeedbackTypes::Error); // if so, error is 'yes'
							$this->feedback->SetMsg('Required field "'.$required.'" not filled out.'); // add a node to the 'errorMsg' array
							$this->_error = true;
						}
					}
				}
			}
			return true;
		}

		/**
		* Loops through each form field and corresponding confirmation field in the $_matching array to check if both values match
		* Designed to work with two form fields of the same name that form an array (i.e. <input name="password[]"... />)
		* Sets any error messages as appropriate
		*
		* @access private
		* @return void
		*/
		public function checkMatching() {
			foreach($this->_matching as $matching) { // for each node of the $_matching array
				foreach($this->_submit as $key => $submit) { // for each node of the $_submit array
					if($matching == $key) { // if the $_matching field name and the $_submit field name match
						if($submit[0] != $submit[1]) { // check that value of the first field of this pair equals the matching confirmation field
							$this->feedback->SetType(FeedbackTypes::Error); // if so, error is 'yes'
							$this->feedback->SetMsg('"'.$matching.'" field and associated confirmation field don\'t match.'); // add a node to the 'errorMsg' array
							$this->_error = true;
						}
					}
				}
			}
			return true;
		}
		

		public function setError($bool)
		{
			$this->_error = $bool;
		}

		
		public function setErrorMsg($string)
		{
			$this->_errorMsg[] = $string;
		}
		
		public function setMsg($msg)
		{
			$this->feedback->SetMsg($msg);
		}
		
		/**
		* Retrieves the submitted form data from the object's memory
		* @return array $this->_submit
		*/
		public function getSubmit() {
			return $this->_submit;
		}

		/**
		* Retrieves the error data from the object's memory
		* @return string $this->_error
		*/
		public function getError() {
			return $this->_error;
		}
	
		/**
		* Retrieves the errorMsg array from the object's memory
		* @return array $this->_errorMsg
		*/
		public function getErrorMsg() {
			return $this->_errorMsg;
		}

		/**
		* Assembles and outputs html based on errors present
		* @return string $this->_msg (html output)
		*/
		public function getMsg() {
			$this->setMsgType();
			$this->feedback->SetMsg($this->_msg);
			return $this->feedback->getMsg();
		}
		
		public function setMsgType() {
			if($this->_error) { $this->feedback->setType(FeedbackTypes::Error); }
			elseif($this->_warning) { $this->feedback->SetType(FeedbackTypes::Warning); }
			else { $this->feedback->SetType(FeedbackTypes::Success); }
		}
	}
?>