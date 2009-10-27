<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__).'/blocks/AllTests.php';
require_once dirname(__FILE__).'/helpers/AllTests.php';

class AllTests {

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite();
		$suite->addTest(Blocks_AllTests::suite());
		$suite->addTest(Helpers_AllTests::suite());

		return $suite;
	}
}
