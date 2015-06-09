<?php

class Template {
	protected $_vars; /// Holds all the template variables
	var $path; /// Path to the templates

	/**
	 * Constructor
	 *
	 * @param string $path the path to the templates
	 *
	 * @return void
	 */
	function Template($path = null) {
		$this->path = $path;
	}

	/**
	 * Set the path to the template files.
	 *
	 * @param string $path path to template files
	 *
	 * @return void
	 */
	function set_path($path) {
		$this->path = $path;
	}

	/**
	 * Set a template variable.
	 *
	 * @param string $name name of the variable to set
	 * @param mixed $value the value of the variable
	 *
	 * @return void
	 */
	function set($name, $value) {
		$this->_vars[$name] = $value;
	}

	/**
	 * Open, parse, and return the template file.
	 *
	 * @param string string the template file name
	 *
	 * @return string
	 */
	function fetch($file) {
			if(isset($this->_vars)) {
				extract($this->_vars);         // Extract the vars to local namespace
			}
			ob_start();                    // Start output buffering
			include($this->path . $file);  // Include the file
			$contents = ob_get_contents(); // Get the contents of the buffer
			ob_end_clean();                // End buffering and discard
			return $contents;              // Return the contents
	}
   /**
    * Set a bunch of variables at once using an associative array.
    *
    * @param array $vars array of vars to set
    * @param bool $clear whether to completely overwrite the existing vars
    *
    * @return void
    */
	function set_vars($vars, $clear = false) {
       if(!$clear && isset($this->_vars)) {
           if(is_array($vars)) $this->_vars = array_merge($this->_vars, $vars);
       }
	   else {
           $this->_vars = $vars;
	   }
   } 
}

?>
