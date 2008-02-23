<?php // BUILD: Remove line

class SG_iCal_Factory {
	public static function factory( SG_iCalReader $ical, $section, $data ) {
		switch( $section ) {
			case "vcalendar":
				require_once dirname(__FILE__).'/../blocks/SG_iCal_VCalendar.php'; // BUILD: Remove line
				return new SG_iCal_VCalendar(SG_iCal_Line::Remove_Line($data), $ical );
			case "vtimezone":
				require_once dirname(__FILE__).'/../blocks/SG_iCal_VTimeZone.php'; // BUILD: Remove line
				return new SG_iCal_VTimeZone(SG_iCal_Line::Remove_Line($data), $ical );
			case "vevent":
				require_once dirname(__FILE__).'/../blocks/SG_iCal_VEvent.php'; // BUILD: Remove line
				return new SG_iCal_VEvent($data, $ical );
			
			default:
				return new ArrayObject(SG_iCal_Line::Remove_Line((array) $data) );
		}
	}
}
