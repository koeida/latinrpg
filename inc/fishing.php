<?php
	$ABL = 0;
	$ACC = 1;
	$fishingData = array(
		"easy" => array (
			array("name"=>'in',   "ncase"=>$ABL, "x"=>0,   "y"=> 0),
			array("name"=>'cum',  "ncase"=>$ABL, "x"=>-55, "y"=> 0),
			array("name"=>'suprā',"ncase"=>$ACC, "x"=>0,   "y"=>-65),
			array("name"=>'sub',  "ncase"=>$ABL, "x"=>0,   "y"=>100)),
		"stuff" => array(
			array(
				array("name"=> "malā",   "x"=>100, "y"=>300),
				array("name"=> "gladiō", "x"=>280, "y"=>227),
				array("name"=> "pecuniā","x"=>435, "y"=>285)),
			array(			
				array("name"=> "malam",   "x"=>100, "y"=>300),
				array("name"=> "gladium", "x"=>280, "y"=>227),
				array("name"=> "pecuniam","x"=>435, "y"=>285))),
		"nouns" => array(
			array("ABL"=> "malā",   "ACC"=>"malam"),
			array("ABL"=> "gladiō", "ACC"=>"gladium"),
			array("ABL"=> "pecuniā","ACC"=>"pecuniam")));
	
	function checkFishingAnswer($prepGuess,$nounGuess,$currentPrep,$currentNoun) {
		global $fishingData;

		if (($prepGuess == $currentPrep) && ($nounGuess == $currentNoun)) {
			$_SESSION['fishing']['currentFish'] += 1;
			$newData = newFishingRound($fishingData);
			return $newData["fishx"].",".$newData['fishy'];
		} else {
			if($_SESSION['fishing']['currentFish'] > 0) {
				$_SESSION['fishing']['currentFish'] -= 1;
			}
			return false;
		}
	}

	function newFishingRound($fishingData) {
		//Determine preposition
		$newPrep = $fishingData['easy'][rand(0,count($fishingData['easy']) - 1)];
		$prepName = $newPrep['name'];
		$prepCase = $newPrep['ncase'];

		//Determine noun
		$nounOptions = $fishingData['stuff'][$prepCase];
		$newNoun = $nounOptions[rand(0,2)];

		//Initialize Session
		$_SESSION['fishing']['currentPrep'] = $newPrep;
		$_SESSION['fishing']['currentNoun'] = $newNoun;
		$nounX = $_SESSION['fishing']['currentNoun']['x'];
		$prepX = $_SESSION['fishing']['currentPrep']['x'];
		$nounY = $_SESSION['fishing']['currentNoun']['y'];
		$prepY = $_SESSION['fishing']['currentPrep']['y'];

		//Calculate fish coordinates
		$fishingData["fishx"] = intval($nounX) + intval($prepX);
		$fishingData["fishy"] = intval($nounY) + intval($prepY);
		return $fishingData;
	}
?>
