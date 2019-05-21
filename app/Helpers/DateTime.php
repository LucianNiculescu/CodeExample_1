<?php

	namespace App\Helpers;

	use Carbon\Carbon;

	/**
	 * Class DateTime
	 * 3 public static methods, short, medium and long
	 * These will each return a string of the date.
	 * 
	 * All have vars for
	 * 		passing in a MySQL timestamp as UTC like 2016-06-14 08:50:55, Null will give UTC time now
	 * 		the local to output like 'en', 'fr', etc. (default en-gb) - 5 digit or 2 digit works fine 
	 * 		time flag (true/false) to append the time to the output
	 * 		time24 flag (true/false) to show the time in 24 hour format (default true)
	 * 		lang - Default to en-gb
	 * 
	 * Use like - \App\Helpers\DateTime::long(null, true, false, 'Europe/London')
	 * 
	 * NB. See http://php.net/manual/en/function.strftime.php for the format
	 * @package App\Admin\Helpers
	 */
	class DateTime {

		//private static $shortDateFormat = 'd M y';
		private static $shortDateFormat = '%e %b %y';
		//private static $midDateFormat = 'D dS M Y';
		private static $midDateFormat = '%a %e %b %Y';
		//private static $longDateFormat = 'l dS F Y';
		private static $longDateFormat = '%A %e %B %Y';

		private static $ddmmyyFormat = 'd/m/y';

		//private static $timeFormat24 = 'H:i';
		private static $timeFormat24 = '%k:%M';
		//private static $timeFormat = 'g:i A';
		private static $timeFormat = '%H:%M %p';

		private static $carbon = null;

        /**
         * Get the current timestamp
         *
         * @return Carbon
         */
		public static function now()
        {
            return Carbon::now();
        }

		/**
		 * Short date/time
		 * See class desc
		 * @param null $UTCDateTime
		 * @param bool $time
		 * @param bool $hour24
		 * @param string $timeZone
		 * @param string $lang
		 * @return string
		 */
		public static function short( $UTCDateTime=null, $time=false, $hour24=true, $timeZone='UTC', $lang='en-gb' )
		{
			// Create the format to return in
			$format = self::$shortDateFormat .self::getTimeFormat($time, $hour24);

			return self::getDateTimeString($UTCDateTime, $timeZone, $format, $lang);
		}

		/**
		 * ddmmyy is converting date to dd/mm/yy
		 * See class desc
		 * @param null $UTCDateTime
		 * @return string
		 */
		public static function ddmmyyToMedium( $UTCDateTime=null, $time=false, $hour24=true, $timeZone='UTC', $lang='en-gb' )
		{
			// Create the format to return in
			$format = self::$ddmmyyFormat;
			if($UTCDateTime != '')
				return self::medium(Carbon::createFromFormat('d/m/y', $UTCDateTime));
			else
				return $UTCDateTime;
			//return self::getDateTimeString($UTCDateTime, $timeZone, $format, $lang);
		}

		/**
		 * Medium date/time
		 * See class desc
		 * @param null $UTCDateTime
		 * @param bool $time
		 * @param bool $hour24
		 * @param string $timeZone
		 * @param string $lang
		 * @return string
		 */
		public static function medium( $UTCDateTime=null, $time=false, $hour24=true, $timeZone='UTC', $lang='en-gb' )
		{
			// Create the format to return in
			$format = self::$midDateFormat .self::getTimeFormat($time, $hour24);

			return self::getDateTimeString($UTCDateTime, $timeZone, $format, $lang);
		}

		
		/**
		 * Long date/time
		 * See class desc
		 * @param null $UTCDateTime
		 * @param bool $time
		 * @param bool $hour24
		 * @param string $timeZone
		 * @param string $lang
		 * @return string
		 */
		public static function long( $UTCDateTime=null, $time=false, $hour24=true, $timeZone='UTC', $lang='en-gb' )
		{
			// Create the format to return in
			$format = self::$longDateFormat .self::getTimeFormat($time, $hour24);

			return self::getDateTimeString($UTCDateTime, $timeZone, $format, $lang);
		}


		/**
		 * Make sure we have the correct time format
		 * @param bool $time
		 * @param bool $hour24
		 * @return string
		 */
		private static function getTimeFormat($time=false, $hour24=true)
		{
			$timeFormat = '';
			if($time)
			{
				if($hour24)
					$timeFormat = ' ' .self::$timeFormat24;
				else
					$timeFormat = ' ' .self::$timeFormat;
			}

			return $timeFormat;
		}


		/**
		 * Set the local
		 * @param $lang
		 */
		private static function setLocal($lang)
		{
			// If the lang is only 2 chars, make it into the 5 chars
			if( strlen ($lang) == 2 )
				$lang = $lang .'_' . strtoupper($lang);

			if( is_null(self::$carbon) )
				self::$carbon = new Carbon();

			// Get the first 2 chars of the string
			$carbonLang = substr($lang,0,2);

			setlocale(LC_TIME, $lang); // de_DE
			self::$carbon->setLocale($carbonLang); // de
		}

		/**
		 * @param $UTCDateTime
		 * @param $timeZone
		 * @param $format
		 * @param string $lang
		 * @return string
		 */
		private static function getDateTimeString($UTCDateTime, $timeZone, $format, $lang='en-gb')
		{
			// Create a new carbon
			self::$carbon = new Carbon($UTCDateTime, $timeZone);

			// Set the local (using carbon)
			self::setLocal($lang);

			// Return the localised string
			return self::$carbon->formatLocalized($format);
		}

        /**
         * Timezones list with GMT offset
         *
         * @return array
         */
        public static function getTimeZones() {
            $zones_array = array();
            $timestamp = time();
            foreach(timezone_identifiers_list() as $key => $zone) {
                date_default_timezone_set($zone);

				$zone = explode('/', $zone); // 0 => Continent, 1 => City

				if ( $zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' ||
					$zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' ||
					$zone[0] == 'Indian' || $zone[0] == 'Pacific' )
				{
					if( isset($zone[1]) != '' )
					{
						$zones_array[$zone[0]][$zone[0] . '/' . $zone[1]] = $zone[1]. ' (GMT ' . date('P', $timestamp).')';
					}
				}
			}
            return $zones_array;
        }


		/**
		 * Changes weeks to seconds
		 * @param $weeks
		 * @return int
		 */
		public static function weeks2seconds($weeks)
		{
			if(is_numeric($weeks)) {
				return 604800*$weeks;
			}

			return $weeks;
		}

		/**
		 * Changes days to seconds
		 * @param $days
		 * @return int
		 */
		public static function days2seconds($days)
		{
			if(is_numeric($days)) {
				return 86400*$days;
			}

			return $days;
		}

		/**
		 * Changes hours to seconds
		 * @param $hours
		 * @return int
		 */
		public static function hours2seconds($hours)
		{
			if(is_numeric($hours)) {
				return 3600*$hours;
			}

			return $hours;
		}

		/**
		 * Changes minutes to seconds
		 * @param $minutes
		 * @return int
		 */
		public static function minutes2seconds($minutes)
		{
			if(is_numeric($minutes)) {
				return 60*$minutes;
			}

			return $minutes;
		}




		/**
		 * Convert number of seconds into years, days, hours, minutes and seconds
		 * and return an string containing those values
		 *
		 * @param integer $seconds Number of seconds to parse
		 * @return string
		 */
		public static function seconds2readable($seconds)
		{
			// Converting given Secounds to integer
			$seconds = intval ($seconds);
			$y = floor($seconds / (86400*365.25));
			$w = floor(($seconds - ($y*(86400*365.25))) / 604800);
			$d = floor(($seconds - ($w*(86400*7))) / 86400);
			$h = gmdate('H', $seconds);
			$m = gmdate('i', $seconds);
			$s = gmdate('s', $seconds);

			$string = '';

			if($y > 0) {
				$yw = $y > 1 ? trans('admin.years') : trans('admin.year');
				$string .= $y .' ' . $yw .' ';
			}

			if($w > 0) {
				$ww = $w > 1 ? trans('admin.weeks') : trans('admin.week');
				$string .= $w .' ' . $ww .' ';
			}

			if($d > 0) {
				$dw = $d > 1 ? trans('admin.days') : trans('admin.day');
				$string .= $d .' ' . $dw .' ';
			}

			if($h > 0) {
				$hw = $h > 1 ? trans('admin.hours') : trans('admin.hour');
				$string .= $h .' ' . $hw .' ';
			}

			if($m > 0) {
				$mw = $m > 1 ? trans('admin.minutes') : trans('admin.minute');
				$string .= $m .' ' . $mw .' ';
			}

			if($s > 0) {
				$sw = $s > 1 ? trans('admin.seconds') : trans('admin.second');
				$string .= $s .' ' . $sw .' ';
			}

			return preg_replace('/\s+/',' ',$string);
		}


		/**
		 * Convert number of milliseconds into years, days, hours, minutes and seconds
		 * and return an string containing those values
		 * @param $milliSeconds
		 * @return string
		 */
		public static function milliSeconds2readable($milliSeconds)
		{
			if($milliSeconds < 1000)
				return intval($milliSeconds) / 1000 . ' ' . trans('admin.seconds');
			else
				return self::seconds2readable(intval($milliSeconds) / 1000);
		}

		/**
		 * This converts the text time like 2w2h4h5m49s to seconds
		 * @param $textTime
		 * @return int
		 */
		public static function convertTextTimeToSeconds($textTime)
		{
			$result = 0;

			// Exploading $textTime to see if there are weeks or not
			$data = explode('w', $textTime);

			if(sizeof($data) == 2)
			{
				$result +=  self::weeks2seconds($data[0]);
				$textTime = $data[1];
			}

			// Exploading $textTime to see if there are days or not
			$data = explode('d', $textTime);

			if(sizeof($data) == 2)
			{
				$result +=  self::days2seconds($data[0]);
				$textTime = $data[1];
			}

			// Exploading $textTime to see if there are hours or not
			$data = explode('h', $textTime);

			if(sizeof($data) == 2)
			{
				$result +=  self::hours2seconds($data[0]);
				$textTime = $data[1];
			}

			// Exploading $textTime to see if there are minutes or not
			$data = explode('m', $textTime);

			if(sizeof($data) == 2)
			{
				$result +=  self::minutes2seconds($data[0]);
				$textTime = $data[1];
			}

			// Exploading $textTime to see if there are secounds or not
			$data = explode('s', $textTime);

			if(sizeof($data) == 2)
			{
				$result += $data[0];
			}

			return $result;
		}
	}