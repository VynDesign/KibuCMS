<?php

	/**
	 * Instantiates all core functionality
	 *
	 *
	 * @package Kibu
	 * @author Vyn Raskopf
	 * @copyright Kibu 2010
	 * @version 1.0.0
	 *
	 * 
	 */

		// require/include all core classes
		require_once 'kibu/core/class/Database.php';
		require_once 'kibu/core/class/Url.php';
		require_once 'kibu/core/class/Kibu.php';

		try {
				$db = new Database();
				$url = new URL(); // instantiate URL class
				$kibu = new Kibu($db, $url);
				if($kibu->testConfig()) {
						$kibu->setCore();
						require_once './kibu/core/includes/loadcustom.php'; // include any custom modules
						$kibu->outputPage();
						$db->disconnect();
				}
				else {
						include_once 'kibu/core/class/Install.php';
						$install = new Install($db, $url);
				}
		}
		catch(RuntimeException $e) {
				echo 'Runtime Exception encountered: ' . $e->getMessage();
		}
		catch(Exception $e) {
				echo 'Exception encountered: ' . $e->getMessage();
		}
?>