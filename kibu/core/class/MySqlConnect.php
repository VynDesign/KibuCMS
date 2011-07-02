<?php
	class MySqlConnect {

		protected $_connection;
		
		public function __construct($host, $user, $pwd, $db) {
			$this->_connection = @new mysqli($host, $user, $pwd, $db);
			if(mysqli_connect_errno()) {
				throw new RuntimeException('Cannot access database: ' . mysqli_connect_error());
			}
		}

		public function getResultSet($sql) {
			$results = new MySqlResult($sql, $this->_connection);
			return $results;
		}
	
		public function __destruct() {
			$this->_connection->close();
		}
	}
?>