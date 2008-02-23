<?php // BUILD: Remove line

class SG_iCal_VTimeZone {
	private $tzid;
	private $daylight;
	private $standard;
	private $cache = array();
	
	public function __construct( $data ) {
		require_once dirname(__FILE__).'/../helpers/SG_iCal_Freq.php'; // BUILD: Remove line
	
		$this->tzid = $data['tzid'];
		$this->daylight = $data['daylight'];
		$this->standard = $data['standard'];
	}
	
	public function getTimeZoneId() {
		return $this->tzid;
	}
	
	public function getOffset( $ts ) {
		$act = $this->getActive($ts);
		return $this->{$act}['tzoffsetto'];
	}
	
	public function getTimeZoneName($ts) {
		$act = $this->getActive($ts);
		return $this->{$act}['tzname'];
	}
	
	private function getActive( $ts ) {
		if( isset($this->cache[$ts]) ) {
			return $this->cache[$ts];
		}
		
		$daylight_freq = new SG_iCal_Freq($this->daylight['rrule'], strtotime($this->daylight['dtstart']));
		$last_dst = $daylight_freq->lastOccurance($ts);
		if( date('Y') == date('Y', $last_dst) ) {
			$this->cache[$ts] = 'daylight';
		} else {
			$this->cache[$ts] = 'standard';
		}
		
		return $this->cache[$ts];
	}
}
