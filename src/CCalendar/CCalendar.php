<?php

// namespace Mattias\Calendar;

class CCalendar {
	
	/* 
	* Members!!
	*/ 
	private $year;
	private $month;
	private $days;
	private $validMonths = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
	
	/**
	 * Methods!! ---
	 *
	 * Constructor to set up the class.
	 *
	 */
	public function __construct() {
		$this->year = isset($_GET['year']) ? $_GET['year'] : date('Y');
		$this->month = isset($_GET['month']) ? $_GET['month'] : date('m');
		if(!in_array($this->month, $this->validMonths)) $this->errorMessage("Have you ever heard of month {$this->month}? Try a valid month (1-12).");
		$this->days = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
	}

	/**
	 *
	 * Display error message in a paragraph, then exit. 
	 * @return void.
	 */	
	private function errorMessage($err) {
		echo "<p>$err</p>";
		exit;
	} 

	/**
	 *
	 * Function to return string of month and year. 
	 * @return string of month and year. 
	 */	
	public function getMonthAndYear() {
		return $this->getMonthName() . ", " . $this->year;
	}

	/**
	 *
	 * Function to display the year member.
	 * @return integer $year.
	 */	
	public function getYear() {
		return $this->year;
	}
	/**
	 *
	 * Function to return the name of the month, in swedish. 
	 * @return string monthname. 
	 */	
	public function getMonthName($month = null) {
		if(is_null($month)) {
			$month = $this->month;
		}
		switch($month) {
			case 1: 
				return 'Januari';
			case 2: 
				return 'Februari'; 
			case 3: 
				return 'Mars';
			case 4: 
				return 'April'; 
			case 5:
				return 'Maj';
			case 6:
				return 'Juni';
			case 7: 
				return 'Juli';
			case 8: 
				return 'Augusti';
			case 9:
				return 'September';
			case 10:
				return 'Oktober';
			case 11: 
				return 'November';
			case 12: 
				return 'December';
		}
	}
	/**
	 *
	 * Function to return the starting day of the table.
	 * @return integer representing the weekday the actual month is starting on. For example the first is on a wednesday = return 3. 
	 */	
	private function getStartingDayNumber() {
		$timestamp = mktime(0, 0, 0, $this->month, 1, $this->year);
		return date('N', $timestamp);
	}
	/**
	 *
	 * Function to return the amount of days in a given month. 
	 * @return integer days in month. 
	 */
	private function getDaysInMonth($month) {
		return cal_days_in_month(CAL_GREGORIAN, $month, $this->year);
	}
	
	/**
	 *
	 * Function to return a date from year, month and date. 
	 * @return date in format Y-m-d (2015-04-22)
	 */
	private function returnDate($year, $month, $date) {
		return date('Y-m-d', mktime(0,0,0,$month, $date, $year));
	}
	
	/**
	 *
	 * Helper function to the getNavigation function. 
	 * @return string http query
	 */
	private function getQueryString($options, $prepend='?') {
		$query = array();
		parse_str($_SERVER['QUERY_STRING'], $query);
		$query = array_merge($query, $options);
		return $prepend . htmlentities(http_build_query($query));
	}
	
	/**
	 *
	 * Function to return the previous month to a given month. 
	 * @return integer representing the previous month.
	 */
	private function getPreviousMonth($month) {
		if($month == 1) {
			return 12;
		} else {
			return $month - 1;
		}
	}

	/**
	 *
	 * Function to return the next month to a given month.
	 * @return integer representing the next month.
	 */
	private function getNextMonth($month) {
		if($month == 12) {
			return 1;
		} else {
			return $month + 1;
		}
	}
	
	/**
	 *
	 * Generate pagineering to navigate between months. 
	 * @return string html, the navigation table. 
	 */
	public function getNavigation() {
		$yearPrev = $this->month == 1 ? 'year' : null;
		$valPrev = isset($yearPrev) ? $this->year - 1 : null;
		$yearNext = $this->month == 12 ? 'year' : null;
		$valNext = isset($yearNext) ? $this->year + 1 : null;
		$html = "<div class='calendar-nav'><span class='left'><a href='" . self::getQueryString(array('year' => $this->year -1)) . "'>&lt; " . ($this->getYear() - 1) . "</a></span>";
		$html .= "<span class='right'><a href='" . self::getQueryString(array('year' => $this->year +1)) . "'>". ($this->getYear() + 1) . " &gt;</a></span></div>";
		$html .= "<div class='calendar-nav'><span class='left'><a href='" . self::getQueryString(array('month' => $this->getPreviousMonth($this->month), $yearPrev => $valPrev)) . "'>&lt; " . self::getMonthName($this->getPreviousMonth($this->month)) . "</a></span>";
		$html .= "<span class='right'><a href='" . self::getQueryString(array('month' => $this->getNextMonth($this->month), $yearNext => $valNext)) . "'>". self::getMonthName($this->getNextMonth($this->month)). " &gt;</a></span></div>";
		return $html;
	}
	
	/**
	 *
	 * Generate the calendar table.
	 * @return string $html, the calendar table.
	 */
	public function getCalendarTable() {
		$counter = 1;
		$daysInPrevMonth = $this->getDaysInMonth($this->getPreviousMonth($this->month));
		$startDay = ($daysInPrevMonth + 2) - $this->getStartingDayNumber(); 
		$rows = ($this->getDaysInMonth($this->getPreviousMonth($this->month)) - $startDay) + $this->getDaysInMonth($this->month) >= 35 ? 6 : 5;
		$date = $startDay;
		if($startDay != 1) {
			$month = $this->getPreviousMonth($this->month);
		}
		if($startDay != 1 && $month == 12) {
			$year = $this->year - 1;
		} else {
			$year = $this->year;
		}
		$html = "<table class='calendar'>\n";
		$html .= "<tr><th class='week'>Vecka</th><th>Måndag</th><th>Tisdag</th><th>Onsdag</th><th>Torsdag</th><th>Fredag</th><th>Lördag</th><th>Söndag</th></tr>\n";
		
		for($i = 1; $i <= $rows; $i++) { // rader
			$html .= "<tr>\n";
			$week = date('W', mktime(0,0,0,$month, $date, $year));
			$html .= "<td class='week'>{$week}</td>\n";
			for($j = 1; $j <= 7; $j++) { // celler
				$css = null;	
				if($date > $this->getDaysInMonth($month) || ($date > $this->days && isset($reset))) { 
					if($month == 12) {
						$year++;
					}
					$date = 1;
					@$reset++;
					$month = $this->getNextMonth($month); // bra här fy fan. Innan var det bara $month++ vilket ju inte funkar så bra för december. Såklart. 
				}
				$css = (@$reset % 2 == 1) ? $css .= " this-month" : $css;
				$css = (date('Y-m-d') == $this->returnDate($year, $month, $date)) ? $css .= " today" : $css;
				$css = $this->checkWeekend(mktime(0,0,0,$month, $date, $year)) ? $css .= " weekend" : $css;
				$html .= "<td class='{$css}'>$date</td>\n";
				$css = null;
				$date++; 
			}
			$html .= "</tr>\n";
		}
		$html .= "</table>\n";
		return $html;
	}
	
	/**
	 *
	 * Function to check if date at timestamp is a weekend or public holiday in Sweden.
	 *
	 * @param timestamp $timestamp
	 * @return boolean true if is swedish public holiday, false if not.
	 * @todo investigate if could return string of public holiday instead of boolean. / boolean 0 if is not weekend. 
	 */

	private function checkWeekend($timestamp) {
		if(date('N', $timestamp) == 7) {
			return 1;
		} else {
			$date = date('d', $timestamp);
			$month = date('m', $timestamp);
			$year = date('Y', $timestamp);
			switch($month){ // switch case month number... 
				case 1:
					switch($date){
						case 1: 
						case 6: 
							return 1;
							break;
					}
					break;
				case 3: 
					// ändrat till - 1 resp +2. konstigt. 
					if((date('d', easter_date($year)) - 2 == $date) && (date('m', easter_date($year)) == $month)) {
						return 1; 
					} elseif((date('d', easter_date($year)) + 1 == $date) && (date('m', easter_date($year)) == $month)) {
						return 1;
					}
					break;
				case 4: 
					/*
						Previously: 
						date('d', easter_date($year)) - 1 == $date)
						Logic will check if the day BEFORE easter day is weekend. That will generate true for the saturday before the easter day. 
						Annandag Påsk previous logic: date('d', easter_date($year)) + 2 == $date)
						Same problem here. That would imply the tuesday is a weekend. Why did i do this?
					*/
					if((date('d', easter_date($year)) - 2 == $date) && (date('m', easter_date($year)) == $month)) {
						return 1; 
					} elseif((date('d', easter_date($year)) + 1 == $date) && (date('m', easter_date($year)) == $month)) {
						return 1;
					}
					break;
				case 5: 
					switch($date) {
						case 1: 
							return 1;
							break;
						default: 
								$dt = new DateTime(date('Y-m-d', easter_date($this->year)));
								$dt->add(new DateInterval('P5W'));
								if(date('d', strtotime('next thursday', strtotime($dt->format('Y-m-d')))) == $date) {
									return 1;
								}
							break;
					}
					break;
				case 6:
					switch($date) {
						case 6:
							return 1;
							break;
						case in_array($date, range(19, 26)):
							if(date('N', $timestamp) == 6 && in_array($date, range(20, 27))) {
								return 1;
							}
							break;
					}
					break;
				case 10: 
					switch($date) {
						case 31:
						if(date('N', $timestamp) == 6) {
							return 1;
						}
						break;
					}
					break;
				case 11:
					switch($date) {
						case in_array($date, range(1, 6)):
							if(date('N', $timestamp) == 6) {
								return 1;
							}
							break;
					}
					break;
				case 12: 
					switch($date) {
						case 25:
						case 26:
							return 1;
						break;
					}
					break;
				default: 
					return 0;
				break;
			}
		}
	}
}