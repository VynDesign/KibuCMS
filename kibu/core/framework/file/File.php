<?php

	/**
	 * Description of File
	 *
	 * @author vyn
	 */
	class File {

		public $file;
		public $exists;
		public $contents;
		public $hasContents;
		public $remote;
		protected $_handle;
		protected $_cache;

		public function __construct($file) {
			$this->file = $file;			
		}
		
		public function read() {
			$this->_handle = fopen($this->file, 'r');
			if($this->_handle !== false) {
				$this->exists = true;
				@filesize($this->file) ? $this->contents = fread($this->_handle, filesize($this->file)) : $this->contents = stream_get_contents($this->_handle) ;
			}
			$this->close();
		}
		
		protected function cache() {
			$this->_cache = fopen('php://temp', 'r+');
			stream_copy_to_stream($this->_handle, $this->_cache);
		}
		
		public function create() {
			$this->_handle = fopen($this->file, 'x');
		}
		
		public function write($data) {
			$this->_handle = fopen($this->file, 'w');
			fwrite($this->_handle, $data);
		}
		
		public function append($data) {
			$this->_handle = fopen($this->file, 'a');
			fwrite($this->_handle, $data);			
		}
		
		public function close() {
			fclose($this->_handle);
		}
		
		public function delete() {
			unlink($this->file);
		}
		
		public function end() {
			return feof($this->_handle);
		}
	}

?>
