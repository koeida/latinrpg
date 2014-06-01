<?php
	require '/inc/latin.php';
	class LatinTests extends PHPUnit_Framework_TestCase {
		public function testDecline() {
			$word = ["gladius","gladi",1];
			$declined = decline($word,ACCUSATIVE,SINGULAR);
			$this->assertEquals("gladium",$declined);

			$declined = decline($word,NOMINATIVE,PLURAL);
			$this->assertEquals("gladiī",$declined);

			$declined = decline($word,NOMINATIVE,SINGULAR);
			$this->assertEquals("gladius",$declined);

			$declined = decline($word,ABLATIVE,PLURAL);
			$this->assertEquals("gladiīs",$declined);
		}
	}
?>
