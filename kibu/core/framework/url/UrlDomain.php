<?php

	//namespace kibu\core\framework\url {

	class UrlDomain {

			protected $host;
			protected $tld;
			protected $domain;
			protected $subdomain;
			protected $domainParts;


			public function __construct($domain = null) {
				if($domain == null) { $this->host  = $_SERVER['HTTP_HOST']; }
				else { $this->host = $domain; }
				$this->setDomainParts();
			}

			public function __toString() { return $this->host; }

			public function __get($name) {
				if(property_exists($this, $name)) { return $this->$name; }
				else { return "Requested property '".$name."' not recognized at UrlDomain"; }
			}

			protected function setDomainParts() {
				$this->domainParts = explode('.', $this->host);
				$partsCount = count($this->domainParts);

				$partsReverse = array_reverse($this->domainParts);
				$this->tld = $partsReverse[0];
				$this->domain = $partsReverse[1];

				if($partsCount >= 3) {
					$this->subdomain = array_reverse(array_slice($partsReverse, 2));
				}
				else{
					$this->subdomain = $partsReverse[3];
				}
			}

		}
	//}
?>
