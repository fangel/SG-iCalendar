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
	 * Note that the events returned needs only slightly overlap.
	 *
	 * @param SG_iCalReader|array $ical The calendar to query
	 * @param int $start
	 * @param int $end
	 * @return SG_iCal_VEvent[]
	 */
	public static function Between( $ical, $start, $end ) {
		if( $ical instanceof SG_iCalReader ) {
			$evs = $ical->getEvents();
		}
		if( !is_array($evs) ) {
			throw new Exception('SG_iCal_Query::Between called with invalid input!');
		}
		
		$rtn = array();
		foreach( $evs AS $e ) {
			if( ($start <= $e->getStart() && $e->getStart() < $end)
			 || ($start < $e->getEnd() && $e->getEnd() <= $end) ) {
				$rtn[] = $e;
			}
		}
		return $rtn;
	}
	
	/**
	 * Returns all events from the calendar after a given timestamp
	 * 
	 * @param SG_iCalReader|array $ical The calendar to query
	 * @param int $start
	 * @return SG_iCal_VEvent[]
	 */
	public static function After( $ical, $start ) {
		if( $ical instanceof SG_iCalReader ) {
			$evs = $ical->getEvents();
		}
		if( !is_array($ical) ) {
			throw new Exception('SG_iCal_Query::After called with invalid input!');
		}
		
		$rtn = array();
		foreach( $ical AS $e ) {
			if( $start <= $e->getStart() ) {
				$rtn[] = $e;
			}
		}
		return $rtn;
	}
	
	public static function Sort( $ical, $column ) {
		if( $ical instanceof SG_iCalReader ) {
			$evs = $ical->getEvents();
		}
		if( !is_array($ical) ) {
			throw new Exception('SG_iCal_Query::Sort called with invalid input!');
		}
		
		$cmp = create_function('$a, $b', 'return strcmp($a->getProperty("' . $column . '"), $b->getProperty("' . $column . '"));');
		usort($ical, $cmp);
		return $ical;
	}
}

