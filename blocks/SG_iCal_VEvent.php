<?php // BUILD: Remove line

class SG_iCal_VEvent {
	private $uid;
	private $start;
	private $end;
	private $summary;
	private $description;
	private $location;
	private $data;
	
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
	
	public function getUID() {
		return $this->uid;
	}
	
	public function getSummary() {
		return $this->summary;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function isBlocking() {
		return !(isset($this->data['transp']) && $this->data['transp'] == 'TRANSPARENT');
	}
	
	public function isConfirmed() {
		return (isset($this->data['status']) && $this->data['status'] == 'CONFIRMED');
	}
}
