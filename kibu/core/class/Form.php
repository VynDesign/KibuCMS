<?php

	require_once './kibu/core/class/Postback.php';

	/**
	 * A class designed to generate html forms and cleanse data
	 *
	 *
	 * @package kibu
	 * @author Vyn Raskopf
	 * @copyright kibu 2009
	 * @version 1.0.0
	 */

	class Form {
	
		/**
		 * VARIABLES
		 */

		/**
		 * Stores the css class of the form
		 *
		 * @var string 
		 */
		protected $_class;

		/**
		 * Stores the file to post form data to (usually the same page the form is on)
		 *
		 * @var string 
		 */
		protected $_action;

		/**
		 * Stores the form method (POST/GET)
		 *
		 * @var string 
		 */
		protected $_method;

		/**
		 * Stores an array of fields that require input
		 *
		 * @var array 
		 */
		protected $_required = array();

		/**
		 * Stores an array of fields that need to match
		 *
		 * @var array 
		 */
		protected $_matching = array();

		/**
		 * Stores the values posted through the form
		 *
		 * @var array
		 */
		protected $_submit = array();

		/**
		 * Stores an array of error messages generated in processing
		 *
		 * @var array 
		 */
		protected $_errorMsg = array();

		/**
		 * Stores the css class of the form
		 *
		 * @var string 
		 */
		protected $_error = false;

		/**
		 * Stores the message that is sent back to the viewer after an event occurs
		 *
		 * @var string 
		 */
		protected $_msg;
	
		/**
		* METHODS
		*/

		/**
		* Constructor, sets the _submit field to the posted form data
		*/ 
		public function __construct() {
			$this->_submit = $_POST;
		}

		/**
		* Loads form fields that require a value into memory
		*
		* @param array $required
		* @access public
		* @return void
		*/
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
		private function checkRequired() {
			foreach($this->_required as $required) { // for each required field
				foreach($this->_submit as $key => $submit) { // for each posted value
					if($required == $key) { // if this is the required field that matches the posted data
						if(is_array($submit)) { // if the data is another array
							foreach($submit as $submit2) { // for each node in this array
								if($submit2 == '' | $submit2 == null) { // check if the submitted form field is empty or null
									$this->_error = true; // if so, error is 'yes'
									$this->_errorMsg[] = 'Required field "'.$required.'" not filled out.'; // add a node to the 'errorMsg' array
								}
							}
						}
						elseif($submit == '' | $submit == null) { // otherwise it's not an array and check if submitted form field is empty or null
							$this->_error = true; // if so, error is 'yes'
							$this->_errorMsg[] = 'Required field "'.$required.'" not filled out.'; // add a node to the 'errorMsg' array
						}
					}
				}
			}
		}

		/**
		* Loops through each form field and corresponding confirmation field in the $_matching array to check if both values match
		* Designed to work with two form fields of the same name that form an array (i.e. <input name="password[]"... />)
		* Sets any error messages as appropriate
		*
		* @access private
		* @return void
		*/
		private function checkMatching() {
			foreach($this->_matching as $matching) { // for each node of the $_matching array
				foreach($this->_submit as $key => $submit) { // for each node of the $_submit array
					if($matching == $key) { // if the $_matching field name and the $_submit field name match
						if($submit[0] != $submit[1]) { // check that value of the first field of this pair equals the matching confirmation field
							$this->_error = true; // if not, error is 'yes'
							$this->_errorMsg[] = '"'.$matching.'" field and associated confirmation field don\'t match.'; // add a node to the 'errorMsg' array
						}
					}
				}
			}
		}
		
				// check for email address validity
		public function validateEmailAddress($emailAddress) {
			if(!ereg("^[^@]{1,64}@[^@]{1,255}$", $emailAddress)) { // check that there's one '@' symbol, and that the lengths are right
				return false; // invalid email: wrong number of characters in one section, or wrong number of @ symbols.
			}
			// Split it into sections to make life easier
			$email_array = explode("@", $emailAddress); // disassemble email address at the '@' symbol, we get two parts in an array - one part before the '@', one after
			$local_array = explode(".", $email_array[0]); // disassemble the first part of the array at any '.'
			for($i = 0; $i < sizeof($local_array); $i++) { // loop through $local_array for illegal characters
				if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) { // if illegal characters found
					return false; // invalid email: illegal characters in local part
				}
			}
			if(!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
				$domain_array = explode(".", $email_array[1]); // explode domain (second part of email address) at any '.'
				if(sizeof($domain_array) < 2) { // if parts are less than 2 (should be at least 'domain.tld')
					return false; // invalid email: Not enough parts to domain
				}
				for($i = 0; $i < sizeof($domain_array); $i++) { // loop through $domain_array for illegal characters
					if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) { // if illegal characters found
						return false; // invalid email: illegal characters in domain
					}
				}
			}
			return true; // if it passes all inspections, this is a valid email
		}
		//
		

		/**
		* Sets $_class, $_action, $_method and outputs a fully formed html <form> opening tag
		* 
		* @param string $class css class
		* @param string $action file to post back to
		* @param optional string $method defaults to 'post'
		* @access public
		* @return html <form> opening tag
		*/
		public function formBegin($class, $action, $method = "post") {
			$this->_class = $class;
			$this->_action = $action;
			$this->_method = $method;
			$begin = "<form class=\"" . $this->_class . "\" action=\"" . $this->_action . "\" method=\"" . $this->_method . "\" enctype=\"multipart/form-data\">";
			return $begin;
		}
		
		/**
		* Outputs an opening html <fieldset> tag with optional css class and legend tag
		* 
		* @param optional string $class css class
		* @param optional string $legend html <legend> tag
		* @access public
		* @return html <fieldset class="$class"> and optional html <legend>$legend</legend> tags
		*/
		public function beginFieldset($class = Null, $legend = Null) {
			$beginFieldset = "<fieldset";
			if($class != Null) {
				$beginFieldset .= " class=\"$class\"";
			}
			$beginFieldset .= ">\n";
			if($legend != Null ) {
				$beginFieldset .= "\t<legend>$legend</legend>\n";
			}
			return $beginFieldset;
		}

		/**
		* Outputs a closing html </fieldset> tag
		*
		* @access public
		* @return html </fieldset> tag
		*/
		public function endFieldset() {
			$endFieldset = "</fieldset>\n";
			return $endFieldset;
		}

		/**
		* Outputs a form field (depending on $type argument) complete with label
		*
		* @param string $type determines what kind of form field (input, select, textarea, etc...)
		* @param string $name sets the name of the form field
		* @param string $label sets label text for the field
		* @param optional array $options associative array of options for a select box
		* @param optional array $value sets a default value for a field
		* @param optional string $extra adds any extra parameters to the tag (for adding javascript events, size of select box, etc).
		*/

		public function field($type, $name, $label, $options = Null, $value = Null, $extra = Null) {
			if($type == "select") {
				echo $extra;
				$field = $this->select($name, $value, $options, $extra);
			}
			elseif($type == "textarea") {
				$field = $this->textarea($name, $value);
			}
			else {
				$field = $this->input($type, $name, $value);
			}
			if($label != Null) {
				$field = $this->label($label, $name, $field, $type);
			}
			return $field;
		}	
		

		/**
		* Outputs html <label>...</label> tags around a field returned by field() method
		* 
		* @param string $label sets the text ouptut inside the <label> tag
		* @param string $name sets the form field name that the label is in reference to
		* @param string $field is the html output from field() method
		* @param string $type determines where the $field is positioned in relation to the $label text
		* @return $field
		*/
		public function label($label, $name, $field, $type) {
			if($type == 'checkbox') {
				$field = "<label for=\"$name\" style=\"word-wrap:no-wrap;\">".$field."".$label."</label>\n";
			}
			else {
				$field = "<label for=\"$name\">".$label.":<br />".$field."</label>\n";
			}	
			return $field;
		}

		/**
		* Outputs an html <input> tag based on params
		*
		* @param string $type sets the input type (text, hidden, password, etc...)
		* @param string $name sets the name/id by which the submitted data will be referenced
		* @param optional string $value sets a default value for the form field
		* @return $input
		*/
		public function input($type, $name, $value = null) {
			$input = "<input type=\"" . $type . "\" id=\"" . $name . "\" name=\"" . $name. "\" value=\"" . $value . "\" />";
			return $input;
		}
		
		/** 
		* Outputs an html <select> tag based on params
		*
		* @param string $name sets the name/id by which the submitted data will be referenced
		* @param string $value sets a default value for the form field
		* @param string $options sets the <option>s inside the select box
		* @param string $extra sets any extra parameters for the <select> tag
		* @return $select
		*/
		public function select($name, $value, $options, $extra) {
			$select = "<select name=\"" . $name . "\" id=\"" . $name . "\" " . $extra . ">\n";
			$select .= $this->_option(Null, '.:Choose:.', Null);
			if($options  == "y/n") {
				$select .= $this->_yesNo($value);
			}
			elseif(is_array($options)) {
				foreach($options as $key => $option) {
					$select .= $this->_option($key, $option, $value);
				}
			}
			$select .= "</select>\n";
			return $select;
		}
		
		/** 
		* Outputs an html <textarea>...</textarea> tag
		*
		* @param string $name sets the name/id by which the posted data will be referenced
		* @param string $value sets a default value of the text within the <textarea>$value</textarea>
		* @return string $textarea
		*/
		public function textarea($name, $value) {
			$textarea = "<textarea name=\"" . $name . "\" id=\"" . $name . "\">".$value."</textarea>\n";
			return $textarea;
		}
		
		/**
		* Outputs an html <option> tag for use in <select> tag
		*
		* @param string $key sets the value of the option tag
		* @param string $option sets the string dipslayed to the viewer for this option
		* @param string $value sets a default value for the option tag, outputting " selected="selected"
		* @return string $option
		*/
		private function _option($key, $option, $value) {
			$output = "<option value=\"$key\"";
			if($key == $value) {
				$output .= " selected=\"selected\"";
			}
			$output .= ">$option</option>\n";
			return $output;
		}
		
		/**
		* Outputs html <option> tags with the values of 'yes' or 'no'
		* 
		* @param string $value sets a default value for for the option tag, in this case one of 'n' or 'y'
		* @return string $option
		*/
		private function _yesNo($value) {
			$array = array("y" => "Yes", "n" => "No");
			$option = '';
			foreach($array as $key => $val) {
				$option .= $this->_option($key, $val, $value);
			}
			return $option;
		}

		/**
		* Outputs a closing html </form> tag
		*
		* @return string $end
		*/
		public function formEnd() {
			$end = "</form>";
			return $end;
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
			$postback = New Postback($this->_error, $this->_errorMsg, $this->_msg);
			$this->_msg = $postback->getMsg();
			return $this->_msg;
		}
	}
?>