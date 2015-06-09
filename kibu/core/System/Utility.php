<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
	require_once './kibu/core/framework/data/Database.php';
	require_once './kibu/core/Navigation/Url.php';
	require_once './kibu/core/System/config.php';

	class Utility {
				
		public static function loadConfig() {
			global $config;
			return $config;
		}

		// generate random password for forgotten password.
		public static function generateRandStr($length, $mode = null) {
			$string = "";  // empty out $string variable to avoid conflicts
			if($mode == 'alpha') {
				$possible = "bcdfghjkmnpqrstvwxyz";  // define possible characters
			}
			elseif($mode == 'num') {
				$possible = "0123456789";  // define possible characters
			}
			else {
				$possible = "0123456789bcdfghjkmnpqrstvwxyz";  // define possible characters
			}
			$i = 0;   // set up a counter
			while ($i < $length) { // add random characters to $string until $length is reached
				$char = substr($possible, mt_rand(0, strlen($possible)-1), 1); // pick a random character from the possible ones
				if (!strstr($string, $char)) { // we don't want this character if it's already in the password
					$string .= $char;
					$i++;
				}
			}
			return $string; // done!
		}



		// check for email address validity
		public static function validateEmail($emailAddress) {
			if(preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])*(\.([a-z0-9])([-a-z0-9_-])([a-z0-9])+)*$/i', $emailAddress)) {
				return true; // if it passes inspection, this is a valid email
			}
			return false;
		}

		//
		public static function getUserLevels() {
			$db = new Database(Utility::loadConfig());
			$db->setQuery("SELECT * FROM userLevels ORDER BY levelNum");
			while($userLevelArray = $db->getAssoc()){
				foreach($userLevelArray as $value) {
					$userLevels[$userLevelArray['levelNum']] = $userLevelArray['levelName'];
				}
			}
			return $userLevels;
		}
		//

		//
		public static function getSections() {
			$db = new Database(Utility::loadConfig());
			$url = new URL_ext();
			$query = "SELECT navigationSections.*, navigationSectionsOrder.*
				FROM navigationSections, navigationSectionsOrder, siteConfig
					WHERE navigationSections.sectionNum = navigationSectionsOrder.sectionNum
						AND navigationSections.siteConfigID = siteConfig.siteConfigID
						AND siteConfig.siteConfigID = '".$url->siteConfig['siteConfigID']."'
					ORDER BY sectionOrderNum";
			$db->setQuery($query);
			while($sectionsArray = $db->getAssoc()){
				$sections[$sectionsArray['sectionID']] = $sectionsArray;
			}
			return $sections;
		}
		//

		public static function getSectionPages($sectionID) {
			$db = new Database(Utility::loadConfig());
			$query = "SELECT *
				FROM contentRecords
					WHERE contentRecords.sectionID = '".$sectionID."'
					ORDER BY orderNum";
			$db->setQuery($query);
			if($db->getNumRows() > '0') {
				while($pagesArray = $db->getAssoc()) {
					$pages[$pagesArray['contentRecordNum']] = $pagesArray;
				}
				return $pages;
			}
		}

		public static function getSectionsAndPages() {
			$sections = Utility::getSections();
			foreach($sections as $section) {
				$sections[$section['sectionID']]['pages'] = Utility::getSectionPages($section['sectionID']);
			}
			return $sections;
		}

		public static function guidGen() {
			if(function_exists('com_create_guid') === true) {
				$guid = trim(com_create_guid(), '{}');
			}
			else {
				$guid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
			}
			return $guid;
		}

		public static function isGUID($string) {
			return preg_match("/^\{?[a-f\d]{8}-(?:[a-f\d]{4}-){3}[a-f\d]{12}\}?$/i", $string) ? true : false;
		}
		
		public static function stripChars($string) {
			$string = str_replace(' ', '', $string);
			$string = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $string);
			return $string;
		}
		
		public static function pastDateYearArray() {
			$yearArray = array_combine(range(date('Y'), date('Y') - 50), range(date('Y'), date('Y') - 50));
			return $yearArray;
		}

		public static function dateYearArray() {
			$yearArray = array_combine(range(date('Y'), date('Y') + 10), range(date('Y'), date('Y') + 10));
			return $yearArray;
		}

		public static function dateMonthFullArray() {
			$monthArray['01'] = 'January';
			$monthArray['02'] = 'February';
			$monthArray['03'] = 'March';
			$monthArray['04'] = 'April';
			$monthArray['05'] = 'May';
			$monthArray['06'] = 'June';
			$monthArray['07'] = 'July';
			$monthArray['08'] = 'August';
			$monthArray['09'] = 'September';
			$monthArray['10'] = 'October';
			$monthArray['11'] = 'November';
			$monthArray['12'] = 'December';

			return $monthArray;
		}

		public static function dateMonthAbbrvArray() {
			$monthArray['01'] = 'Jan';
			$monthArray['02'] = 'Feb';
			$monthArray['03'] = 'Mar';
			$monthArray['04'] = 'Apr';
			$monthArray['05'] = 'May';
			$monthArray['06'] = 'Jun';
			$monthArray['07'] = 'Jul';
			$monthArray['08'] = 'Aug';
			$monthArray['09'] = 'Sep';
			$monthArray['10'] = 'Oct';
			$monthArray['11'] = 'Nov';
			$monthArray['12'] = 'Dec';

			return $monthArray;
		}

		public static function dateDayArray() {
			$dayArray['01'] = '1';
			$dayArray['02'] = '2';
			$dayArray['03'] = '3';
			$dayArray['04'] = '4';
			$dayArray['05'] = '5';
			$dayArray['06'] = '6';
			$dayArray['07'] = '7';
			$dayArray['08'] = '8';
			$dayArray['09'] = '9';
			$dayArray['10'] = '10';
			$dayArray['11'] = '11';
			$dayArray['12'] = '12';
			$dayArray['13'] = '13';
			$dayArray['14'] = '14';
			$dayArray['15'] = '15';
			$dayArray['16'] = '16';
			$dayArray['17'] = '17';
			$dayArray['18'] = '18';
			$dayArray['19'] = '19';
			$dayArray['20'] = '20';
			$dayArray['21'] = '21';
			$dayArray['22'] = '22';
			$dayArray['23'] = '23';
			$dayArray['24'] = '24';
			$dayArray['25'] = '25';
			$dayArray['26'] = '26';
			$dayArray['27'] = '27';
			$dayArray['28'] = '28';
			$dayArray['29'] = '29';
			$dayArray['30'] = '30';
			$dayArray['31'] = '31';

			return $dayArray;

		}

		public static function timeHoursArray() {
			$timeHoursArray['12'] = '12';
			$timeHoursArray['01'] = '1';
			$timeHoursArray['02'] = '2';
			$timeHoursArray['03'] = '3';
			$timeHoursArray['04'] = '4';
			$timeHoursArray['05'] = '5';
			$timeHoursArray['06'] = '6';
			$timeHoursArray['07'] = '7';
			$timeHoursArray['08'] = '8';
			$timeHoursArray['09'] = '9';
			$timeHoursArray['10'] = '10';
			$timeHoursArray['11'] = '11';

			return $timeHoursArray;
		}

		public static function timeMinutesSecondsArray() {
			$timeMinutesSecondsArray['00'] = '00';
			$timeMinutesSecondsArray['05'] = '05';
			$timeMinutesSecondsArray['10'] = '10';				
			$timeMinutesSecondsArray['15'] = '15';
			$timeMinutesSecondsArray['20'] = '20';
			$timeMinutesSecondsArray['25'] = '25';
			$timeMinutesSecondsArray['30'] = '30';
			$timeMinutesSecondsArray['35'] = '35';
			$timeMinutesSecondsArray['40'] = '40';
			$timeMinutesSecondsArray['45'] = '45';
			$timeMinutesSecondsArray['50'] = '50';
			$timeMinutesSecondsArray['55'] = '55';

			return $timeMinutesSecondsArray;
		}

		public static function timeAmPmArray() {
			$ampmArray['am'] = 'am';
			$ampmArray['pm'] = 'pm';
			return $ampmArray;
		}

		public static function alphabet() {
			for ($i = 65; $i <= 90; $i++) {
				$alphabet[] = chr($i);
			}
			return $alphabet;
		}
		
		public static function concatArray($array, $delimiter, $usekeys = false) {
			$string = "";
			foreach($array as $key => $value) {
				if($usekeys) {
					$string .= $key . $delimiter;
				}
				else {
					$string .= $value . $delimiter;
				}
			}
			$dLen = strlen($delimiter);
			$string = substr($string, 0, -$dLen);
			return $string;
		}
		
		
		public static function pre($array) {
			echo "<pre>";
			print_r($array);
			echo "</pre>";
		}
	}
	

?>
