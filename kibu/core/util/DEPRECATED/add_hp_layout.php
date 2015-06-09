<?php


require_once $_SERVER['DOCUMENT_ROOT'].'/kibu/core/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/kibu/core/framework/data/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/kibu/core/System/Utility.php';

$db = new Database(Utility::loadConfig());

$db->setQuery("SELECT assetTypeID, assetOrderNum, assetName, contentZoneID FROM `contentRecordAssets` WHERE  `contentRecordNum` LIKE  '464F3A67-3249-46DF-80CC-FAE96E6FCADD' AND contentZoneID != 1");

$db->getAssocArray();

$assets = $db->returnData;

foreach($assets as $key => $val) {
		$val['layoutID'] = 5;
	echo "<pre>";		
		print_r($val);
	echo "</pre>";
		
	$db->insert("publishingLayoutsAssets", $val);
	
	if($db->getAffectedRows() > 0) {
		echo $db->getAffectedRows();
	}
	else {
		echo $db->getError();
	}
}

?>
