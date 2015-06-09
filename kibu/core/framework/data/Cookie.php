<?php
	/**
	 * A class designed to ease/automate setting of cookies
	 * 
	 * @package Kibu
	 * @author Vyn Raskopf
	 * @copyright Kibu 2010
	 * @version 1.0.2
	 */

	class Cookie {
	
		/**
		 * VARIABLES
		 */
		
		/**
		 * Stores the prefix of the cookie name, to make all cookies unique to the site
		 *
		 * @var string
		 */
		public $_cookiePrefix;
		/**
		 * Stores the suffix of the cookie name, which determines what kind of cookie it is
		 *
		 * @var string
		 */
		public $_cookieSuffix;
		/**
		 * Stores the name used as a suffix appended to $_cookiePrefix to make a unique cookie name within the site
		 *
		 * @var string
		 */
		public $_cookieName;
		/**
		 * Stores the full name of the cookie, in the form of $_cookiePrefix.'_'.$_cookieSuffix
		 *
		 * @var string
		 */
		public $_cookieValue;
		/**
		 * Stores the time of expiry for the cookie, defaults to null, effecively the end of visitors session
		 *
		 * @var int
		 */
		public $_cookieTime = null;
		/**
		 * Stores the path to be set for cookie, defaults to "/"
		 *
		 * @var string
		 */
		public $_cookiePath = "/";
		/**
		 * Stores the domain name from which the cookie may be accessed
		 *
		 * @var string
		 */
		public $_cookieDomain;


		/**
		 * METHODS
		 */			

		/*
		 * Constructor, sets the ingredients for bakeCookie() method
		 *
		 * @param 	string 	$cookieSuffix 	effectively the name of the cookie, appended to $_cookiePrefix
		 * @param 	string 	$cookieValue 	value to be stored in the cookie 
		 */
		public function __construct($cookieName = null) {
			$this->setCookiePrefix();
			$this->setCookieName($cookieName);
			$this->setCookieTime('1', 'mos');
			$this->setCookieDomain();
		}
		/*
		 * Sets the domain that the cookie can be accessed from, as drawn from the URL class
		 */
		public function setCookieDomain() {
			global $url;
			$this->_cookieDomain = $url->getCookieDomain();
		}
		/*
		 * Sets the prefix used in the name of the cookie, as drawn from the Constants class
		 */		
		public function setCookiePrefix() {
			global $url;
			$siteConfig = $url->siteConfig;
			$this->_cookiePrefix = $siteConfig['cookiePrefix'];
		}
		/*
		 * Sets the full $_cookieName by appending the suffixe to the prefix
		 */
		public function setCookieName($cookieSuffix) {
			$this->_cookieSuffix = $cookieSuffix;
			$this->_cookieName = $this->_cookiePrefix . '_' . $this->_cookieSuffix;
		}
		/*
		 * Sets the value to be stored in the cookie
		 *
		 * @param 	string 	$cookieValue 	info to be stored in cookie 
		 */
		public function setCookieValue($cookieValue) {
			$this->_cookieValue = $cookieValue;
		}
		/*
		 * Gets the value stored in the cookie
		 *
		 * @return 	string 	$cookieValue 	info stored in cookie 
		 */
		public function getCookieValue() {
			return $this->_cookieValue;
		}
		/*
		 * Sets the date/time in which the cookie expires
		 *
		 * @param 	int 	$time 	accepts any integer. default is '1'
		 * @param 	string 	$units 	accepts one of mins, hrs, days, mos, null. default is 'mos' 
		 */
		public function setCookieTime($time, $units) {
			switch(strtolower($units)) {
				case 'mins':
					$min = 60;
					$this->_cookieTime = time() + $min * $time;
					break;
				case 'hrs':
					$hour = 60 * 60; // setting cookie times. three hours for admin password cookie
					$this->_cookieTime = time() + $hour * $time;
					break;
				case 'days':
					$day = 60 * 60 * 24; // one day for regular member password cookie
					$this->_cookieTime = time() + $day * $time;
					break;
				case 'mos':
					$month = 60 * 60 * 24 * 30; // one month for all username cookies
					$this->_cookieTime = time() + $month * $time;
					break;
				case null:
					$this->_cookieTime = null;
				default:
					return 'Invalid Property';
			}
		}

		public function isCookieSet() {
				if(isset($_COOKIE[$this->_cookieName])) {
						return true;
				}
				else {
						return false;
				}
		}

		/*
		 * Sets the cookie based on all pre-set or publicly declared vars
		 * For test environment purposes, localhost is checked and not passed to the setcookie() method.
		 * Any other value for $_cookieDomain is passed to setcookie().
		 *
		 */
		function bakeCookie($name, $value = null) {
			//$this->setCookieName($name);
			$this->setCookieValue($value);
			if($this->_cookieDomain == 'localhost') {
				setcookie($this->_cookieName, $this->_cookieValue, $this->_cookieTime, $this->_cookiePath);			
			}
			else {
				setcookie($this->_cookieName, $this->_cookieValue, $this->_cookieTime, $this->_cookiePath, $this->_cookieDomain);			
			}
		}

		/*
		 * Destroys the cookie by setting the time to a value in the past
		 */		
		function eatCookie() {
			$this->_cookieTime = time() - 100;
			$this->bakeCookie($this->_cookieName);
		}
	}

?>