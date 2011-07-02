<?php
	class MySqlResult implements Iterator, Countable {

		protected $_current;
		protected $_key;
		protected $_valid;
		protected $_result;

		public function __construct($sql, $connection) {
			if(!$this->_result = $connection->query($sql)) {
				throw new RuntimeException($connection->error . '. The actual query submitted was: ' . $sql);
			}
		}
		
		public function rewind() {
			if(!is_null($this->_key)) {
				$this->_result->data_seek(0);
			}
			$this->_key = 0;
			$this->_current = $this->_result->fetch_assoc();
			$this->_valid = is_null($this->_current) ? false : true;	
		}
	
		public function valid() {
			return $this->_valid;
		}

		public function current() {
			return $this->_current;
		}

		public function key() {
			return $this->_key;
		}

		public function next() {
			$this->_current = $this->_result->fetch_assoc();
			$this->_valid = is_null($this->_current) ? false : true;
			$this->_key++;
		}

		public function count() {
			return $this->_result->num_rows;
		}
	
	}
?>