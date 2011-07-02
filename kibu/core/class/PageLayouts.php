<?php
	
	class PageLayouts {
		
		protected $_submit;
		protected $_pageLayouts;
		protected $_layoutAssets;

		public function __construct() {
			$this->setPageLayouts();
			if((isset($_POST['pageLayout'])) && $_POST['pageLayout'] > '0') {
				$this->_submit = $_POST;
				$this->publishLayoutAssets();
			}
		}

		private function setPageLayouts() {
			$query = "SELECT * FROM publishingLayouts ORDER BY layoutName ASC";
			$query = $this->_db->setQuery($query);
			while($assoc = $this->_db->getAssoc($query)) {
				$layouts[] = $assoc;
			}
			$this->_pageLayouts = $layouts;
		}


		public function outputLayoutOptions(){
			ob_start();
			foreach($this->_pageLayouts as $pageLayout) {
				echo "<option value=\"".$pageLayout['layoutID']."\">".$pageLayout['layoutName']."</option>\n";
			}
			$layoutOptions = ob_get_contents();
			ob_end_clean();
			return $layoutOptions;
		}


		public function outputJScript() {
			ob_start();
			echo "<script type=\"text/javascript\">\n";
			echo "\t\t\t function replaceText(ID,descriptionID) {\n";
			echo "\t\t\t\t var descriptions = new Array();\n";
			foreach($this->_pageLayouts as $pageLayout) {
				echo "\t\t\t\t descriptions[".$pageLayout['layoutID']."] = \"<strong>".$pageLayout['layoutName']."</strong><br />".$pageLayout['layoutDesc']."\";\n";
			}
			echo "\t\t\t\t if(descriptionID > '0') {\n";
			echo "\t\t\t\t\t document.getElementById(ID).innerHTML = descriptions[descriptionID];\n";
			echo "\t\t\t\t }\n";
			echo "\t\t\t\t else {\n";
			echo "\t\t\t\t\t document.getElementById(ID).innerHTML = descriptions[0];\n";
			echo "\t\t\t\t }\n";
			echo "\t\t\t}\n";
			echo "\t\t</script>\n";
			$script = ob_get_contents();
			ob_end_clean();
			return $script;
		}

		public function setPageLayoutAssets() {
				global $db;
				$query = "SELECT *
							FROM publishingLayoutsAssets, contentAssetTypes
							WHERE publishingLayoutsAssets.layoutID = '".$this->_submit['pageLayout']."'
								AND publishingLayoutsAssets.assetTypeID = contentAssetTypes.assetTypeID
							ORDER BY publishingLayoutsAssets.assetOrderNum";
				$query = $db->setQuery($query);
				while($assetsArray = $db->getAssoc($query)){
					$assets[$assetsArray['assetTypeName']] = $assetsArray;
				}
			$this->_layoutAssets = $assets;
			//print_r($this->_layoutAssets);
		}

		public function publishLayoutAssets() {
			$contentRecordNum = $this->generateContentRecordNum($this->_submit['sectionID']);
			$this->setPageLayoutAssets();
			foreach($this->_layoutAssets as $asset) {
				$this->_addAsset($asset, $asset['assetOrderNum'], 'y');
			}
		}
		
		private function _addAsset() {
				$addAsset = "INSERT INTO contentRecordAssets SET
						assetTypeID = '".$array['assetTypeID']."',
						assetName = '".$array['assetName']."',
						contentRecordNum = '".$contentRecordNum."',
						assetCreateDate = '".$this->_curDate."',
						assetCreateTime = '".$this->_curTime."',
						assetOrderNum = '".$assetOrderNum."',
						isVisible = '".$isVisible."'";
				if(mysql_query($addAsset)) {
						$this->_msg = 'Asset published successfully.';
						if($isVisible == 'n') {
								$this->_msg .= ' If you have just added this asset, you may need to set any applicable parameters and approve it before it will display to visitors. You may do so on the <a href="'.$this->_page.'">content edit mode</a> of this page.';
						}
				}
		}
	}
?>