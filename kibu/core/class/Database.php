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

				public $connected;
				public $table_name = '';


		/*
		 *
		 */
		function Database() {
				require './kibu/core/config.php';
				$this->_config = $config;
				foreach($this->_config as $key => $value) {
						$this->{$key} = $value;
				}
				if($this->_host == null || $this->_user == null || $this->_pass == null || $this->_db == null) {
						$this->connected = false;
				}
				else {
						$this->connected = true;
						$this->connect();
				}
		}

		protected function connect() {
				$this->_linkID = @mysql_connect($this->_host, $this->_user, $this->_pass);
				if (!$this->_linkID) {
						$this->connected = false;
						Throw new RuntimeException("Could not connect to server: <b>$this->_host</b>.");
				}
				if(!@mysql_select_db($this->_db, $this->_linkID)) { //no database
						$this->connected = false;
						Throw new RuntimeException("Could not open database: <b>$this->_db</b>.");
				}
		}

		public function setQuery($sql) {
			$this->_query = mysql_query($sql, $this->_linkID);
			if(!$this->_query) {
				Throw new RuntimeException("MySql query failed trying to $sql");
			}
			else {
				$this->_affectedRows = mysql_affected_rows();
				return true;
			}
		}

		public function getHost() {
			return $this->_host;
		}

		public function getQuery() {
			return $this->_query;
		}

		public function getRow() {
			$row = mysql_fetch_row($this->_query);
			return $row;
		}

		public function getAssoc() {
			$assoc = mysql_fetch_assoc($this->_query);
			return $assoc;
		}

		public function getNumRows() {
			$numRows = mysql_num_rows($this->_query);
			return $numRows;
		}

		public function insertQuery() {
			$insert = mysql_query($this->_query);
			return $insert;
		}

		public function getError() {
				return $this->_error;
		}

		/*
		* desc: sends insert query to database using an array
		* @param: table, assoc array of data (doesn't need escaped)
		* returns: (query_id) for fetching results etc
		*/
		public function insert($table, $data) {
			$q="INSERT INTO `" . $table . "` ";
			$v=''; $n='';
			foreach($data as $key=>$val) {
				$n.="`$key`, ";
				if(strtolower($val)=='null') $v.="NULL, ";
				elseif(strtolower($val)=='now()') $v.="NOW(), ";
				else $v.= "'".$this->escape($val)."', ";
			}

			$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";

			if($this->query($q)){
				//$this->free_result();
				return mysql_insert_id();
			}
			else return false;
		}

		/*
		* desc: sends update query to database using an array
		* @param: table, assoc array of data (doesn't need escaped), where condition
		* returns: (query_id) for fetching results etc
		*/
		function update($table, $data, $where='1') {
			$q="UPDATE `" . $table . "` SET ";
			foreach($data as $key=>$val) {
				if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
				elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
				else $q.= "`$key`='".$val."', ";
			}
			$q = rtrim($q, ', ') . ' WHERE '.$where.';';
			return $this->setQuery($q);
		}

		/*
		* @desc: disconnects from MySql database
		* @param: ID of MySql connection link
		*
		*/
		public function disconnect() {
			if(!mysql_close($this->_linkID)){
				Throw new RuntimeException("Connection close failed.");
			}
		}
	}

?>