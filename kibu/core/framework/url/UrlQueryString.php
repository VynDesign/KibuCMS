<?php

	//namespace kibu\core\framework\url {

		class UrlQueryString {

			protected $queryString;
			protected $queryParts;

			public function __construct($queryString = null) {
				if($queryString == null) {
					$this->queryString = $_SERVER['QUERY_STRING'];
				}
				else {
					$this->queryString = $queryString;
				}
				parse_str($this->queryString, $this->queryParts);
			}

			public function __get($name) {
				if(key_exists($name, $this->queryParts)) { return $this->queryParts[$name]; }
				return false;
			}	
						
			public function __toString() {
				return $this->queryString;
			}
			
			public function GetParts() {
				return $this->queryParts;
			}
		}
	//}
?>
