<?php
	require '/inc/fishing.php';
	class FishTests extends PHPUnit_Framework_TestCase {
		public function testWrongAnswer() {
			global $fishingData, $_SESSION;
			$wrongPrep = "prep";
			$wrongNoun = "noun";
			$correctPrep = "cprep";
			$correctNoun = "cnoun";
			$_SESSION['fishing']['currentFish'] = 10;
			$result = checkFishingAnswer($wrongPrep,$wrongNoun,$correctPrep,$correctNoun);
			$this->AssertEquals(false,$result);
			$this->AssertEquals(9,$_SESSION['fishing']['currentFish']);
		}
	}
?>
