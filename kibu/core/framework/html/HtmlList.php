<?php

/**
 * Description of HtmlList
 *
 * @author vyn
 */
	
	//namespace kibu\core\framework\html {

		require_once './kibu/core/framework/html/HtmlTag.php';
		require_once './kibu/core/framework/html/HtmlListItemCollection.php';
		
		final class ListTypes {
			const unordered = 0;
			const ordered = 1;
		}

		class HtmlList extends HtmlTag {

			protected $listItems;
			public $listItemsCollection;


			/**
			 * 
			 * @param ListTypes $listType
			 * @param array $listItems 
			 */
			public function __construct($listType = ListTypes::unordered, $listItems = array()) {
				if($listType == ListTypes::ordered) {
					parent::__construct("ol");
				}
				elseif($listType == ListTypes::unordered) {
					parent::__construct("ul");
				}

				if(count($listItems)) {
					$this->Fill($listItems);
				}			
			}

			public function Fill($listItems = array()) {
				if(is_a($listItems, "HtmlTagCollection")) {
					$items = $listItems->Retrieve();
					foreach($items as $key => $value) {
						$this->listItems[$key] = $value->GetMarkup();
					}
				}
				else {
					$this->listItems = $listItems;
				}

				$this->listItemsCollection = new HtmlListItemCollection($this->listItems);				
			}
			
			public function GetMarkup() {
				$this->text = $this->listItemsCollection->GetMarkup();			
				return parent::GetMarkup();
			}
		}
	//}
?>
