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
	}
?>
