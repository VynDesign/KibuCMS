<?php

	/**
	 * Instantiates all core functionality
	 *
	 *
	 * @package Kibu
	 * @author Vyn Raskopf
	 * @copyright Kibu 2010
	 * @version 1.0.0
	 */

		// require/include all core classes
		require_once 'kibu/core/class/Database.php';
		require_once 'kibu/core/class/Url.php';
		require_once 'kibu/core/class/Constants.php';
		require_once 'kibu/core/class/Date.php';
		require_once 'kibu/core/class/Content.php';
		require_once 'kibu/core/class/Template.php';
		require_once 'kibu/core/class/Cookie.php';
		require_once 'kibu/core/class/Authentication.php';
		require_once 'kibu/core/class/Module.php';
		require_once 'kibu/core/class/Navigation.php';
		require_once 'kibu/core/class/AdditionalHead.php';
		require_once 'kibu/core/class/AdditionalFoot.php';
	
		try {
				$db = new Database(); // instantiate Database class, connect to database

				$url = new URL(); // instantiate URL class

				$constants = new Constants(); // instantiate Constants class

				$auth = new Authentication(); // instantiate Authentication class

				$date = new Date();
			
				$content = new Content(); // instantiate Content class

				$module = new Module();
		
				$navigation = new Navigation(); // instantiate Navigation class
						$globalNav = $navigation->getGlobalNav(); // set globalNav variable
						$currentNav = $navigation->getCurrentNav(); // set currentNav variable
		
				$additionalHead = new AdditionalHead(); // instantiate AdditionalHead class, used to inject code into <head></head>
				$additionalFoot = new AdditionalFoot(); // instantiate AdditionalFoot class, used to inject code into <body></body> just before closing tag after all other content

				$tpl = new Template('./kibu/templates/'); // instantiate Template class
		}
		catch(RuntimeException $e) {
				echo 'Runtime Exception encountered: ' . $e->getMessage();
		}
		catch(Exception $e) {
				echo 'Exception encountered: ' . $e->getMessage();
		}

		require_once 'kibu/core/class/Kibu.php';
		try {
				$kibu = new Kibu($date, $constants, $additionalHead, $additionalFoot, $globalNav, $currentNav, $content);
				$db->disconnect();
		}
		catch(RuntimeException $e) {
				echo 'Runtime Exception encountered: ' . $e->getMessage();
		}
		catch(Exception $e) {
				echo 'Exception encountered: ' . $e->getMessage();
		}
?>