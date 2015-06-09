<?php
	
	require_once './kibu/core/framework/data/Database.php';

	class DBTable extends Database {
	
		public $DBFields;
		protected $_db;
		protected $_tableName;
		
		public function __construct($config = array(), $dbTableName = null) {
			parent::Database($config);
			if($dbTableName != null) {
				$this->_tableName = $dbTableName;
			}
			
		}
		
		public function Create() {
			
		}
	
		public function FromXML(SimpleXMLElement $node) {
			
		}
	
	}

?>
