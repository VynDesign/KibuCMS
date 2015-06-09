<?php

	//namespace kibu\core\framework\url {

		class UrlPath {

			protected $path;
			protected $pathParts;
			protected $dirname;
			protected $dirnameParts;
			protected $basename;
			protected $filename;
			protected $extension;

			public function __construct($url = null) {
				if($url == null) { $this->path = $_SERVER['REQUEST_URI']; }
				else { $this->path = parse_url($url, PHP_URL_PATH); }

				$this->setPathParts();
				$this->setExtension();
				$this->setDirnameParts();
			}

			public function __toString() { return $this->path; }

			public function __get($name) {
				if(property_exists($this, $name)) { return $this->$name; }
				else { return "Requested property '".$name."' not recognized at UrlPath"; }
			}

			protected function setPathParts() {
				$this->pathParts = pathinfo($this->path);
				foreach($this->pathParts as $key => $value) {
					$this->$key = $value;
				}
			}

			protected function setFilename() {
				if(isset($this->pathParts['filename'])) {
					$this->filename = $this->pathParts['filename'];
				}
				else { // fix for php < ver. 5.x.x, which first introduces 'filename' node to array generated by pathinfo()
					$this->pathParts['filename'] = substr($this->pathParts['basename'], 0, strrpos($this->pathParts['basename'], '.'));
				}
			}

			protected function setExtension() {
				if(isset($this->pathParts['extension'])) {
					$this->extension = $this->pathParts['extension'];
				}
				else { $this->extension = 'html'; }
			}	

			protected function setDirnameParts() {
				$this->dirnameParts = array_values(array_filter(explode("/", $this->dirname)));
			}
		}
	//}
?>
