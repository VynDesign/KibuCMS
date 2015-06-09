<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/kibu/core/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/kibu/core/framework/data/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/kibu/core/System/Utility.php';

$db = new Database($config);
$db->setQuery("SELECT * FROM userRecords");
while($result = $db->getAssoc()) {
	$users[] = $result;
}

foreach($users as $user => $data) {
	$userName = $data['userName'];	
	if($data['userGUID'] == null) {
		$userGUID = Utility::guidGen();
		if($db->setQuery("UPDATE userRecords SET userGUID = '".$userGUID."' WHERE userName = '".$userName."' LIMIT 1")) {
			echo "User '".$userName."' updated with GUID '".$userGUID."'.<br />";
		}
	}
	else {
		echo "User '".$userName."' already has GUID assigned.<br />";
	}	
}



?>
