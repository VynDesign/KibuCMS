<?php

	class Date extends DateTime {
	
		protected $_year;
		protected $_month;
		protected $_day;

		public function __construct($dateString = 'now', $timezone = null) {

			if($timezone) { 
				parent::__construct($dateString, $timezone);
			}	
			else {
				parent::__construct($dateString);
			}
		
			$this->_year = (int) $this->format('Y');
			$this->_month = (int) $this->format('n');
			$this->_day = (int) $this->format('j');

		}

		public function setTime($hours, $minutes, $seconds = 0) {
			if(!is_numeric($hours) || !is_numeric($minutes) || !is_numeric($seconds)) {
				throw new Exception('Pos_Date method setTime() expects two required and one optional numbers separated by commas in the order: hours(req), minutes(req), seconds(opt)');
			}
			$outOfRange = false;
			if($hours < 0 || $hours > 23) {
				$outOfRange = true;
			}
			if($minutes < 0 || $minutes > 59) {
				$outOfRange = true;
			}
			if($seconds < 0 || $seconds > 59) {
				$outOfRange = true;
			}
			if($outOfRange) {
				throw new Exception('Time not within expected range');
			}
			parent::setTime($hours, $minutes, $seconds);
		}


		public function setDate($year, $month, $day = 1) {
			if(!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
				throw new Exception('Pos_Date method setDate() expects two required and one optional numbers separated by commas in the order: year(req), month(req), day(opt)');
			}
			if(!checkdate($month, $day, $year)) {
				throw new Exception('Non-existent date.');
			}
			parent::setDate($year, $month, $day);
			$this->_year = (int) $year;
			$this->_month = (int) $month;
			$this->_day = (int) $day;
		}

		public function modify() {
			throw new Exception('DateTime method "modify()" has been disabled for this application');
		}

		public function setMDY($USDate) {
			$dateParts = preg_split('{[-/ :.]}', $USDate);
			if(!is_array($dateParts) || count($dateParts) !=3) {
				throw new Exception('Pos_Date method setMDY() expects date in "MM/DD/YYYY" format.');
			}
			$this->setDate($dateParts[2], $dateParts[0], $dateParts[1]);
		}

		public function setDMY($EuroDate) {
			$dateParts = preg_split('{[-/ :.]}', $EuroDate);
			if(!is_array($dateParts) || count($dateParts) !=3) {
				throw new Exception('Pos_Date method setDMY() expects date in "DD/MM/YYYY" format.');
			}
			$this->setDate($dateParts[2], $dateParts[1], $dateParts[0]);
		}

		public function setFromMySQL($MySQLDate) {
			$dateParts = preg_split('{[-/ :.]}', $MySQLDate);
			if(!is_array($dateParts) || count($dateParts) !=3) {
				throw new Exception('Pos_Date method setFromMySQL() expects date in "YYYY-MM-DD" format.');
			}
			$this->setDate($dateParts[0], $dateParts[1], $dateParts[2]);
		}

		public function getMDY($leadingZeros = false) {
			if($leadingZeros) {
				return $this->format('m/d/Y');
			}
			else {
				return $this->format('n/j/Y');
			}
		}
		
		public function getDMY($leadingZeros = false) {
			if($leadingZeros) {
				return $this->format('d/m/Y');
			}
			else {
				return $this->format('j/n/Y');
			}
		}
	
		public function getMySQLFormat() {
			return $this->format('Y-m-d');
		}

		public function getFullYear() {
			return $this->_year;
		}
		
		public function getYear() {
			return $this->format('y');
		}

		public function getMonth($leadingZero = false) {
			return $leadingZero ? $this->format('m') : $this->_month;	
		}

		public function getMonthName() {
			return $this->format('F');
		}

		public function getMonthAbbr() {
			return $this->format('M');
		}

		public function getDay($leadingZero = false) {
			return $leadingZero ? $this->format('d') : $this->_day;
		}

		public function getDayOrdinal() {
			return $this->format('jS');
		}

		public function getDayName() {
			return $this->format('l');	
		}

		public function getDayAbbr() {
			return $this->format('D');
		}

		public function addDays($numDays) {
			if(!is_numeric($numDays) || $numDays < 1) {
				throw new Exception('method addDays() expects a positive integer as argument.');
			}
			parent::modify('+' . intval($numDays) . ' days');
		}

		public function subDays($numDays) {
			if(!is_numeric($numDays)) {
				throw new Exception('method subDays() expects an integer as argument.');
			}
			parent::modify('-' . abs(intval($numDays)) . ' days');
		}

		public function addWeeks($numWeeks) {
			if(!is_numeric($numWeeks) || $numWeeks < 1) {
				throw new Exception('method addWeeks() expects a positive integer as argument.');
			}
			parent::modify('+' . intval($numWeeks) . ' weeks');
		}

		public function subWeeks($numWeeks) {
			if(!is_numeric($numWeeks)) {
				throw new Exception('method subWeeks() expects an integer as argument.');
			}
			parent::modify('-' . abs(intval($numWeeks)) . ' days');
		}

		public function addMonths($numMonths) {
			if(!is_numeric($numMonths) || $numMonths < 1) {
				throw new Exception('Pos_Date method "addMonths()" expects a positive integer.');
			}
			$numMonths = (int) $numMonths;
			// Add the months to the current month number.
			$newValue = $this->_month + $numMonths;
			// if new value is less than or equal to 12, the year doesn't change. assign new value to month
			if($newValue <= 12) {
				$this->_month = $newValue;
			}
			else {
				// a new value greater than 12 means calculating for both new month and new year. calculating the year is different for december so use modulo division by 12 on the new value. if the remainder is not 0, the new month is not december.
				$notDecember = $newValue % 12;
				if($notDecember) {
					// the remainder of the modulo division is the new month.
					$this->_month = $notDecember;
					// divide the new value by 12 and round down to get the number of years to add.
					$this->_year += floor($newValue / 12);
				}
				else {
					// the new month must be december
					$this->_month = 12;
					$this->_year == ($newValue / 12) -1;
				}				
			}
			$this->checkLastDayOfMonth();
			parent::setDate($this->_year, $this->_month, $this->_day);		
		}


		public function subMonths($numMonths) {
			if(!is_numeric($numMonths)) {
				throw new Exception('Pos_Date method "subMonths()" expects an integer.');
			}
			else {
				$numMonths = abs(intval($numMonths));
				// Subtract numMonths from current month number
				$newValue = $this->_month - $numMonths;
				// if the result is greater than 0, it's still the same year and you can assign the new value to the month
				if($newValue > 0) {
					$this->_month = $newValue;
				}
				else {
					// create array of months in reverse
					$months = range(12 , 1);
					// get absolute value of newValue
					$newValue = abs($newValue);
					// get the array position of the resulting month
					$monthPosition = $newValue % 12;
					$this->_month = $months[$monthPosition];
					// array begins at 0, so if monthPosition is 0 it must be december
					if($monthPosition) {
						$this->_year -= ceil($newValue / 12);
					}
					else {
						$this0->year -= ceil($newValue / 12) +1;
					}
				}
			}
			$this->checkLastDayOfMonth();
			parent::setDate($this->_year, $this->_month, $this->_day);
		}


		public function addYears($numYears) {
			if(!is_numeric($numYears) || $numYears < 1) {
				throw new Exception('Pos_Date method "addYears()" expects a positive integer.');
			}
			$this->_year += (int) $numYears;
			$this->checkLastDayOfMonth();
			parent::setDate($this->_year, $this->_month, $this->_day);
		}

		public function subYears($numYears) {
			if(!is_numeric($numYears)) {
				throw new Exception('Pos_Date method "subYears()" expects a positive integer.');
			}
			$this->_year += abs(intval($numYears));
			$this->checkLastDayOfMonth();
			parent::setDate($this->_year, $this->_month, $this->_day);			

		}

		final protected function checkLastDayOfMonth() {
			if(!checkdate($this->_month, $this->_day, $this->_year)) {
				$use30 = array(4 , 6 , 9 , 11);
				if(in_array($this->_month, $use30)) {
					$this->_day = 30;
				}
				else {
					$this->_day = $this->isLeap() ? 29 : 28;
				}				
			}
		}

		public function isLeap() {
			if($this->_year % 400 == 0 || ($this->_year % 4 == 0 && $this->_year % 100 !=0)) {
				return true;
			}
			else {
				return false;
			}
		}
		
		static public function dateDiff(Pos_Date $startDate, Pos_Date $endDate) {
			$start = gmmktime(0, 0, 0, $startDate->_month, $startDate->_day, $startDate->_year);
			$end   = gmmktime(0, 0, 0, $endDate->_month, $endDate->_day, $endDate->_year);
			return ($end - $start) / (60 * 60 * 24);

		}

		public function  __toString() {
			return $this->format('l, F jS, Y');
		}

		public function __get($dateFormat) {
			switch(strtolower($dateFormat)) {
				case 'mdy':
					return $this->format('n/j/Y');
				case 'mdy0':
					return $this->format('m/d/Y');
				case 'dmy':
					return $this->format('j/n/Y');
				case 'dmy0':
					return $this->format('d/m/Y');
				case 'mysql':
				case 'yyyy-mm-dd':
				case 'Y-m-d':
					return $this->format('Y-m-d');
				case 'fullyear':
					return $this->_year;
				case 'year':
					return $this->format('y');
				case 'month':
					return $this->_month;
				case 'month0':
					return $this->format('m');
				case 'monthname':
					return $this->format('F');
				case 'monthabbr':
					return $this->format('M');
				case 'day':
					return $this->_day;
				case 'day0':
					return $this->format('d');
				case 'dayordinal':
					return $this->format('jS');
				case 'dayname':
					return $this->format('l');
				case 'dayabbr':
					return $this->format('D');
				case 'time':
					return $this->format('H:i:s');
				default:
					return 'Invalid property';
			}
		}
	}

?>