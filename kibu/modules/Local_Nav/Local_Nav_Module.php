<?php

/**
 * Description of local_nav_class
 *
 * @author vyn
 */

	require_once './kibu/core/Content/Module.php';
	require_once './kibu/core/Content/interface/IModuleEdit.php';
	require_once './kibu/core/Navigation/Navigation.php';
	require_once './kibu/core/framework/html/HtmlAnchorLinkCollection.php';
	require_once './kibu/core/framework/html/HtmlList.php';

	class Local_Nav_Module extends Module {
		
		protected $listID;// = "localNav";
		protected $listClass;// = "localNav";
		protected $listItemClass;// = "localNavItem";
		protected $listItemLinkClass;// = "localNavItemLink";
		protected $selectedListItemClass;// = "selected";
		protected $selectedListItemLinkClass;// = "selected";
		protected $selectedItem;
		
		private $_anchorLinkCollection;
		private $_list;
		private $_nav;

		public function __construct($requestedModule = null) {
			parent::__construct($requestedModule);
			$this->_nav = new Navigation();
			$this->_nav->getCurrentNode();
						
			foreach($this->_params as $key => $value) {
				$this->{$key} = $value;
			}
			
			if(is_array($this->_nav->localNode)) {
				$this->_setAnchorLinkCollection();
				$this->_setHtmlList();
			}
		}
		
		protected function _setAnchorLinkCollection() {
			foreach($this->_nav->localNode as $key => $value) {
				$relUrl = "/".$value['sectionName']."/".$value['titleClean'].".".$this->_nav->_url->extension;
				$items[$relUrl] = $value['contentTitle'];
				if($this->_nav->section == $value['sectionName'] && $this->_nav->filename == $value['titleClean']) {
					$this->selectedItem = $relUrl;
				}
			}
			$this->_anchorLinkCollection = new HtmlAnchorLinkCollection($items);
			$this->_anchorLinkCollection->SetItemClass($this->listItemLinkClass);
			if(array_key_exists($this->selectedItem, $this->_anchorLinkCollection->Retrieve())) {
				$this->_anchorLinkCollection->{$this->selectedItem}->AppendClass($this->selectedListItemLinkClass);
			}
			$this->_anchorLinkCollection->Finalize();
		}


		protected function _setHtmlList() {
			$this->_list = new HtmlList(ListTypes::unordered, $this->_anchorLinkCollection);
			$this->_list->attributes->Add("id", $this->listID);
			$this->_list->attributes->Add("class", $this->listClass);
			$this->_list->listItemsCollection->SetItemClass($this->listItemClass);
			if(array_key_exists($this->selectedItem, $this->_list->listItemsCollection->Retrieve())) {			
				$this->_list->listItemsCollection->{$this->selectedItem}->AppendClass($this->selectedListItemClass);
			}
			$this->_params['nav'] = $this->_list->GetMarkup();
		}
	}
?>
