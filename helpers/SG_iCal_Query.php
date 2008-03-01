<?php // BUILD: Remove line

/**
 * A collection of functions to query the events in a calendar.
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_Query {
	/**
	 * Returns all events from the calendar between two timestamps
	 * 
	 * @param SG_iCalReader $ical The calendar to query
	 * @param int $start
	 * @param int $end
	 * @return SG_iCal_VEvent[]
	 */
	public static function Between( SG_iCalReader $ical, $start, $end ) {
		$evs = $ical->getEvents();
		$rtn = array();
		foreach( $evs AS $e ) {
			if( ($start < $e->getStart() && $e->getStart() < $end)
			 || ($start < $e->getEnd() && $e->getEnd() < $end) ) {
				$rtn[] = $e;
			}
		}
		return $rtn;
	}
}

