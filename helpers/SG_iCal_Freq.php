<?php // BUILD: Remove line

class SG_iCal_Freq {
	private $rules = array('freq'=>'yearly', 'interval'=>1);
	private $start = 0;
	private $weekdays = array('MO'=>'monday', 'TU'=>'tuesday', 'WE'=>'wednesday', 'TH'=>'thursday', 'FR'=>'friday', 'SA'=>'saturday', 'SU'=>'sunday');
	
	public function __construct( $rule, $start ) {
		$rules = array();
		foreach( explode(';', $rule) AS $v) {
			list($k, $v) = explode('=', $v);
			$rules[ strtolower($k) ] = $v;
		}
			
		$this->rules += $rules;
		$this->start = $start;
	}
	
	public function lastOccurance( $offset ) {
		$t1 = $this->start;
		while( ($t2 = $this->findNext($t1)) < $offset) {
			$t1 = $t2;
		}
		return $t1;
	}
	
	public function nextOccurance( $offset ) {
		return $this->findNext( $this->lastOccurance( $offset) );
	}
	
	private function findNext($offset) {
		$t = $this->findStartingPoint( $offset );
		$t = mktime(date('H', $this->start), date('i', $this->start), date('s', $this->start), date('m', $t), date('d', $t), date('Y',$t));
		if( isset($this->rules['bymonth']) ) {
			$t = mktime(date('H',$t), date('i',$t), date('s',$t), $this->rules['bymonth'], 1, date('Y', $t));
		}
		if( isset($this->rules['byday']) ) {
			$t = $this->rule_byday($this->rules['byday'], $t);
		}

		return $t;
	}
	
	/**
	 * Returns $offset
	 */
	private function findStartingPoint( $offset ) {
		$freq = strtolower($this->rules['freq']);
		switch( $freq ) {
			case "daily":
				$freq = "day__"; // nice hack here
			case "yearly":
			case "monthly":
			case "weekly":
			case "hourly":
			case "minutely":
				$t = '+' . $this->rules['interval'] . ' ' . substr($freq,0,-2) . 's';
				$sp = strtotime($t, $offset);
				break;
			default:
				$sp = $this->start + 1;
		}
		return $sp;
	}
	
	private function rule_byday($rule, $t) {
		$dir = ($rule{0} == '-') ? -1 : 1;
		$dir_t = ($dir == 1) ? 'next' : 'last';
		$c = preg_replace('/[^0-9]/','',$rule);
		$d = $this->weekdays[substr($rule,-2)];
		if( $dir == -1 ) {
			// We are going backworks, so add a month so we can
			// back up again
			$t = strtotime("+1 month", $t);
		}
		while($c > 0 ) {
			$t = strtotime($dir_t . ' ' . $d . ' ' . date('H:i:s',$t), $t);
			$c--;
		}
		return $t;
	}
}
