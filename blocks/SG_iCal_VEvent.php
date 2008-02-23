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
 * @license Creative Commons: Attribution-Share Alike 2.5 Denmark (http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB)
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
		
		$this->start = strtotime($data['dtstart']->getData());
		unset($data['dtstart']);
		if( isset($data['dtstart']['tzid']) ) {
			echo 'Start has TZ' . "\n";
			$ts = $ical->getTimeZoneInfo($data['dtstart']['tzid']);
			$offset = $ts->getOffset($this->start);
			$this->start = strtotime(gmdate('D, d M Y H:i:s', $this->start) . ' ' . $offset);
		}

		$this->end = strtotime($data['dtend']->getData());
		unset($data['dtend']);
		if( isset($data['dtend']['tzid']) ) {
			echo 'End has TZ' . "\n";
			$ts = $ical->getTimeZoneInfo($data['dtend']['tzid']);
			$offset = $ts->getOffset($this->end);
			$this->end = strtotime(gmdate('D, d M Y H:i:s', $this->end) . ' ' . $offset);
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
}
