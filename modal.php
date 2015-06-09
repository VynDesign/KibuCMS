<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

	require_once './kibu/core/Authentication/Authentication.php';
	require_once './kibu/core/framework/data/Database.php';
	require_once './kibu/core/Navigation/Url.php';
	require_once './kibu/core/Content/Modal.php';


	$db = new Database(Utility::loadConfig()); // instantiate Database class, connect to database
	$url = new URL_ext(); // instantiate URL class
	$auth = new Authentication(); // instantiate Authentication class

	try {
		$modal = new Modal(); // instantiate Modal class to run the requested process in a modal window
		echo $modal->GetModalContent();
	}
	catch(RuntimeException $e) {
		echo 'Runtime Exception encountered: ' . $e->getMessage();
	}
	catch(Exception $e) {
		echo 'Exception encountered: ' . $e->getMessage();
	}

?>
