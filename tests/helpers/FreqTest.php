<?php

require_once dirname(__FILE__) . '/../common.php';
require_once 'PHPUnit/Framework.php';

class FreqTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		date_default_timezone_set('Europe/Copenhagen');
	}

	public function testDailyCount() {
		$dateset = array(
			873183600,
			873270000,
			873356400,
			873442800,
			873529200,
			873615600,
			873702000,
			873788400,
			873874800,
			873961200,
			-1
		);

		$rule = 'FREQ=DAILY;COUNT=10';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testDailyUntil() {
		$dateset = array(
			873183600,
			873270000,
			873356400,
			873442800,
			873529200,
			873615600,
			873702000,
			873788400,
			873874800,
			873961200,
			874047600
		);

		$rule = 'FREQ=DAILY;UNTIL=19971224T000000Z';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);

		$freq = new SG_iCal_Freq($rule, $start);
		$this->assertEquals(882864000, $freq->previousOccurrence(time()));
	}

	public function testDailyInterval() {
		$dateset = array(
			873183600,
			873356400,
			873529200,
			873702000,
			873874800,
			874047600,
			874220400
		);
		$rule = 'FREQ=DAILY;INTERVAL=2';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testDailyIntervalCount() {
		$dateset = array(
			873183600,
			874047600,
			874911600,
			875775600,
			876639600,
			-1
		);
		$rule = 'FREQ=DAILY;INTERVAL=10;COUNT=5';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testDailyBydayBymonthUntil() {
		$rules = array(
			'FREQ=YEARLY;UNTIL=20000131T090000Z;BYMONTH=1;BYDAY=SU,MO,TU,WE,TH,FR,SA',
			'FREQ=DAILY;UNTIL=20000131T090000Z;BYMONTH=1'
		);
		$datesets = array(
			array(
				883641600,
				883728000,
				883814400,
				883900800
			),
			array(
				915177600,
				915264000,
				915350400,
				915436800
			),
			array(
				946713600,
				946800000,
				946886400,
				946972800
			)
		);

		foreach( $rules As $rule ) {
			$start = strtotime('19980101T090000');
			$this->assertRule( $rule, $start, $datesets[0]);

			$start = strtotime('+1 year', $start);
			$this->assertRule( $rule, $start, $datesets[1]);

			$start = strtotime('+1 year', $start);
			$this->assertRule( $rule, $start, $datesets[2]);

			$freq = new SG_iCal_Freq($rule, $start);
			$this->assertEquals(949305600, $freq->previousOccurrence(time()));
		}
	}

	public function testWeeklyCount() {
		$dateset = array(
			873183600,
			873788400,
			874393200,
			874998000,
			875602800,
			876207600,
			876812400,
			877417200,
			878025600,
			878630400,
			-1
		);
		$rule = 'FREQ=WEEKLY;COUNT=10';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testWeeklyUntil() {
		$dateset = array(
			873183600,
			873788400,
			874393200,
			874998000,
			875602800,
			876207600,
			876812400,
			877417200,
			878025600,
			878630400,
			879235200
		);
		$rule = 'FREQ=WEEKLY;UNTIL=19971224T000000Z';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);

		$freq = new SG_iCal_Freq($rule, $start);
		$this->assertEquals(882864000, $freq->previousOccurrence(time()), 'Failed getting correct end date');
	}

	public function testWeeklyBydayLimit() {
		$rules = array(
			'FREQ=WEEKLY;UNTIL=19971007T000000Z;WKST=SU;BYDAY=TU,TH',
			'FREQ=WEEKLY;COUNT=10;WKST=SU;BYDAY=TU,TH'
		);
		$dateset = array(
			873183600,
			873356400,
			873788400,
			873961200,
			874393200,
			874566000,
			874998000,
			875170800,
			875602800,
			875775600,
			-1
		);
		$start = strtotime('19970902T090000');
		foreach( $rules AS $rule ) {
			$this->assertRule( $rule, $start, $dateset);
		}
	}

	public function testWeeklyIntervalUntilByday() {
		$dateset = array(
			873183600,
			873270000,
			873442800,
			874306800,
			874479600,
			874652400,
			875516400,
			875689200,
			875862000,
			876726000,
			876898800
		);
		$rule = 'FREQ=WEEKLY;INTERVAL=2;UNTIL=19971224T000000Z;WKST=SU;BYDAY=MO,WE,FR';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);

		$freq = new SG_iCal_Freq($rule, $start);
		$this->assertEquals(882777600, $freq->previousOccurrence(time()), 'Failed getting correct end date');
	}

	public function testWeeklyIntervalBydayCount() {
		$dateset = array(
			873183600,
			873356400,
			874393200,
			874566000,
			875602800,
			875775600,
			876812400,
			876985200,
			-1
		);
		$rule = 'FREQ=WEEKLY;INTERVAL=2;COUNT=8;WKST=SU;BYDAY=TU,TH';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyBydayCount() {
		$dateset = array(
			873442800,
			875862000,
			878889600,
			881308800,
			883728000,
			886752000,
			889171200,
			891586800,
			894006000,
			897030000,
			-1
		);
		$rule = 'FREQ=MONTHLY;COUNT=10;BYDAY=1FR';
		$start = strtotime('19970905T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyBydayUntil() {
		$dateset = array(
			873442800,
			875862000,
			878889600,
			881308800,
			-1
		);
		$rule = 'FREQ=MONTHLY;UNTIL=19971224T000000Z;BYDAY=1FR';
		$start = strtotime('19970905T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyIntervalBydayCount2() {
		$dateset = array(
			873615600,
			875430000,
			878457600,
			880876800,
			883900800,
			885715200,
			888739200,
			891154800,
			894178800,
			896598000,
			-1
		);
		$rule = 'FREQ=MONTHLY;INTERVAL=2;COUNT=10;BYDAY=1SU,-1SU';
		$start = strtotime('19970907T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyBydayCount2() {
		$dateset = array(
			874911600,
			877330800,
			879753600,
			882777600,
			885196800,
			887616000,
			-1
		);
		$rule = 'FREQ=MONTHLY;COUNT=6;BYDAY=-2MO';
		$start = strtotime('19970922T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyBymonthday() {
		$dateset = array(
			875430000,
			878112000,
			880704000,
			883382400,
			886060800,
			888480000
		);
		$rule = 'FREQ=MONTHLY;BYMONTHDAY=-3';
		$start = strtotime('19970928T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyBymonthdayCount() {
		$dateset = array(
			873183600,
			874306800,
			875775600,
			876898800,
			878457600,
			879580800,
			881049600,
			882172800,
			883728000,
			884851200,
			-1
		);
		$rule = 'FREQ=MONTHLY;COUNT=10;BYMONTHDAY=2,15';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyBymonthdayCount2() {
		$dateset = array(
			875602800,
			875689200,
			878284800,
			878371200,
			880876800,
			880963200,
			883555200,
			883641600,
			886233600,
			886320000,
			-1
		);
		$rule = 'FREQ=MONTHLY;COUNT=10;BYMONTHDAY=1,-1';
		$start = strtotime('19970930T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyIntervalBymonthdayCount() {
		$dateset = array(
			873874800,
			873961200,
			874047600,
			874134000,
			874220400,
			874306800,
			921052800,
			921139200,
			921225600,
			921312000,
			-1
		);
		$rule = 'FREQ=MONTHLY;INTERVAL=18;COUNT=10;BYMONTHDAY=10,11,12,13,14,15';
		$start = strtotime('19970910T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMonthlyIntervalByday() {
		$dateset = array(
			873183600,
			873788400,
			874393200,
			874998000,
			875602800,
			878630400,
			879235200,
			879840000,
			880444800,
			884073600
		);
		$rule = 'FREQ=MONTHLY;INTERVAL=2;BYDAY=TU';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyCountBymonth() {
		$dateset = array(
			865926000,
			868518000,
			897462000,
			900054000,
			928998000,
			931590000,
			960620400,
			963212400,
			992156400,
			994748400,
			-1
		);
		$rule = 'FREQ=YEARLY;COUNT=10;BYMONTH=6,7';
		$start = strtotime('19970610T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyIntervalCountBymonth() {
		$dateset = array(
			857980800,
			915955200,
			918633600,
			921052800,
			979113600,
			981792000,
			984211200,
			1042185600,
			1044864000,
			1047283200,
			-1
		);
		$rule = 'FREQ=YEARLY;INTERVAL=2;COUNT=10;BYMONTH=1,2,3';
		$start = strtotime('19970310T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyIntervalCountByyearday() {
		$dateset = array(
			852105600,
			860655600,
			869295600,
			946713600,
			955263600,
			963903600,
			1041408000,
			1049958000,
			1058598000,
			1136102400,
			-1
		);
		$rule = 'FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200';
		$start = strtotime('19970101T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyByday() {
		$dateset = array(
			864025200,
			895474800,
			926924400
		);
		$rule = 'FREQ=YEARLY;BYDAY=20MO';
		$start = strtotime('19970519T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyByweeknoByday() {
		$dateset = array(
			863420400,
			894870000,
			926924400
		);
		$rule = 'FREQ=YEARLY;BYWEEKNO=20;BYDAY=MO';
		$start = strtotime('19970512T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyBydayBymonth() {
		$dateset = array(
			858240000,
			858844800,
			859449600,
			889084800,
			889689600,
			890294400,
			890899200,
			920534400,
			921139200,
			921744000
		);
		$rule = 'FREQ=YEARLY;BYMONTH=3;BYDAY=TH';
		$start = strtotime('19970313T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyBydayBymonth2() {
		$dateset = array(
			865494000,
			866098800,
			866703600,
			867308400,
			867913200,
			868518000,
			869122800,
			869727600,
			870332400,
			870937200
		);
		$rule = 'FREQ=YEARLY;BYDAY=TH;BYMONTH=6,7,8';
		$start = strtotime('19970605T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyBydayBymonthday() {
		$dateset = array(
			873183600,
			887356800,
			889776000,
			910944000,
			934527600,
			971420400
		);
		$rule = 'FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyBydayBymonthday2() {
		$dateset = array(
			874134000,
			876553200,
			878976000,
			882000000,
			884419200,
			886838400,
			889257600,
			892278000,
			894697200,
			897721200
		);
		$rule = 'FREQ=MONTHLY;BYDAY=SA;BYMONTHDAY=7,8,9,10,11,12,13';
		$start = strtotime('19970913T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testYearlyIntervalBymonthBydayBymonthday() {
		$dateset = array(
			847180800,
			973584000,
			1099382400
		);
		$rule = 'FREQ=YEARLY;INTERVAL=4;BYMONTH=11;BYDAY=TU;BYMONTHDAY=2,3,4,5,6,7,8';
		$start = strtotime('19961105T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	// TODO: SETPOS rules

	public function testHourlyIntervalUntil() {
		$dateset = array(
			873183600,
			873194400,
			873205200,
			-1
		);
		$rule = 'FREQ=HOURLY;INTERVAL=3;UNTIL=19970902T170000';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMinutelyIntervalCount() {
		$dateset = array(
			873183600,
			873184500,
			873185400,
			873186300,
			873187200,
			873188100,
			-1
		);
		$rule = 'FREQ=MINUTELY;INTERVAL=15;COUNT=6';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMinutelyIntervalCount2() {
		$dateset = array(
			873183600,
			873189000,
			873194400,
			873199800,
			-1
		);
		$rule = 'FREQ=MINUTELY;INTERVAL=90;COUNT=4';
		$start = strtotime('19970902T090000');
		$this->assertRule( $rule, $start, $dateset);
	}

	public function testMinutelyIntervalByhour() {
		$rules = array(
			'FREQ=MINUTELY;INTERVAL=20;BYHOUR=9,10,11,12,13,14,15,16'/*,
			'FREQ=DAILY;BYHOUR=9,10,11,12,13,14,15,16;BYMINUTE=0,20,40'*/
		);
		// TODO: Fix it so multi byhour and byminute will work
		$dateset = array(
			873183600,
			873184800,
			873186000,
			873187200,
			873188400,
			873189600,
			873190800,
			873192000,
			873193200,
			873194400
		);
		$start = strtotime('19970902T090000');
		foreach( $rules AS $rule ) {
			$this->assertRule( $rule, $start, $dateset);
		}
	}

	/*
	weird : in this test $start is not a matched occurrence but...

	to do something like that, we need EXDATE :
	DTSTART;TZID=US-Eastern:19970902T090000
	EXDATE;TZID=US-Eastern:19970902T090000
	RRULE:FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13
	*/

	public function testFirstOccurrencesByYearDay() {
		$rule = 'FREQ=YEARLY;INTERVAL=2;BYYEARDAY=1;COUNT=5';
		$start = strtotime('2009-10-27T090000');
		$freq = new SG_iCal_Freq($rule, $start);
		$this->assertEquals(strtotime('2009-10-27T09:00:00'), $freq->firstOccurrence());
		$this->assertEquals(strtotime('2011-01-01T09:00:00'), $freq->nextOccurrence($start));
	}

	public function testFirstOccurrencesByYearDayWithoutFirstDate() {
		$rule = 'FREQ=YEARLY;INTERVAL=2;BYYEARDAY=1;COUNT=5';
		$start = strtotime('2009-10-27T090000');
		$freq = new SG_iCal_Freq($rule, $start, array($start));
		$this->assertEquals(strtotime('2011-01-01T09:00:00'), $freq->firstOccurrence());
	}

	public function testLastOccurrenceByYearDay() {
		$rule = 'FREQ=YEARLY;INTERVAL=2;BYYEARDAY=1;COUNT=5';
		$start = strtotime('2011-01-01T090000');
		$freq = new SG_iCal_Freq($rule, $start);
		$this->assertEquals(strtotime('2019-01-01T09:00:00'), $freq->lastOccurrence());
	}

	public function testCacheCount() {
		$rule = 'FREQ=YEARLY;INTERVAL=2;BYYEARDAY=1;COUNT=5';
		$start = strtotime('2011-01-01T090000');
		$freq = new SG_iCal_Freq($rule, $start);
		$this->assertEquals(5, count($freq->getAllOccurrences()));
		$this->assertEquals(strtotime('2019-01-01T09:00:00'), $freq->lastOccurrence());
	}

	/* TODO: BYSETPOS rule :
	The 3rd instance into the month of one of Tuesday, Wednesday or
	Thursday, for the next 3 months:

		DTSTART;TZID=US-Eastern:19970904T090000
		RRULE:FREQ=MONTHLY;COUNT=3;BYDAY=TU,WE,TH;BYSETPOS=3
	*/

	/* TODO: WKST rule
	*/

	//check a serie of dates
	private function assertRule( $rule, $start, $dateset ) {
		$freq = new SG_iCal_Freq($rule, $start);
		reset($dateset);
		$n = $start - 1;
		do {
			$n = $freq->findNext($n);
			//echo date('Y-m-d H:i:sO ',$n);
			$e = (current($dateset) != -1) ? current($dateset) : false;
			$this->assertEquals($e, $n);
		} while( next($dateset) !== false );
	}
}

?>
