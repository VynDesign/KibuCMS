<?php

/**
 * Description of NavigationSection
 *
 * @author vyn
 */

	require_once './kibu/core/framework/html/HtmlAnchorLinkCollection.php';

	class NavigationSection extends HtmlAnchorLinkCollection {
	
		public $ID;
		public $CssClass;
		public $ItemCssClass;
		public $ItemLinkCssClass;
		public $selectedItemCssClass;
		public $selectedItemLinkCssClass;
		
		protected $sectionNum;
		protected $sectionName;
		protected $sectionFullName;
		protected $visible = true;
		protected $selected = false;
		protected $landingPageRecordNum;

		public function __construct() {
			parent::__construct(ListTypes::unordered);
			
		}
		
		protected function _setAnchorLinkCollection() {
			foreach($this->_nav->localNode as $key => $value) {
				$relUrl = "/".$value['sectionName']."/".$value['titleClean'].".".$this->_nav->extension;
				$items[$relUrl] = $value['contentTitle'];
				if($this->_nav->section == $value['sectionName'] && $this->_nav->filename == $value['titleClean']) {
					$this->selectedItem = $relUrl;
				}
			}
			$this->_anchorLinkCollection = new HtmlAnchorLinkCollection($items);
			$this->_anchorLinkCollection->SetItemClass($this->listItemLinkClass);
			if(key_exists($this->selectedItem, $this->_anchorLinkCollection->Retrieve())) {
				$this->_anchorLinkCollection->{$this->selectedItem}->AppendClass($this->selectedListItemLinkClass);
			}
			$this->_anchorLinkCollection->Finalize();			
		}


		protected function _setHtmlList() {
			$this->_list = new HtmlList(ListTypes::unordered, $this->_anchorLinkCollection);
			$this->_list->attributes->Add("id", $this->listID);
			$this->_list->attributes->Add("class", $this->listClass);
			$this->_list->listItemsCollection->SetItemClass($this->listItemClass);
			if(key_exists($this->selectedItem, $this->_list->listItemsCollection->Retrieve())) {			
				$this->_list->listItemsCollection->{$this->selectedItem}->AppendClass($this->selectedListItemClass);
			}
			$this->_params['nav'] = $this->_list->GetMarkup();
		}		
	}

?>
