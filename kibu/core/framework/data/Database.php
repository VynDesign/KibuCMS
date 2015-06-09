<?php
	/**
	 * A class designed to ease connecting to and querying from a MySql database
	 *
	 * @package Kibu
	 * @author Vyn Raskopf
	 * @copyright Kibu 2010
	 * @version 1.0.0
	 */

	class Database {
		/*
		 *
		 */
			protected $_config;
			protected $_host;
			protected $_user;
			protected $_pass;
			protected $_db;
			protected $_query;
			protected $_record = array();
			protected $_error = "";
			protected $_errno = 0;
			protected $_linkID = 0;
			protected $_affectedRows = 0;
			protected $_connection;
			protected $_result;
			
			public $error = false;
			public $configured;
			public $connected;
			public $table_name = '';
			public $returnData;


		/*
		 *
		 */	
			
		public function Database($config = array()) {
			if(!$config == null) {
				$this->_config = $config;
				foreach($this->_config as $key => $value) {
					$this->{$key} = $value;
				}
				if($this->_host == null || $this->_user == null || $this->_pass == null || $this->_db == null) {
					$this->configured = false;
					$this->error = true;					
				}
				else {
					$this->configured = true;
					$this->connect();
				}
			}
			else {
				$this->configured = false;
				$this->error = true;
			}
		}

//		protected function connect() {
//				$this->_linkID = @mysql_connect($this->_host, $this->_user, $this->_pass);
//				if (!$this->_linkID) {
//						$this->connected = false;
//						Throw new RuntimeException("Could not connect to server: <b>$this->_host</b>.");
//				}
//				elseif(!@mysql_select_db($this->_db, $this->_linkID)) { //no database
//						$this->connected = false;
//						Throw new RuntimeException("Could not open database: <b>$this->_db</b>.");
//				}
//				else {$this->connected = true;}
//		}
		
		protected function connect() {
			if($this->configured) {
				$this->_connection = new mysqli($this->_host, $this->_user, $this->_pass, $this->_db);
				$this->_errno = $this->_connection->connect_errno;
				$this->_error = $this->_connection->connect_error;
				if($this->_errno > 0) {
					$this->error = true;
					$this->connected = false;
					Throw new RuntimeException("Error connecting to database: ".$this->error);
				}
				else {
					$this->connected = true;
				}
			}
		}

//		public function setQuery($sql) {
//			$this->_query = mysql_query($sql, $this->_linkID);
//			if(!$this->_query) {
//				Throw new RuntimeException("MySql query failed trying to $sql");
//			}
//			else {
//				$this->_affectedRows = mysql_affected_rows();
//				return true;
//			}
//		}
		
		public function startTransaction() {
			$this->_connection->autocommit(FALSE);
		}
	
		public function cancelTransaction() {
			$this->_connection->rollback();
		}
		
		public function endTransaction() {
			$this->_connection->commit();
		}
		
		public function setQuery($sql) {
			if(!$result = $this->_connection->query($sql)){
				$this->error = true;
				$this->_error = $this->_connection->error;
				Throw new RuntimeException("Error encountered running the query: " . $this->_error . "");
			}
			else {
				$this->_result = $result;
			}
		}
		
//		
//		public function getHost() {
//			return $this->_host;
//		}

		public function getQuery() {
			return $this->_query;
		}

		public function getRow() {
			return $this->_result->fetch_row();
		}

		public function getAssoc() {
			return $this->_result->fetch_assoc();
		}

		public function getAssocArray() {
			$this->returnData = null;
			while($assoc = $this->getAssoc()) {
				$this->returnData[] = $assoc;
			}
		}
		
		public function getNumRows() {
			return $this->_result->num_rows;
		}

		public function insertQuery() {
			$insert = mysql_query($this->_query);
			return $insert;
		}

		public function getError() {
			$this->_error = $this->_connection->error;
			return $this->_error;
		}

		public function getAffectedRows() {
			return $this->_connection->affected_rows;
		}
		
		
		/*
		* desc: sends insert query to database using an array
		* @param: table, assoc array of data (doesn't need escaped)
		* returns: (query_id) for fetching results etc
		*/
		public function insert($table, $data = array()) {
			$q="INSERT INTO `" . $table . "` ";
			$v=''; $n='';
			foreach($data as $key => $val) {
				$val = $this->_connection->escape_string($val);
				$n.="`$key`, ";
				if(strtolower($val)=='null') $v .= "NULL, ";
				elseif(strtolower($val)=='now()') $v .= "NOW(), ";
				else $v .= "'".$val."', ";
			}

			$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";

			echo $q."<br />";
			if($this->setQuery($q)){
				$this->_connection->free_result();
				return $this->_connection->insert_id();
			}
			else return false;
		}
		
		/*
		* desc: sends update query to database using an array
		* @param: table, assoc array of data (doesn't need escaped), where condition
		* returns: (query_id) for fetching results etc
		*/
		public function update($table, $data = array(), $where='1') {
			$q="UPDATE `" . $table . "` SET ";
			foreach($data as $key => $val) {
				$val = $this->_connection->escape_string($val);
				if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
				elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
				else $q.= "`$key`='".$val."', ";
			}

			$q = rtrim($q, ', ') . ' WHERE '.$where.';';
			$this->setQuery($q);
		}

		/*
		* @desc: disconnects from MySql database
		* @param: ID of MySql connection link
		*
		*/
//		public function disconnect() {
//			if(!mysql_close($this->_linkID)){
//				Throw new RuntimeException("Connection close failed.");
//			}
//		}
		
		public function disconnect() {
			if(!$this->_connection->close()) {
				Throw new RuntimeException("MySqli connection failed to close");
			}
		}
	}

?>