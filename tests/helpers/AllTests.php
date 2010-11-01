<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/FreqTest.php';
require_once dirname(__FILE__).'/RecurrenceTest.php';
require_once dirname(__FILE__).'/DurationTest.php';

class Helpers_AllTests {

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Helpers');
		$suite->addTestSuite('FreqTest');
		$suite->addTestSuite('RecurrenceTest');
		$suite->addTestSuite('DurationTest');

		return $suite;
	}

}