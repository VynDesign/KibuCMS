<?php

	//namespace kibu\core\framework\url {
		class UrlParts {

			protected $fullUrl;
			protected $parts;

			public function __construct($url = null) {
				if($url != null) {
					$this->fullUrl = $url;				
				}
				else {
					$this->determineProtocol();
					$this->fullUrl = $this->determineProtocol().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				}
				$this->parts = parse_url($this->fullUrl);			
			}

			public function __toString() { return $this->fullUrl; }

			public function __get($name) {
				if($name == 'fullUrl') {return $this->fullUrl; }
				elseif(key_exists($name, $this->parts)) {
					switch ($name) {
						case 'scheme':return $this->parts['scheme'];
						case 'user':return $this->parts['user'];
						case 'pass':return $this->parts['pass'];					
						case 'host':return $this->parts['host'];
						case 'port':return $this->parts['port'];
						case 'path':return $this->parts['path'];
						case 'query':return $this->parts['query'];
						case 'fragment':return $this->parts['fragment'];
						default: return "Property ".$name." not recognized at UrlParts";
					}
				}
			}

			protected function determineProtocol() {
				$protocol = "http://";
				if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
					$protocol = "https://";
				}
				return $protocol;
			}		
		}
	//}
?>
