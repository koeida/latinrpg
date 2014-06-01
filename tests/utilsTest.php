<?php
	require '/inc/utils.php';
	class utilsTest extends PHPUnit_Framework_TestCase {
		public function testMapDict() {
			$start = array(
				array('key' => 0, 'key2' => 0),
				array('key' => 1, 'key2' => 1));
			$expected = array(
				array('key' => 2, 'key2' => 0),
				array('key' => 3, 'key2' => 1));

			$actual = mapDict($start,'key',function($x) { return $x + 2;});
			$this->assertTrue(serialize($expected) === serialize($actual));
		}

		public function testFilterDict() {
			$start = array(
				array('key' => 0, 'key2' => 0),
				array('key' => 1, 'key2' => 1));
			$expected = array(
				array('key' => 0, 'key2' => 0));

			$actual = filterDict($start,'key',function($x) { return $x == 0; });
			$this->assertTrue($expected == $actual);
		}	

		public function testFirstDict() {
			$start = array(
				array('key' => 0, 'key2' => 0),
				array('key' => 1, 'key2' => 1));
			$expected = array('key' => 0, 'key2' => 0);

			$actual = firstDict($start,'key',function($x) { return $x == 0; });
			$this->assertTrue($expected == $actual);
		}	
	}
?>
