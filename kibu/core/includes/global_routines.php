<?php
	
	/* moved to kibu/core/class/Content.php, tested and approved 05-11-09
	// page title routine. keeps page title from appearing redundant by checking for pages that are the same name as sections they reside in
	function pageTitle($siteAddress, $sectionFullName, $contentTitle, $siteTagLine, $isSiteDefault) {
		$pageTitle = $siteAddress;
		if($isSiteDefault == 'y') {
			$pageTitle .= " - ". $siteTagLine;
		}
		elseif($sectionFullName == $contentTitle) {
			$pageTitle .= " - ". $sectionFullName;
		}
		elseif(($sectionFullName != $contentTitle)) {
			$pageTitle .= " - ". $sectionFullName ." - ". $contentTitle;
		}
		return $pageTitle;
	}
	//
	*/

	// grab userRecords userID based on cookie
//	if(isset($_COOKIE[$uCookie])) { // if the cookie for username is available
//		$username = $_COOKIE[$uCookie]; // turn username into more manageable variable
//		if(isset($_COOKIE[$pCookie])) {
//			$password = $_COOKIE[$pCookie];
//		}
// 		$userQuery = mysql_query("SELECT userRecords.userID, userRecords.username, userRecords.password, userLevels.levelName FROM userRecords, userLevels where username = '$username' AND userLevels.levelNum = userRecords.userLevelNum");
//		$userArray = mysql_fetch_array($userQuery);
//		$userID = $userArray['userID']; // turn user ID into more manageable variable
//		$userLevel = $userArray['levelName'];
//		$userName = $userArray['username'];
//	}
//	elseif((!isset($_COOKIE[$uCookie])) && (!isset($_COOKIE[$pCookie]))) {
//		$userID = '0';
//		$memberLevelAuth = '0'; 
//	}		
	//

/* moved to kibu/core/class/Authentication.php tested and approved 05-11-09
	//
	function getUserInfo($username){
		$userQuery = mysql_query("SELECT userRecords.*, userLevels.levelName FROM userRecords, userLevels where username = '$username' AND userLevels.levelNum = userRecords.userLevelNum");
	   	$userArray = mysql_fetch_assoc($userQuery);
		return $userArray;
	}
	//
*/

/* moved to kibu/core/class/Authentication.php tested and approved 05-11-09
	// Check login routine
	function checkLogin($uLevel, $userCookie, $passCookie) { // if a page is member-only this function is called to make sure the appropriate permission levels are upheld. accepts user level, username cookie and password cookie as arguements.
		if($uLevel == '0') { // if user level of the page in question is 0, page is open to all visitors... 
			return true; // returns true and outputs the content of the page
		}
		elseif((!isset($userCookie)) || (!isset($passCookie))) { // if the cookie does not exist, no way to tell what visitor's user level is...
			return false; // returns false and outputs the login form
		}
		elseif(isset($userCookie)) { // if the cookie does exist...
	        $check = mysql_query("SELECT password, userLevelNum FROM userRecords WHERE userName = '$userCookie'") or die(mysql_error()); // select the password and user level that corresponds to this username cookie in the DB or die trying
			while($info = mysql_fetch_assoc($check)) {
				if($passCookie != $info['password']) { // Checks if the cookie has the wrong password
					return false; // outputs the login form
				}
				elseif($passCookie == $info['password'] && $info['userLevelNum'] < $uLevel) { // if password cookie is the same as the password drawn from the database, but the userlevel is not high enough to view content...
					header("Location: http://".$_SERVER['HTTP_HOST']."/unauthorized/"); // redirect to unauthorized page.
				}
				else { // otherwise, everything checks out...
					return true; // so returns true and outputs the content of the page
				}
			}
		}
	}
	//
*/

/* moved to kibu/modules/registration/registration_class.php, 05-11-09
	function register($post, $siteEmail, $siteAddress, $curDate, $curTime) {
		$message = "\t\t\t<p class=\"message\">";
		if((isset($_POST['register'])) | (isset($_POST['updateprofile']))) { //Checks to see if the registration form has been submitted
			$userName = addslashes($_POST['userName']); // store the POST variable username as a more manageable variable
			$password = (md5($_POST['password'])); // store and "md5" encrypt the POST variable password as a more manageable variable
			$pwConf = (md5($_POST['pwConf']));
			$emailAddress = $_POST['emailAddress'];
			if(isset($_POST['authcode'])) {
				$emailConf = $_POST['emailAddress'];
			}
			else {
				$emailConf = $_POST['emailConf'];
			}
			$firstName = addslashes($_POST['firstName']);
			$lastName = addslashes($_POST['lastName']);
			$nickName = addslashes($_POST['nickName']);
			$namePref = $_POST['namePref'];
			$initIPAddr = $_SERVER['REMOTE_ADDR'];
			$joinDate = $curDate;
			$joinTime = $curTime;
			$emailVerifyString = uniqid(time());
			$uNameCheckQuery = mysql_query("SELECT userID FROM userRecords WHERE userName = '".$userName."'")or die(mysql_error()); //Checks the login data against the database
			$uNameCheck = mysql_num_rows($uNameCheckQuery); // find number of rows (should be 0) with that username
			$emailCheckQuery = mysql_query("SELECT userID FROM userRecords WHERE emailAddress = '".$emailAddress."'")or die(mysql_error());
			$emailCheck = mysql_num_rows($emailCheckQuery); // find number of rows (should be 0) with that email address
			if(isset($_POST['authcode'])) {
				if(!$userName | !$password | !$pwConf | !$emailAddress | $password != $pwConf | $uNameCheck > '0') {  //This catches all errors, and delineates error messages below
					$message .= "Registration has failed. There were one or more problems submitting your registration:</p>\n\t\t\t<ul class=\"message\">";
					if(!$userName | !$password |!$pwConf | !$emailAddress | !$emailConf | !$firstName | !$lastName) { // if required field(s) not filled out
						$message .="<li>You failed to supply information for a required field.</li>\n";	
					}
					if($password != $pwConf) { // if password doesn't match the re-typed password
						$message .="<li>Your password did not match the password re-typed in the confirmation field.</li>\n";
					}
					if($uNameCheck > '0') { // if username check comes back with a value other than 0
						$message .="<li>That username is already in our system.</li>\n";	
					}
					if($emailCheck > '0') { // if username check comes back with a value other than 0
						$message .="<li>That email address is already in our system.</li>\n";	
					}
					$message .= "\t\t\t</ul>\n";
				}
			}
			elseif(!$userName | !$password | !$pwConf | !$emailAddress | !$emailConf | $password != $pwConf | $uNameCheck > '0' | !validateEmailAddress($emailAddress) | $emailCheck > '0' | $emailAddress != $emailConf) {  //This catches all errors, and delineates error messages below
				$message .= "There were one or more problems submitting your registration:</p>\n\t\t\t<ul class=\"message\">";
				if(!$userName | !$password |!$pwConf | !$emailAddress | !$emailConf) { // if required field(s) not filled out
					$message .="<li>You failed to supply information for a required field.</li>\n";	
				}
				if($password != $pwConf) { // if password doesn't match the re-typed password
					$message .="<li>Your password did not match the password re-typed in the confirmation field.</li>\n";
				}
				if($uNameCheck > '0') { // if username check comes back with a value other than 0
					$message .="<li>That username is already in our system.</li>\n";	
				}
				if(!validateEmailAddress($emailAddress)) { // if the email is not valid
					$message .="<li>The email address you supplied seems to be invalid.</li>\n";	
				}
				if(!isset($_POST['authcode'])) {
					if($emailCheck > '0') { // if the email is already in the system
						$message .="<li>That email address is already in our system.</li>\n";	
					}
				}
				if($emailAddress != $emailConf) { // if the email address does not match the re-typed email address
					$message .="<li>The email address supplied does not match the email re-typed in the confirmation field</li>\n";
				}
				$message .= "\t\t\t</ul>\n";
			}
			else {
				if(isset($_POST['authcode'])) {
					$authcode = $_POST['authcode'];
					$memberInsert = "UPDATE userRecords SET
						userName = '$userName',
						password = '$password',
						firstName = '$firstName',
						lastName = '$lastName',
						nickName = '$nickName',
						namePref = '$namePref',
						emailVerified = 'y',
						verifyDate = '$curDate',
						verifyTime = '$curTime',
						userLevelNum = '1' 
						WHERE emailAddress = '$emailAddress' 
						AND emailVerifyString = '$authcode'"; // create new member insert query
				}
				else {
					$memberInsert = "INSERT INTO userRecords SET 
						userName = '$userName',
						password = '$password',
						firstName = '$firstName',
						lastName = '$lastName',
						nickName = '$nickName',
						namePref = '$namePref',
						emailAddress = '$emailAddress',
						initIPAddr = '$initIPAddr',
						joinDate = '$curDate',
						joinTime = '$curTime',
						emailVerified = 'n',
						emailVerifyString = '$emailVerifyString',
						userLevelNum = '0'"; // create new member insert query
				}
				if(mysql_query($memberInsert)) { // if new member insert query executed successfully
					if(!isset($_POST['authcode'])) {
						if(sendEmail("$firstName $lastName", $siteEmail, $emailAddress, $configArray['regConfirmEmailBody'], 'regConfirm', $emailVerifyString)) { // and if send of authentication email is successful
							$message .= "Your registration has been processed, but your account is not yet verified. You will receive an email at the address you supplied shortly with a verification link. At that time, click on (or copy and paste) the full web address into your favorite web browser and your account will be verified.</p>\n"; // output success message
						}
						else { // otherwise, if send of email was unsuccessful
							$message .= "There was a problem processing your registration. Please try again, and <a href=\"/ContactUs/?section=ContactUs&page=ContactUs\">contact the administrator</a> if you continue to have problems.</p>\n"; // output error message
						}
					}
					else {
						$message .= "Your registration has been processed. You may now log in at the top of the screen using the credentials you submitted</p>\n";
					}
				}
				else {
					$message .= "There was a problem processing your registration. Please try again, and <a href=\"/ContactUs/?section=ContactUs&page=ContactUs\">contact the administrator</a> if you continue to have problems.</p>\n"; // output error message
				}
			}
			return $message;
		}
	}
	//
	*/

/* moved to kibu/modules/registration/registration_class.php, 05-11-09
//
function regAuth($authcode) {
	if(isset($_GET['authcode'])) { // if authcode parameter present in URI
		$authcode = $_GET['authcode']; // store authcode parameter as more manageable variable
		if(isset($_GET['email'])) {
			$email = $_GET['email'];
			$memberQuery = mysql_query("SELECT userID, emailVerifyString FROM userRecords WHERE emailAddress = '$email'");
			$member = mysql_fetch_array($memberQuery);
			$userID = $member['userID'];
			$emailVerifyString = $member['emailVerifyString'];
			if($authcode != $emailVerifyString) {
				$message = "\t\t\t\t<p>That authorization code does not match the userRecord for that email address.</p>\n";
			}
			else {
				profileForm(Null, $userID, 'newsletterreg', "/area/register/page/processed/"); // run regForm function from 'includes/global_routines.php'
			}
		}
		else {
			$userQuery = mysql_query("SELECT userID, userName, password, firstName, lastName, emailAddress FROM userRecords WHERE emailVerifyString = '$authcode'"); // query database for applicable user record
			$userNumRows = mysql_num_rows($userQuery); // check to make sure this authcode exists (should be 1)
			$userArray = mysql_fetch_array($userQuery); // create array from query
			$firstName = $userArray['firstName'];
			$lastName = $userArray['lastName'];
			$userID = $userArray['userID'];
			if($userNumRows == '0') {
				$message = "\t\t\t<p>The authorization code you have entered seems to be invalid. If you navigated here through an email that prompted you to authorize your newly created account, there seems to have been an error. If you copied and pasted the URL from the email, please ensure that the entire web address is accounted for, including the long string of letters and numbers after the word \"authcode=\".\n";
				$message .= "\t\t\t<p>If you have followed all directions and are still getting an error, please contact the administrator.</p>\n";
				$message .= "\t\t\t<p>If you have navigated here by accident and have not registered with us, why not <a href=\"register.php\">register now</a>!?/p>\n";
			}
			else {
				$memberUpdate = "UPDATE userRecords SET 
					userLevelNum = '1', 
					emailVerified = 'y', 
					verifyDate = '$curDate', 
					verifyTime = '$curTime' 
					WHERE userID = '$userID' 
					AND emailVerifyString = '$authcode'";
				if(mysql_query($memberUpdate)) { // if update query executed successfully, ouptut success message
					$message = " Your email address and account have been verified. You may use your username and password to login in the form at the top of the site.</p>\n";
				}
				else { // otherwise, update query did not execute successfully, output error message
					$message = " There was a problem authenticating your email address and account information. Contact the administrator in order to rectify the situation.</p>\n";
				}
			}
		}
		return $message;
	}
}
//
*/

/* moved to kibu/modules/registration/registration_class.php, 05-11-09
//
function resetPassword($post, $siteEmail, $siteAddress) {
	if(isset($_POST['resetpassword'])) {
		$emailAddress = $_POST['emailAddress'];
		$emailQ = "SELECT userID, userName, password FROM userRecords WHERE emailAddress = '$emailAddress'"; // get userId, userName, and original password from database where the email address is the same as the posted form data
		$emailQuery = mysql_query($emailQ);
		$emailNumRows = mysql_num_rows($emailQuery); // number of rows that contain that email address (should be 1)
		if($emailNumRows == '0') { // if the number of rows that contain that email address is '0' (i.e. not in the database), output error message
			$message = "<p class=\"message\">The email address you entered doesn't appear to be in our system. Please try again by clicking the your browswer's \"back\" button .</p>\n";
		}
		else { // otherwise, we continue...
			$emailArray = mysql_fetch_array($emailQuery); // output array of the query
			$userID = $emailArray['userID'];
			$userName = $emailArray['userName']; // turn userName into variable	
			$oldPassword = $emailArray['oldPassword'];
			$newPassword = generatePassword(8); // generate a new random password using generatePassword function from 'inc/inc.global_routines.php'
			$newHashPassword = md5($newPassword); // create encrypted password to insert into database
			$resetPassword = "UPDATE userRecords SET password = '$newHashPassword' WHERE userID = '$userID'"; // create sql update of new encrypted password
			if(mysql_query($resetPassword)) { // if the sql statement is successfully executed
				$emailBody = $configArray['resetPWEmailBody'];
				if(sendEmail($userName, $siteEmail, $emailAddress, $emailBody, 'resetpassword', $newPassword, $siteAddress)) { // send email with new password and output success message
					$message = "<p>Your password has been successfully reset. Watch your email for the confirmation containing your username and newly generated password. Once you log in with the new credentials, you may navigate to \"My Profile\" in the \"Site Actions\" menu in the upper-right hand corner of the site to change your password back to something you will more easily remember.</p>\n";
				}
				else { // if the email encountered a problem, put old password back and output error message
					$unresetPassword = "UPDATE userRecords SET password = '$oldPassword' WHERE userID = '$userID'";
					if(mysql_query($unresetPassword)) { // if the old password sql is executed correctly
						$message = "<p>There was a problem emailing your credentials. Your original password has been restored. Please try again and contact the administrator for assistance if the problem persists.</p>\n";
					}
					else { // if everything totally fails for some reason, output catastrohpic failure message
						$message = "<p>There were multiple problems with resetting your password. Please contact the administrator for assistance.</p>\n";
					}
				}
			}
			else { // if the sql statement was not successfully executed, output error message.
				$message = "<p>There was a problem resetting your password. Please try again and contact the administrator for assistance if the problem persists.</p>\n";
			}
		}
		return $message;
	}
}
//
*/

/* moved to kibu/core/class/Authentication.php, tested and approved 04-23-09
//
function setLogin($post, $curDate, $curTime, $uCookie, $pCookie, $cookiedomain, $redirect) {
	if(isset($_POST['login'])) { //Checks to see if the login form has been submitted either from this page or from the header login function (inc/inc.global_routines.php)
		$username = $_POST['username']; // store the POST variable username as a more manageable variable
		$password = (md5($_POST['pass'])); // store and "md5" encode the POST variable password as a more manageable variable
		$check = checkPass($username, $_POST['pass'], NULL);
		if(!$username | !$password | !$check) {  // This makes sure they filled all the required fields and the username has a member record
			$message = "\t\t\t<p class=\"message\">An error occurred submitting your login:</p>\n\t\t\t<ul>";
			if(!$username | !$password) { // if required field(s) not filled out
				$message .="<li class=\"message\">You failed to supply information for a required field.</li>\n";	
			}
			if(!$check) { // if the password or username are wrong
				$message .="<li class=\"message\">You submitted an invalid username or password.<br />Forgot your login credentials?</a> <a href=\"resetpassword.html\">Reset your password</a>!</li>\n";
			}
			$message .= "\t\t\t</ul>\n";
			return $message;
		}
		else {
			$username = stripslashes($username);
			$lastIPAddr = $_SERVER['REMOTE_ADDR'];
			$lastActive = mysql_query("UPDATE userRecords SET lastActiveDate = '$curDate', lastActiveTime = '$curTime', lastIPAddr = '$lastIPAddr' WHERE userName = '$username'");
			$threehours = time() + 10800; // setting cookie times. three hours for admin password cookie
			$day = time() + 60 * 60 * 24; // one day for regular member password cookie
			$month = time() + 60 * 60 * 24 * 30; // one month for all username cookies
			setcookie($uCookie, $username, $month, "/", $cookiedomain);
			setcookie($pCookie, $password, $day, "/", $cookiedomain);
			header("Location:$redirect");
		}
	}
}
//
*/

/* made obsolete by Cookie::eatCookie() method in kibu/core/class/Cookie.php 04-23-09 
//
function cookieDestroy($cookie, $urlPath, $cookiedomain) {
	//This script will kill the cookie immediately
	$past = time() - 100;
	//this makes the time in the past to destroy the cookie
	setcookie($cookie, "", $past, "/", $cookiedomain); //deletes the username cookie
	header("Location: $urlPath");
}
*/

/* moved to kibu/core/class/Authentication.php tested and approved 05-11-09
// check password. a simple function to check whether a username and password match up
function checkPass($username, $password, $md5pass) {
	if($password !=Null) {
		$pass = md5($password);
	}
	else {
		$pass = $md5pass;
	}
	$passQuery = mysql_query("SELECT userID, userLevelNum FROM userRecords WHERE userName = '$username' AND password = '$pass'");
	$numrows = mysql_num_rows($passQuery);
	if($numrows > '0') { // if $numrows is greater than 0 (it should only be 1, but just to be sure...)
		$userInfo = mysql_fetch_assoc($passQuery);
		return $userInfo;
	}
	else { // if $numrows is 0, username and password don't match up.
		return false;
	}
}
//
*/

/* moved to kibu/core/class/Content.php, tested and approved 05-13-09
//
function getUserLevels() {
	$query = mysql_query("SELECT * FROM userLevels ORDER BY levelNum");
	while($userLevelArray = mysql_fetch_assoc($query)){
		$userLevels[$userLevelArray['levelNum']] = $userLevelArray;
	}	
	return $userLevels;	
}
//
*/

// Submit Date routine
function submitDate($submitDate) {
   $formattedSubDate = date("M d, Y", strtotime($submitDate));
   return $formattedSubDate;
}
//

// Submit Time routine
function submitTime($submitTime) {
	$formattedSubTime = date("g:i:s a", strtotime($submitTime));
	return $formattedSubTime;
}

//

// Edit Date routine
function editDate ($editDate) {
   if ($editDate !=="0000-00-00") {
     echo "<p>Last edited on: ";
     $formattedEditDate = date("M d, Y", strtotime($editDate));
     return $formattedEditDate;
   }
}
//

// Month Year routine
function monthYear($submitDate) {
	$fauxDate = "".$submitDate."-01";
	$formattedDate = date("F Y", strtotime($fauxDate));
	return "$formattedDate";
}
//

// Year-month routine
function yearMonth($submitDate) {
	$fauxDate = "".$submitDate."-01";
	$formattedDate = date("Y-m", strtotime($fauxDate));
	return "$formattedDate";
}
//

// Month Day, Year routine
function monthDayYear($submitDate) {
	$formattedDate = date("F d, Y", strtotime($submitDate));
	return "$formattedDate";
}
//

// truncate body routine
function truncate($text, $number) {
  $text_array = explode(' ', $text);
  for($x = 0; $x < $number; $x++) {
    echo $text_array[$x], ' ';
  }
  echo "... ";
}
//

//
function stripChars($string) {
	$string = strtolower($string);
	$string = str_replace(' ', '-', $string);
	$string = preg_replace('/[^a-zA-Z0-9 -]/s', '', $string);
	return $string;
}
//

/* moved to kibu/modules/publishing/publishing_class.php 04-24-09
//
function generateContentRecordNum($sectionID, $curDate, $curTime) {
	$strippedDate = str_replace('-', '', $curDate);
	$strippedTime = str_replace(':', '', $curTime);
	$contentRecordNum = $strippedDate."-".$strippedTime."-".$sectionID;
	return $contentRecordNum;
}
//
*/

/* moved to kibu/modules/publishing/publishing_class.php 04-24-09

//
function publishContent($post, $curDate, $curTime, $userID) {
	$message =  "\t\t\t\t<p class=\"message\">";
	$contentTitle = htmlentities($_POST['contentTitle']);
	if($_POST['titleClean'] == Null) {
		$titleClean = stripChars($_POST['contentTitle']);
	}
	else {
		$titleClean = $_POST['titleClean'];
	}
    $metaKeywds = $_POST['metaKeywds'];
    $metaDesc = $_POST['metaDesc'];
    $isVisible = $_POST['isVisible'];
    $sectionID = $_POST['sectionID'];
    $isSectionDefault = $_POST['isSectionDefault'];
    $visitorAuthLevel = $_POST['visitorAuthLevel'];
    $editorAuthLevel = $_POST['editorAuthLevel'];
	$contentTypeID = $_POST['contentTypeID'];
    $commentsOpen = $_POST['commentsOpen'];
    $showComments = $_POST['showComments'];
    $approveComments = $_POST['approveComments'];
    $responseAuthLevel = $_POST['responseAuthLevel'];
	if(isset($_POST['CreateContent'])) {
		$contentRecordNum = generateContentRecordNum($_POST['sectionID'], $curDate, $curTime);
		$sql = "INSERT INTO contentRecords SET
			contentTitle='$contentTitle',
			titleClean='$titleClean',
			ownerID='$userID',
			authorID='$userID',
			metaKeywds='$metaKeywds',
			metaDesc='$metaDesc',
			isVisible='$isVisible',
			sectionID='$sectionID',
			isSectionDefault='$isSectionDefault',
			visitorAuthLevel='$visitorAuthLevel',
			editorAuthLevel='$editorAuthLevel',
			contentTypeID='$contentTypeID',
			commentsOpen='$commentsOpen',
			showComments='$showComments',
			approveComments='$approveComments',
			responseAuthLevel='$responseAuthLevel',
			contentRecordNum='$contentRecordNum'";
		if(mysql_query($sql)) {
			$message .=  "Page created successfully.";
			$message .= "Content Record Number: $contentRecordNum";
			//header("Location:http://".$_SERVER['HTTP_HOST']."/borked/");		
		}
		else {
			$message .= "There was a problem creating page: ".mysql_error()."";
		}
	}
	elseif(isset($_POST['UpdateContent'])) {	
		$contentID = $_POST['contentID'];
	    $contentRecordNum = $_POST['contentRecordNum'];
		foreach($_POST['contentAsset'] as $key => $value) {
			$assetUpdate = "UPDATE contentRecordAssets SET assetBody = '$value', assetEditDate = '$curDate', assetEditTime = '$curTime' WHERE assetID = '$key' AND contentRecordNum = '$contentRecordNum'";
			if(mysql_query($assetUpdate)){ }
			else { $message .= "There was a problem updating assetID '$key'<br /><br />";}			
		}
		$sql = "UPDATE contentRecords SET
			contentTitle='$contentTitle',
			titleClean='$titleClean',
			metaKeywds='$metaKeywds',
			metaDesc='$metaDesc',
			isVisible='$isVisible',
			sectionID='$sectionID',
			isSectionDefault='$isSectionDefault',
			visitorAuthLevel='$visitorAuthLevel',
			editorAuthLevel='$editorAuthLevel',
			commentsOpen='$commentsOpen',
			showComments='$showComments',
			approveComments='$approveComments',
			responseAuthLevel='$responseAuthLevel'
			WHERE contentID='$contentID' AND contentRecordNum='$contentRecordNum'";
		if(mysql_query($sql)) {
			$message .=  "Content updated successfully.";
		}
		else {
			$message .= "There was a problem updating content: ".mysql_error()."";
		}
	}
	$message .= "</p>\n";
	return $message;
}
//
*/

/* moved to kibu/modules/publishing/publishing_class.php 04-24-09

//
function publishAsset($_POST) {
	$publishAsset = "INSERT INTO contentRecordAssets SET assetBody = '$value', assetCreateDate = '$curDate', assetCreateTime = '$curTime', contentRecordNum ='$contentRecordNum'";
}
//
*/

/* moved to kibu/core/class/Content.php, tested and approved 04-08-09
//
function getSections() {
	$query = mysql_query("SELECT * FROM contentSections ORDER BY sectionOrderNum");
	while($sectionsArray = mysql_fetch_assoc($query)){
		$sections[$sectionsArray['sectionID']] = $sectionsArray;
	}
	return $sections;
}
//
*/

/* modified and moved to kibu/core/class/Content.php, tested and approved 04-08-09
//
function getContentRecord($titleClean, $section){
	// if the URL has no parameters it's the homepage, so get the content of the default section page for the default site section
	if($titleClean == '' | $titleClean == Null) { 
		$queryAppendix = "contentRecords.sectionID = contentSections.sectionID AND contentSections.isSiteDefault = 'y' AND contentRecords.isSectionDefault = 'y'";	
	}
	else { 
		$queryAppendix = "contentRecords.titleClean = '$titleClean' AND contentSections.sectionName = '$section'";
	}
	// query database for content record and associated database tables/columns
	$query = "SELECT userRecords.username, contentRecords.*, contentSections.sectionID, contentSections.sectionFullName, contentSections.sectionName, templateViews.templateViewLink, siteConfig.* 
				FROM userRecords, templateViews, contentRecords, siteConfig, contentSections
				WHERE templateViews.templateViewID = contentRecords.templateViewID
					AND contentSections.sectionID = contentRecords.sectionID 
					AND userRecords.userID = contentRecords.authorID 
					AND ".$queryAppendix."";
	$contentQuery = mysql_query($query);
	$contentNumRows = mysql_num_rows($contentQuery);
	if($contentNumRows == '1') {
		$content = mysql_fetch_assoc($contentQuery);
		return $content;
	}
	else {
		header("Location:/errors/404.html");
	}
}
//
*/


/* modified and moved to kibu/core/class/Content.php, tested and approved 04-08-09
//
function getContentType($contentTypeID) {
	$query = "SELECT contentType.contentTypeName, contentControllers.*, contentPageTypes.*, templateMasters.*
			FROM contentType, contentControllers, contentPageTypes, templateViews, templateMasters
			WHERE contentType.contentTypeID = '$contentTypeID'
				AND contentType.templateMasterID = templateMasters.templateMasterID
				AND contentType.contentPageTypeID = contentPageTypes.ctID
				AND contentType.contentControllerID = contentControllers.contentControllerID";
	$contentTypeQuery = mysql_query($query);
	$contentType = mysql_fetch_assoc($contentTypeQuery);

	return $contentType;
}
//
*/

/* moved to kibu/core/class/Content.php, tested and approved 04-08-09 

//
function getContentTypes() {
	$query = mysql_query("SELECT * FROM contentType ORDER BY contentTypeName");
	while($contentTypesArray = mysql_fetch_assoc($query)){
		$contentTypes[$contentTypesArray['contentTypeID']] = $contentTypesArray;
	}
	return $contentTypes;
}
//
*/

/* moved to kibu/core/class/Content.php, tested and approved 04-08-09 
//
function getContentAssets($contentRecordNum) {
	$query = mysql_query("SELECT contentRecordAssets.*, contentAssetTypes.*, contentAssetEditFields.assetEditInput
							FROM contentRecordAssets, contentAssetTypes, contentAssetEditFields 
							WHERE contentRecordAssets.contentRecordNum = '$contentRecordNum' 
								AND contentRecordAssets.assetTypeID = contentAssetTypes.assetTypeID
								AND contentAssetEditFields.assetEditID = contentAssetTypes.assetEditID
							ORDER BY contentRecordAssets.assetOrderNum ASC");
	while($assetsArray = mysql_fetch_assoc($query)){
		$assets[$assetsArray['assetTypeName']] = $assetsArray;
	}
	return $assets;
}
//
*/

//
function getAssetFields() {
	$query = mysql_query("SELECT contentAssetTypes.*, contentAssetEditFields.assetEditInput
							FROM contentAssetTypes, contentAssetEditFields 
							WHERE contentAssetEditFields.assetEditID = contentAssetTypes.assetEditID
							ORDER BY contentAssetTypes.assetTypeNameClean ASC");
	while($assetsArray = mysql_fetch_assoc($query)){
		$assets[$assetsArray['assetTypeName']] = $assetsArray;
	}
	return $assets;
}
//

//
function aggregateSection($sectionID) {
	$query = "SELECT userRecords.username, contentRecords.*, contentAssetTypes.*, contentRecordAssets.assetName, contentRecordAssets.assetBody, contentSections.sectionID, contentSections.sectionFullName, contentSections.sectionName
				FROM userRecords, contentSections, contentAssetTypes, contentRecords LEFT JOIN contentRecordAssets ON contentRecords.contentRecordNum = contentRecordAssets.contentRecordNum
				WHERE contentSections.sectionID = contentRecords.sectionID 
					AND userRecords.userID = contentRecords.authorID
					AND contentSections.sectionID = contentRecords.sectionID
					AND contentRecords.sectionID = '$sectionID'
					AND contentAssetTypes.assetTypeID = contentRecordAssets.assetTypeID
					AND contentAssetTypes.assetTypeNameClean = 'teaser'
					AND contentRecords.isSectionDefault != 'y'
					AND contentRecords.isVisible = 'y'
				ORDER BY contentRecords.submitDate DESC, contentRecords.submitTime DESC";
	$array = mysql_query($query);
	while($content = mysql_fetch_assoc($array)){
		$entries[$content['contentRecordNum']] = $content;
	}
	return $entries;
}
//

/* moved to kibu/modules/comments/Comments_class.php, tested and approved 04-16-09 
// Grab all comments for content record ($contentRecordNum) and, if necessary, only those that are approved
function getComments($contentRecordNum	, $approveComments) {
	$query = "SELECT contentResponses.*, userRecords.username 
					FROM contentResponses LEFT JOIN userRecords ON contentResponses.userID = userRecords.userID
					WHERE contentResponses.contentRecordNum = '$contentRecordNum'";
	if($approveComments == 'y') {
		$query .= " AND commentApproved = 'y'";
	}
	$commentQuery = mysql_query($query);
	while($commentsArray = mysql_fetch_assoc($commentQuery)){
		$comments[$commentsArray['responseID']] = $commentsArray;
	}
	return $comments;
}
*/

/* moved to kibu/modules/comments/Comments_class.php, tested and approved 04-16-09 
// Comment submission function
function commentSubmit($post, $curDate, $curTime, $approveComments) {
	if(isset($_POST['submitcomment'])) {
		$message = "\t\t\t<p id=\"commentposted\" class=\"comments message\">There was an error submitting your repsonse: ";
		if((!isset($_POST['memberID'])) && ($_POST['name'] == '' | $_POST['email'] == '')) {
			$message .= "You failed to supply at least one required field.</p>\n";
		}
		elseif((isset($_POST['password'])) && (($_POST['password'] == '') || (!checkPass($_POST['name'], $_POST['password'], NULL)))) {
			if($_POST['password'] == '') {		
				$message .= "You are not logged in and attempted to post a response as a registered member.";
			}
			else {
				$message .= "That password seems invalid. Please try again below.";
			}
		}
		else {
			$guestName = $_POST['name'];
			$guestEmail = $_POST['email'];
			$guestWebsite = $_POST['website'];
			$guestComment = $_POST['comment'];
			$guestIPAddr = $_POST['IP'];
			$contentRecordNum = $_POST['entryNum'];
			$userID = $_POST['memberID'];
			if($approveComments == 'n') {
				$commentApproved = 'y';
			}
			else {
				$commentApproved = '';
			}
			$insert = "INSERT INTO contentResponses set
				commentSubmitDate='$curDate',
				commentSubmitTime='$curTime',
				guestName='$guestName',
				guestEmail='$guestEmail',
				guestWebsite='$guestWebsite',
				guestComment='$guestComment',
				guestIPAddr='$guestIPAddr',
				contentRecordNum='$contentRecordNum',
				userID='$userID',
				commentApproved='$commentApproved'";
			if(mysql_query($insert)) {
				$message = "\t\t\t\t\t<p id=\"commentposted\" class=\"comments message\">Your response has been submitted";
				if($approveComments == 'y') {
					$message .= ", however responses for this page are subject to approval by site staff. It will be posted after it has been reviewed and approved";
				}
				$message .= ".</p>\n";
			}
			else {
				$message .= "</p>\n";
			}	
		}
		return $message;
	}
}
//
*/

/* moved to kibu/modules/contact_us/contact_us_class.php, tested and approved 04-21-09 
// contact us form submission
function contactUs($_POST, $to, $curDate, $curTime) {
	if(isset($_POST['send'])) {
		$from = $_REQUEST['from'];
		$headers = "From: $from";
 		$subject = $_REQUEST['subject'];
		$emailbody = $_REQUEST['emailbody'];
		$IPAddr = $_SERVER['REMOTE_ADDR'];
		if ((preg_match("/http/i", "$from")) || (preg_match("/http/i", "$subject")) || (preg_match("/http/i", "$emailbody"))) { 
			$message = array('messageText' => "Malicious code content detected. Your IP Number of <strong>$IPAddr</strong> has been logged.", 'error' => 'y');
		}
		elseif($to == Null | $from == Null | $subject == Null | $emailbody == Null) {
			$message = array('messageText' => "Your message could not be delivered because you neglected to fill out one or more required information fields. Please review the information below to ensure that all fields are filled out.", 'error' => 'y');
		}
		elseif(!validateEmailAddress($from)) {
			$message = array('messageText' => "That email address appears to be invalid. Please review the information below to ensure that it is correct.", 'error' => 'y');
		}
		else {
			$sent = mail($to, $subject, $emailbody, $headers);
			if($sent) {
				$insert = "INSERT INTO siteContactUs SET 
							contactUsSubmitDate = '$curDate',
							contactUsSubmitTime = '$curTime',
							contactUsIPAddr = '$IPAddr',
							contactUsEmailAddr = '$from',
							contactUsSubject = '$subject',
							contactUsBody = '$emailbody'";
				if(mysql_query($insert)) {
					$message = array('messageText' => "Your message has been sent. You will receive a response as soon as possible.", 'error' => 'n');
				}
				else {
					$message = array('messageText' => "An error occurred sending email.", 'error' => 'y');
				}
			}
			else {
				$message = array('messageText' => "An error occurred sending email.", 'error' => 'y');
			}
		}
	}
	return $message;
}
//
*/

/* moved to kibu/core/class/Email.php, tested and approved 07-01-09 
//
function sendEmail($addressee, $fromEmailAddress, $recipEmailAddress, $emailBody, $type, $extra, $siteAddress) {
	if($type == 'resetpassword') {
		$subject ="Password reset from ".$siteAddress."";
		$boundary ="".md5($extra)."";
		$textBody ="Dear ".$addressee.",\r\n";
		$textBody .="".$emailBody."";
		$textBody .="Username: ".$addressee."\n";
		$textBody .="Password: ".$extra."\n\n";
		$htmlBody ="<p>Dear ".$addressee.",</p>\n\n";
		$htmlBody .="<p>".$emailBody."</p>\n\n";
		$htmlBody .="<p>Username: ".$addressee."<br />\n";
		$htmlBody .="Password: ".$extra."</p>\n\n";
	}
	elseif($type == 'regConfirm') { // if the above variable $type is set to regConfirm, append to emailBody in both html and plain text and create email subject
		$subject ="Email address/account verification from ".$siteAddress."";
		$boundary ="".$extra."";
		$textBody ="Dear ".$addressee.",\n\n";        $textBody .="".$emailBody."\n\n";		$textBody .="http://".$_SERVER['HTTP_HOST']."/register/?authcode=$extra\n\n";
		$htmlBody ="<p>Dear ".$addressee.",</p>\n\n";
		$htmlBody .="<p>".$emailBody."</p>\n\n";		$htmlBody .="<p><a href=\"http://".$_SERVER['HTTP_HOST']."/register/?authcode=".$extra."\">".$_SERVER['HTTP_HOST']."/register/?authcode=".$extra."</a></p>\n\n";
	}
	elseif($type == 'newsletter') {
		$subject ="Newsletter subscription verification from ".$siteAddress."";
		$boundary ="".$extra."";
        $textBody ="".$emailBody."\n\n";		$textBody .="http://".$_SERVER['HTTP_HOST']."/newsletter/?authcode=".$extra."\n\n\n\n";
		$textBody .="Now is the best time to sign up as a member of the community at ".$_SERVER['HTTP_HOST']."! As a member you get access to the \"After 50 self-evaluation\" that lets you take a look at the broader picture of your quality of life between 50 and elderly!\n\n";
		$textBody .="Simply copy and paste the link below into your favorite web browser's address bar to realize the full benefit of joining the community:\n\n";
		$textBody .="".$_SERVER['HTTP_HOST']."/register/?authcode=".$extra."&amp;email=".$recipEmailaddress."\n";

		$htmlBody ="<p>".$emailBody."</p>\n\n";		$htmlBody .="<p><a href=\"http://".$_SERVER['HTTP_HOST']."/newsletter/?authcode=".$extra."\">".$_SERVER['HTTP_HOST']."/newsletter/?authcode=".$extra."</a></p><br />\n\n";
		$htmlBody .="<p>Now is the best time to sign up as a member of the community at ".$_SERVER['HTTP_HOST']."! As a member you get access to the \"After 50 self-evaluation\" that lets you take a look at the broader picture of your quality of life between 50 and elderly!</p>\n";
		$htmlBody .="<p>Simply click on the link below to realize the full benefit of joining the community:<br />\n";
		$htmlBody .="<a href=\"http://".$_SERVER['HTTP_HOST']."/register/?authcode=".$extra."&amp;email=".$recipEmailAddress."\">".$_SERVER['HTTP_HOST']."/register/?authcode=".$extra."&amp;email=".$recipEmailAddress."</a></p>\n";
	}
	$mime_boundary ="==Multipart_Boundary_x".$boundary."x";
	$headers ="From: ".$fromEmailAddress."\n";	$headers .="MIME-Version: 1.0\n";
	$headers .="Content-Type: multipart/alternative;\n";
	$headers .="\tboundary=\"".$mime_boundary."\"\n"; 
	$messageBody ="This is a multi-part message in MIME format.\n\n";	$messageBody .="--$mime_boundary\n";
	$messageBody .="Content-Type: text/plain; charset=\"iso-8859-1\"\n";	$messageBody .="Content-Transfer-Encoding: 7bit\n\n";
	$messageBody .="$textBody\r\n";
	$messageBody .="Thank you,\n\n";
	$messageBody .="-The Administrator\n\n";	$messageBody .="This is an automatically generated message, please do not respond to this email address.\n\n"; 
	$messageBody .="This is a solicited email. By joining the community at ".$_SERVER['HTTP_HOST'].", you agree to receive periodic emails.\n\n";
	$messageBody .="If you do not wish to receive these messages, copy and paste the following web address in its entirety into your web browser's address bar to unsubscribe:\n\n";
	$messageBody .="".$_SERVER['HTTP_HOST']."/newsletter/?optout=".$recipEmailAddress."\n\n"; 
	$messageBody .="--$mime_boundary\n";
	$messageBody .="Content-Type: text/html; charset=\"iso-8859-1\"\n";	$messageBody .="Content-Transfer-Encoding: 7bit\n\n";
	$messageBody .="<html><body>\n\n";
	$messageBody .="".$htmlBody."\n\n";
	$messageBody .="<p>Thank you,<br />\n\n";
	$messageBody .="-The Administrator</p>\n\n";	$messageBody .="<p>This is an automatically generated message, please do not respond to this email address.</p>\n\n";
	$messageBody .="<p>This is a solicited email. By joining the community at ".$_SERVER['HTTP_HOST'].", you agreed to receive periodic emails.<br />\n\n";
	$messageBody .="If you do not wish to receive these messages, visit the following web address to unsubscribe:<br />\n\n";
	$messageBody .="<a href=\"".$_SERVER['HTTP_HOST']."/newsletter/?optout=".$recipEmailAddress."\">".$_SERVER['HTTP_HOST']."/newsletter/?optout=".$recipEmailAddress."</a></p></body></html>\n\n"; 
	$messageBody .="--$mime_boundary--\n";
	$send = mail($recipEmailAddress, $subject, $messageBody, $headers); // utilize php mail function
	if($send) { // if email generated and sent successfully
		return true; // yay!
	}
	else { // otherwise, not sent successfully
		return false; // boo!
	}
}
//
*/

/* moved to kibu/core/class/Email.php, tested and approved 07-01-09 
// check for email address validity
function validateEmailAddress($email) {	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) { // check that there's one '@' symbol, and that the lengths are right		return false; // invalid email: wrong number of characters in one section, or wrong number of @ symbols.	}	// Split it into sections to make life easier	$email_array = explode("@", $email); // disassemble email address at the '@' symbol, we get two parts in an array - one part before the '@', one after	$local_array = explode(".", $email_array[0]); // disassemble the first part of the array at any '.'	for ($i = 0; $i < sizeof($local_array); $i++) { // loop through $local_array for illegal characters		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) { // if illegal characters found			return false; // invalid email: illegal characters in local part		}	}	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name		$domain_array = explode(".", $email_array[1]); // explode domain (second part of email address) at any '.'		if (sizeof($domain_array) < 2) { // if parts are less than 2 (should be at least 'domain.com')			return false; // invalid email: Not enough parts to domain		}		for ($i = 0; $i < sizeof($domain_array); $i++) { // loop through $domain_array for illegal characters			if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) { // if illegal characters found				return false; // invalid email: illegal characters in domain			}		}	}	return true; // if it passes all inspections, this is a valid email}
//
*/

/* moved to kibu/modules/registration/registration_class.php, 05-11-09
// generate random password for forgotten password.
function generatePassword($length) {  $password = "";  // empty out $password variable to avoid conflicts  $possible = "0123456789bcdfghjkmnpqrstvwxyz";  // define possible characters  $i = 0;   // set up a counter  while ($i < $length) { // add random characters to $password until $length is reached    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1); // pick a random character from the possible ones    if (!strstr($password, $char)) { // we don't want this character if it's already in the password      $password .= $char;      $i++;    }  }  return $password; // done!}
//
*/

?>