<?php

define('SG_ICALREADER_VERSION', '0.2');

/**
 * A simple iCalReader. Won't handle all the different fancy-smancy 
 * stuff. What it will do is to parse the END/BEGIN-structures into 
 * a easy to parse/read php-array.
 *
 * Roadmap:
 *  * Do a proper parsing of events to make a single api to query for event start/stop, alarms etc
 *  * Parse timezones and have them affect timestamps
 * 
 * A simple example:
 * <?php
 * $ical = new SG_iCalReader("http://example.com/calendar.ics");
 * foreach( $ical->getEvents() As $event ) {
 *   // Do stuff with the event $event
 * }
 * ?>
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCalReader {
	private $url;
	private $parsed;
	private $is_utf8 = true;
	
	private $parsed_data = array();

	/**
	 * Constructs a new iCalReader. You can supply the url now, or later using setUrl
	 * @param $url string
	 */
	public function __construct($url = '') {
		$this->url = $url;
		$this->parsed = false;

		require_once dirname(__FILE__) . '/helpers/SG_iCal_Factory.php'; // BUILD: Remove line
		require_once dirname(__FILE__) . '/helpers/SG_iCal_Line.php'; // BUILD: Remove line
	}

	/**
	 * Sets (or resets) the url this reader reads from. If the given
	 * url is empty or the same as the existing it will be ignored.
	 * Otherwise the url will change and the status of the parser will
	 * be reset.
	 *
	 * If you need to time a few passes over parse(), a quick way to
	 * make sure _parse() will get called is to set the url again
	 * and setting $force to true to reset the url even though it even
	 * though the url hasn't changed.
	 *
	 * @param $url string
	 * @param $force bool
	 */
	public function setUrl( $url = '', $force = false ) {
		if( trim($url) != '' && ($url != $this->url || $force) ) {
			$this->url = $url;
			$this->parsed = false;
			$this->is_utf8 = true;
		}
	}
	
	/**
	 * Returns the main calendar info. You can then query the returned
	 * object with ie getTitle(). 
	 *
	 * @return SG_iCal_VCalendar
	 */
	public function getCalendarInfo() {
		$this->ensureParsed();
		return $this->parsed_data['vcalendar'];
	}
	
	/**
	 * Returns a given timezone for the calendar. This is mainly used
	 * by VEvents to adjust their date-times if they have specified a
	 * timezone.
	 *
	 * If no timezone is given, all timezones in the calendar is
	 * returned.
	 *
	 * @param $tzid string
	 * @return SG_iCal_VTimeZone
	 */
	public function getTimeZoneInfo( $tzid = null ) {
		$this->ensureParsed();
		if( $tzid == null ) {
			return $this->parsed_data['vtimezone'];
		} else {
			foreach( $this->parsed_data['vtimezone'] AS $tz ) {
				if( $tz->getTimeZoneId() == $tzid ) {
					return $tz;
				}
			}
			return null;
		}
	}
	
	/**
	 * Returns the events found
	 * @return array
	 */
	public function getEvents() {
		$this->ensureParsed();
		return $this->parsed_data['vevent'];
	}
	
	/**
	 * Makes sure the iCal file gets fetched, decoded, unfolded and
	 * parsed.
	 *
	 * Note: It's not neccesary to call parse yourself. If you query 
	 * for some of the data, the file will be parsed.
	 * It can't hurt, as a already parsed feed ain't parsed again..
	 *
	 */
	public function parse() {
		if( !$this->parsed ) {
			$content = $this->fetch();
			if( !$this->is_utf8 ) {
				$content = utf8_encode($content);
			}
			$content = $this->unfold_lines($content);
		
			$this->_parse( $content );		
			$this->parsed = true;
		}
	}
	
	// The rest are private function
	
	/**
	 * Ensures that the current url is parsed in the parsed_data member
	 * @throws Exception
	 * @ensure The feed located at $url is parsed and stored in $parsed_data
	 */
	private function ensureParsed() {
		if( !$this->parsed ) {
			if( $this->url == '' ) {
				throw new Exception('No url given to iCalReader to parse');
			}
			$this->parse();
		}
		
		if( empty($this->parsed_data) ) {
			throw new Exception('Invalid iCal file or parse failure!');
		}
	}
	
	/**
	 * Fetches url and stores it in the content member
	 * @return string
	 */
	protected function fetch() {
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		$content = curl_exec($c);
		
		$ct = curl_getinfo($c, CURLINFO_CONTENT_TYPE);
		$enc = preg_replace('/^.*charset=([-a-zA-Z0-9]+).*$/', '$1', $ct);
		if( $ct != '' && strtolower(str_replace('-','', $enc)) != 'utf8' ) {
			// Well, the encoding says it ain't utf-8
			$this->is_utf8 = false;
		} elseif( !$this->_valid_utf8( $content ) ) {
			// The data isn't utf-8
			$this->is_utf8 = false;
		}
		
		return $content;
	}
	
	/**
	 * Takes the string $content, and creates a array of iCal lines. 
	 * This includes unfolding multi-line entries into a single line.
	 * @param $content string
	 */
	private function unfold_lines($content) {
		$data = array();
		$content = explode("\n", $content);
		for( $i=0; $i < count($content); $i++) {
			$line = rtrim($content[$i]);
			while( isset($content[$i+1]) && strlen($content[$i+1]) > 0 && ($content[$i+1]{0} == ' ' || $content[$i+1]{0} == "\t" )) {
				$line .= rtrim(substr($content[++$i],1));
			}
			$data[] = $line;
		}
		return $data;
	}
		
	/**
	 * Parses the feed found in content and calls storeSection to store
	 * parsed data
	 */
	private function _parse( $content ) {
		$main_sections = array('vevent', 'vjournal', 'vtodo', 'vtimezone', 'vcalendar');
		$sections = array();
		$section = '';
		$current_data = array();
		
		foreach( $content AS $line ) {
			$line = new SG_iCal_Line($line);			
			if( $line->isBegin() ) {
				// New block of data, $section = new block
				$section = strtolower($line);
				$sections[] = strtolower($line);
			} elseif( $line->isEnd() ) {
				// End of block of data ($removed = just ended block, $section = new top-block)
				$removed = array_pop($sections);
				$section = end($sections);

				if( array_search($removed, $main_sections) !== false ) {
					$this->storeSection( $removed, $current_data[$removed]);
					$current_data[$removed] = array();
				}
			} else {
				// Data line
				foreach( $main_sections AS $s ) {
					// Loops though the main sections
					
					if( array_search($s, $sections) !== false ) {
						// This section is in the main section
						if( $section == $s ) {
							// It _is_ the main section
							$current_data[$s][$line->getIdent()] = $line; 
						} else {
							// Sub section
							$current_data[$s][$section][$line->getIdent()] = $line; 
						}
						break;
					}
				}
			}
		}
		$current_data = array();
	}
	
	/**
	 * Stores the data in the parsed_data member
	 */
	private function storeSection( $section, $data ) {
		$data = SG_iCal_Factory::factory($this, $section, $data);
		if( $section == 'vcalendar' ) {
			// We don't want to do parsed_data['vcalendar'][0] to get the data, so it's special..
			$this->parsed_data['vcalendar'] = $data;
		} else {
			$this->parsed_data[$section][] = $data;
		}
	}
	
	/**
	 * This functions does some regexp checking to see if the value is 
	 * valid UTF-8.
	 *
	 * The function is from the book "Building Scalable Web Sites" by 
	 * Cal Henderson.
	 *
	 * @return bool
	 */
	private function _valid_utf8( $data ) {
		$rx  = '[\xC0-\xDF]([^\x80-\xBF]|$)';
		$rx .= '|[\xE0-\xEF].{0,1}([^\x80-\xBF]|$)';
		$rx .= '|[\xF0-\xF7].{0,2}([^\x80-\xBF]|$)';
		$rx .= '|[\xF8-\xFB].{0,3}([^\x80-\xBF]|$)';
		$rx .= '|[\xFC-\xFD].{0,4}([^\x80-\xBF]|$)';
		$rx .= '|[\xFE-\xFE].{0,5}([^\x80-\xBF]|$)';
		$rx .= '|[\x00-\x7F][\x80-\xBF]';
		$rx .= '|[\xC0-\xDF].[\x80-\xBF]';
		$rx .= '|[\xE0-\xEF]..[\x80-\xBF]';
		$rx .= '|[\xF0-\xF7]...[\x80-\xBF]';
		$rx .= '|[\xF8-\xFB]....[\x80-\xBF]';
		$rx .= '|[\xFC-\xFD].....[\x80-\xBF]';
		$rx .= '|[\xFE-\xFE]......[\x80-\xBF]';
		$rx .= '|^[\x80-\xBF]';

		return ( ! (bool) preg_match('!'.$rx.'!', $data) );
	}
}
