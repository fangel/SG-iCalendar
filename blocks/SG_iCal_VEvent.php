<?php // BUILD: Remove line

/**
 * The wrapper for vevents. Will reveal a unified and simple api for 
 * the events, which include always finding a start and end (except
 * when no end or duration is given) and checking if the event is 
 * blocking or similar.
 *
 * Will apply the specified timezone to timestamps if a tzid is 
 * specified
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_VEvent {
	private $uid;
	private $start;
	private $end;
	private $summary;
	private $description;
	private $location;
	private $data;
	
	/**
	 * Constructs a new SG_iCal_VEvent. Needs the SG_iCalReader 
	 * supplied so it can query for timezones.
	 * @param SG_iCal_Line[] $data
	 * @param SG_iCalReader $ical
	 */
	public function __construct($data, SG_iCalReader $ical ) {
		$this->uid = $data['uid']->getData();
		unset($data['uid']);
		
		if( isset($data['dtstart']) ) {
			$this->start = $this->getTimestamp( $data['dtstart'] );
			unset($data['dtstart']);
		}
		
		if( isset($data['dtend']) ) {
			$this->end = $this->getTimestamp($data['dtend']);
			unset($data['dtend']);
		} elseif( isset($data['duration']) ) {
			require_once dirname(__FILE__).'/../helpers/SG_iCal_Duration.php'; // BUILD: Remove line
			$dur = new SG_iCal_Duration( $data['duration']->getData() );
			$this->end = $this->start + $dur->getDuration();
			unset($data['duration']);
		}

		$imports = array('summary','description','location');
		foreach( $imports AS $import ) {
			if( isset($data[$import]) ) {
				$this->$import = $data[$import]->getData();
				unset($data[$import]);
			}
		}
		
		$this->data = SG_iCal_Line::Remove_Line($data);
	}
	
	/**
	 * Returns the UID of the event
	 * @return string
	 */
	public function getUID() {
		return $this->uid;
	}
	
	/**
	 * Returns the summary (or null if none is given) of the event
	 * @return string
	 */
	public function getSummary() {
		return $this->summary;
	}
	
	/**
	 * Returns the description (or null if none is given) of the event
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Returns true if the event is blocking (ie not transparent)
	 * @return bool
	 */
	public function isBlocking() {
		return !(isset($this->data['transp']) && $this->data['transp'] == 'TRANSPARENT');
	}
	
	/**
	 * Returns true if the event is confirmed
	 * @return bool
	 */
	public function isConfirmed() {
		return (isset($this->data['status']) && $this->data['status'] == 'CONFIRMED');
	}
	
	/**
	 * Returns the timestamp for the beginning of the event
	 * @return int
	 */
	public function getStart() {
		return $this->start;
	}
	
	/**
	 * Returns the timestamp for the end of the event
	 * @return int
	 */
	public function getEnd() {
		return $this->end;
	}
	
	/**
	 * Calculates the timestamp from a DT line.
	 * @param $line SG_iCal_Line
	 * @return int
	 */
	private function getTimestamp( SG_iCal_Line $line ) {
		$ts = strtotime($line->getData());
		if( isset($data['dtend']['tzid']) ) {
			$tz = $ical->getTimeZoneInfo($line['tzid']);
			$offset = $tz->getOffset($ts);
			$ts = strtotime(gmdate('D, d M Y H:i:s', $ts) . ' ' . $offset);
		}
		return $ts;
	}
}
