<?php

class SG_iCal_VCalendar implements IteratorAggregate {
	private $data;
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	public function getTitle() {
		if( isset($this->data['x-wr-calname']) ) {
			return $this->data['x-wr-calname'];
		} else {
			return null;
		}
	}
	
	public function getDescription() {
		if( isset($this->data['x-wr-caldesc']) ) {
			return $this->data['x-wr-caldesc'];
		} else {
			return null;
		}
	}
	
	public function getIterator() {
		return new ArrayIterator($this->data);
	}
}

?>