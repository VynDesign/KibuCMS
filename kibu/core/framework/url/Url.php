<?php
	/**
	 * A class designed to manipulate and utilize pieces of URL/URI
	 *
	 *
	 * @package kibu
	 * @author Vyn Raskopf
	 * @copyright kibu 2009
	 * @version 1.0.0
	 */
	
	//namespace kibu\core\framework\url {

		require_once './kibu/core/framework/url/UrlParts.php';
		require_once './kibu/core/framework/url/UrlDomain.php';
		require_once './kibu/core/framework/url/UrlPath.php';
		require_once './kibu/core/framework/url/UrlQueryString.php';

		class Url {

			protected $urlParts;
			protected $urlDomain;
			protected $urlPath;
			protected $urlQueryString;
			protected $referringURL;

			public function __construct($url = null) {

				if($url != null) {
					$this->urlParts = new UrlParts($url);
				}
				else {
					$this->urlParts = new UrlParts();	
				}
				$this->urlDomain = new UrlDomain($this->urlParts->host);
				$this->urlPath = new UrlPath($this->urlParts->path);			
				$this->urlQueryString = new UrlQueryString($this->urlParts->query);
				$this->setReferring();	
			}

			public function __toString() { return $this->urlParts->scheme."://".$this->urlDomain.$this->urlPath; }

			public function __get($name) {
				if(property_exists($this, $name)) { return $this->$name; }
				elseif(property_exists($this->urlParts, $name)) { return $this->urlParts->$name; }
				elseif(property_exists($this->urlDomain, $name)) { return $this->urlDomain->$name; } 
				elseif(property_exists($this->urlPath, $name)) { return $this->urlPath->$name; }
				elseif(property_exists($this->urlQueryString, $name)) {return $this->urlQueryString->$name; }
				elseif($name == "QueryString") { return $this->urlQueryString; }
				else { return "Requested property '".$name."' not recognized"; }
			}

			public function setReferring() {
				if(isset($_SERVER['HTTP_REFERER'])) {
					$this->referringURL = $_SERVER['HTTP_REFERER'];
				}
			}
			
//			public function isValid() {
//				if (isset($this->urlParts->host) && $this->urlParts->host != @gethostbyname($this->urlParts->host)) {				
//					if (PHP_VERSION >= 5) {
//						$headers = @implode('', @get_headers((string)$this));
//					}
//					else {
//						if (!($fp = @fsockopen($this->urlParts->host, $this->urlParts->port, $errno, $errstr, 10))) {
//							return false;
//						}
//						fputs($fp, "HEAD ".$this->urlParts->path.$this->urlParts->query." HTTP/1.1\r\nHost: ".$this->urlParts->host."\r\n\r\n");
//						$headers = fread($fp, 4096);
//						fclose($fp);
//						
//						echo $this->urlParts->path.$this->urlParts->query;
//					//}
//					return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
//				}
//				return false;				
//			}
		}
	//}
?>