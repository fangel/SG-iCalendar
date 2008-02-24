<?php // BUILD: Remove line

/**
 * A class to store Frequency-rules in. Will allow a easy way to find the 
 * last (previous) and next occurance of the rule.
 *
 * Still really lacking, but will parse Timezone-rules fairly decent..
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_Freq {
	private $rules = array('freq'=>'yearly', 'interval'=>1);
	private $start = 0;
	private $weekdays = array('MO'=>'monday', 'TU'=>'tuesday', 'WE'=>'wednesday', 'TH'=>'thursday', 'FR'=>'friday', 'SA'=>'saturday', 'SU'=>'sunday');
	
	/**
	 * Constructs a new Freqency-rule
	 * @param $rule string 
	 * @param $start int Unix-timestamp (important!)
	 */
	public function __construct( $rule, $start ) {
		$rules = array();
		foreach( explode(';', $rule) AS $v) {
			list($k, $v) = explode('=', $v);
			$rules[ strtolower($k) ] = $v;
		}
			
		$this->rules += $rules;
		$this->start = $start;
	}
	
	/**
	 * Returns the last (most recent) occurance of the rule from the 
	 * given offset
	 * @param int $offset
	 * @return int
	 */
	public function lastOccurance( $offset ) {
		$t1 = $this->start;
		while( ($t2 = $this->findNext($t1)) < $offset) {
			$t1 = $t2;
		}
		return $t1;
	}
	
	/**
	 * Returns the next occurance of this rule after the given offset
	 * @param int $offset
	 * @return int
	 */
	public function nextOccurance( $offset ) {
		return $this->findNext( $this->lastOccurance( $offset) );
	}
	
	/**
	 * Calculates the next time after the given offset that the rule 
	 * will apply.
	 * @param int $offset
	 * @return int
	 */
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
	 * Finds the starting point for the next rule. It goes 'interval'
	 * 'freq' forward in time since the given offset
	 * @param int $offset
	 * @return int
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
	
	/**
	 * Applies the BYDAY rule to the given timestamp
	 * @param string $rule
	 * @param int $t
	 * @return int
	 */
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
