<?php

define('SG_ICALREADER_VERSION', '0.5');

/**
 * A simple iCal parser. Should take care of most stuff for ya
 *
 * Roadmap:
 *  * Finish FREQUENCY-parsing.
 *  * Add API for recurring events
 * 
 * A simple example:
 * <?php
 * $ical = new SG_iCal("http://example.com/calendar.ics");
 * foreach( $ical->getEvents() As $event ) {
 *   // Do stuff with the event $event
 * }
 * ?>
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal {
	private $information;
	private $events;
	private $timezones;

	/**
	 * Constructs a new iCalReader. You can supply the url now, or later using setUrl
	 * @param $url string
	 */
	public function __construct($url = false) {
		require_once dirname(__FILE__) . '/helpers/SG_iCal_Factory.php'; // BUILD: Remove line
		require_once dirname(__FILE__) . '/helpers/SG_iCal_Line.php'; // BUILD: Remove line
		require_once dirname(__FILE__) . '/helpers/SG_iCal_Query.php'; // BUILD: Remove line
		require_once dirname(__FILE__) . '/helpers/SG_iCal_Parser.php'; // BUILD: Remove line

		if( $url !== false ) {
			SG_iCal_Parser::Parse($url, $this);
		}
	}

	/**
	 * Sets (or resets) the url this reader reads from.
	 * @param $url string
	 */
	public function setUrl( $url = false ) {
		if( $url !== false ) {
			SG_iCal_Parser::Parse($url, $this);
		}
	}
	
	/**
	 * Returns the main calendar info. You can then query the returned
	 * object with ie getTitle(). 
	 * @return SG_iCal_VCalendar
	 */
	public function getCalendarInfo() {
		return $this->information;
	}
	
	/**
	 * Sets the calendar info for this calendar
	 * @param SG_iCal_VCalendar $info
	 */
	public function setCalendarInfo( SG_iCal_VCalendar $info ) {
		$this->information = $info;
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
		if( $tzid == null ) {
			return $this->timezones;
		} else {
			if ( !isset($this->timezones)) {
				return null;
			}
			foreach( $this->timezones AS $tz ) {
				if( $tz->getTimeZoneId() == $tzid ) {
					return $tz;
				}
			}
			return null;
		}
	}
	
	/**
	 * Adds a new timezone to this calendar
	 * @param SG_iCal_VTimeZone $tz
	 */
	public function addTimeZone( SG_iCal_VTimeZone $tz ) {
		$this->timezones[] = $tz;
	}
	
	/**
	 * Returns the events found
	 * @return array
	 */
	public function getEvents() {
		return $this->events;
	}

	/**
	 * Adds a event to this calendar
	 * @param SG_iCal_VEvent $event
	 */
	public function addEvent( SG_iCal_VEvent $event ) {
		$this->events[] = $event;
	}
}

/**
 * For legacy reasons, we keep the name SG_iCalReader..
 */
class SG_iCalReader extends SG_iCal {}
