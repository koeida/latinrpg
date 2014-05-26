<?php

	require '/inc/Mustache.php';
	require '/inc/functional/_import.php';
	use Functional as F;
	require '/javascripts/fishing.php';

	$login = function () {
		if(isset($_POST['username'])) {
			$res = sqlSelect("select * from users where username = '".$_POST['username']."'");
			if(count($res) > 0) {
				$pwd = $res[0]['password'];
				if($pwd == $_POST['password']) {
					$_SESSION['uid'] = $res[0]['id'];
				}
			}

		} 
		if(!isset($_SESSION['uid'])) {
			renderTemplate("templates/login.html",array());
		}

	};

	require '/inc/required.php';

	function renderTemplate($file,$data) {
		$m = new Mustache_Engine(array("escape" => function($v){return $v;}));
		$template = file_get_contents($file);
		echo $m->render($template,$data);
	}

	function renderTemplateNE($file,$data) {
		$m = new Mustache_Engine;
		$template = file_get_contents($file);
		return $m->render($template,$data);
	}

	function getPoints($uid) {
		$sql = "select number, point_id from points_users where user_id = '$uid'";
		$res = sqlSelect($sql);
		return $res;
	}

	function concatWith($a,$s) {
		$res = F\reduce_left($a,function($val,$i,$c,$red) use ($s) {
			return $red . $s . $val;
		},"");
		return substr($res,1);
	}

	function requirementsSatisfied($points,$colname,$id,$pointsRequirement) {
		$res = F\First($points, function($x) use ($colname,$id) {return $x[$colname] == $id;});
		$number = $res['number'];
		if($number >= $pointsRequirement) {
			return true;
		} else {
			return false;
		}
	}

	function getAchievementList($uid) {
		$points = ($uid == '') ? array() : getPoints($uid);
		$reqs = array(
				array("id" => 0, "req" => 25, "achievement_id" => 0), //Fishing1 
				array("id" => 0, "req" => 75, "achievement_id" => 1), //Fishing2
				array("id" => 1, "req" => 25, "achievement_id" => 2), //EasyMonst
				array("id" => 2, "req" => 25, "achievement_id" => 3), //HardMonst
				array("id" => 3, "req" =>  6, "achievement_id" => 4)); //Words1
		
		$achievements = array_reduce($reqs,function ($carry,$e) use ($points) {
			if(requirementsSatisfied($points,'point_id',$e['id'],$e['req'])) {
				array_push($carry,$e['achievement_id']);
				return $carry;
			} else {
				return $carry;
			}
		},array());

		//Get html for completed achievements
		$sql = "SELECT description, badgeUrl, id, requirements, name FROM quests";
		$sqlRes = sqlSelect($sql);
		$res = array_reduce($sqlRes,function ($carry, $e) use ($achievements) {
			$template = 'templates/displayAchievement.html';
			$title = $e['name'];
			$makeTemp = function ($class,$title,$desc,$icon) use ($template) {
				return renderTemplateNE($template,array(
					"Class" => $class,
					"Title" => $title,
					"Description" => $desc,
					"IconUrl" => $icon));
			};

			if(F\contains($achievements,intval($e['id']))) {
				return $carry . $makeTemp("complete",$title, $e['description'],$e['badgeUrl']);
			} else {
				$img = substr($e['badgeUrl'],0,-4)."_no.png";
				return $carry . $makeTemp("incomplete",$title,$e['requirements'],$img);
			}
		},"");

		return $res;
	}


	$stats = function ($params) {
		if(F\contains($params,"getAchievementsXml")) {
			echo getAchievementList($_SESSION['uid']);
		} else if(F\contains($params,"getTreasure")) {
			$sqlTreasure = 'SELECT name,description,iconpath,amount from currencies,users_currencies where user_id = '.$_SESSION['uid'].' and currency_id = id';
			$res = sqlSelect($sqlTreasure);
			renderTemplate('templates/treasure.html',array('treasures' => $res));
		} else {
			renderTemplate('templates/character.html',$_SESSION);
		}
	};

	$battle = function ($params) {
		function addBattleRewards($isEasy) {
			$pointId = ($isEasy) ? 1 : 2;
			$trinketsGiven = ($isEasy) ? 1 : 2;
			$updateKillCountSql = "CALL increment_points (".$_SESSION['uid'].",".$pointId.",1)";
			$updateTrinketCountSql = "CALL increment_currency ($trinketsGiven,".$_SESSION['uid'].",2)";
			sqlRun($updateKillCountSql);
			sqlRun($updateTrinketCountSql);
		}
		if(F\contains($params,"killeasy")) {
			addBattleRewards(true);
		} else if (F\contains($params,"killhard")) {
			addBattleRewards(false);
		}
		renderTemplate('templates/second_declension.html',$_SESSION);
	};

	$fish = function ($params) use ($fishingData) {
		if(F\contains($params,"checkanswer")) {
			$prepGuess = urlDecode($params[3]);
			$nounGuess = urlDecode($params[4]);
			$currentPrep = $_SESSION['fishing']['currentPrep']['name'];
			$currentNoun = $_SESSION['fishing']['currentNoun']['name']; 
			if (($prepGuess == $currentPrep) && ($nounGuess == $currentNoun)) {
				$_SESSION['fishing']['currentFish'] += 1;
				$newData = newFishingRound($fishingData);
				echo $newData["fishx"].",".$newData['fishy'];
			} else {
				if($_SESSION['fishing']['currentFish'] > 0) {
					$_SESSION['fishing']['currentFish'] -= 1;
				}
				printf("false");
			}
		} else if (F\contains($params,"getPoints")) {
			//return total, currentCaught
			$currentCaught = $_SESSION['fishing']['currentFish'];
			$updateTotalSql = "CALL increment_currency(".$currentCaught.",".$_SESSION['uid'].",1)";
			sqlRun($updateTotalSql);
			$getTotalSql = "SELECT amount FROM users_currencies WHERE user_id = ".$_SESSION['uid']." AND currency_id = 1";
			$totalRes = sqlSelect($getTotalSql);
			$total = $totalRes[0]['amount'];
			echo $total.",".$currentCaught;
		} else {
			$_SESSION['fishing'] = array();
			$_SESSION['fishing']['currentFish'] = 0;
			renderTemplate('templates/fishing.html',newFishingRound($fishingData));	
		}
	};

	$toplevel = function () {
		renderTemplate('templates/overworld.html', $_SESSION);
	};

	function matchToImage($name,$v) {
		if($v == 'i') {
			return '<img name="'.$name.'" src="/img/'.$name.'.png" width="250px" />';
		} else {
			return $v;
		}
	}

	function getNewWord() {
		$sqlGetPossibleQuestions = 'SELECT word, matching, type from words where id in (select word_id from user_words where user_id = '.$_SESSION['uid'].')';
		$res = sqlSelect($sqlGetPossibleQuestions);

		$last = (isset($_SESSION['lastNewWord'])) ? $_SESSION['lastNewWord'] : '';
		$_SESSION['wordcount'] = count($res);

		//Pick random word from list;
		$wordChoices = F\pluck($res,'word');
		$word = getRandomFrom($wordChoices,$last);
		$wordData = F\first($res,function($elem) use ($word) {
			return $elem['word'] == $word;});

		//Get list of possible wrong answers of same type (noun, adj, etc);
		$answerChoices = F\select($res,function($elem) use ($wordData) {
			return $elem['type'] == $wordData['type'];});
		sort($answerChoices);

		//Generate the set of choices. 
		
		$answers = array();
		
		array_push($answers,matchToImage($word,$wordData['matching']));
		for($x = 0; $x < 3; $x++) {
			do {
				$selection = getRandomFrom($answerChoices,$word);			
				$result = matchToImage($selection['word'],$selection['matching']);
			} while(!(array_search($result,$answers) === false));
			array_push($answers,$result);
		}

		shuffle($answers);
		$_SESSION['lastNewWord'] = $wordData;
		return array("word" => $word, "answers" => $answers);
	}

	function getNewWords() {
		$sqlGetNewWords = 'SELECT id,word,matching,type from words where id not in (select word_id from user_words where user_id = '.$_SESSION['uid'].')';
		$res = sqlSelect($sqlGetNewWords);
		$_SESSION['wordcount'] = count($res);
		if(count($res) >= 3) {
			$vars = array();
			for($x = 0; $x < 3; $x++) {
				array_push($vars,"<b>".$res[$x]['word']."</b><br/>".matchToImage($res[$x]['word'],$res[$x]['matching']));
				sqlRun("INSERT INTO user_words (user_id,word_id) VALUES (" . $_SESSION['uid'] . "," . $res[$x]['id'] . ')');
			}
			renderTemplate('templates/answers.html',array("answers" => $vars));
		}
	}

	function getGold($uid) {
		$sqlGetGold = 'SELECT amount FROM users_currencies WHERE currency_id = 0 and user_id = '.$uid;
		$res = sqlSelect($sqlGetGold);
		return intval($res[0]['amount']);
	}

	$words = function ($params) {
		if(isset($params[2]) && $params[2] == 'check') {
			$answer = urldecode($params[3]);
			
			if(($answer == $_SESSION['lastNewWord']['word']) || ($answer == $_SESSION['lastNewWord']['matching'])) {
				//increment gold
				$amountToIncrement = intval($_SESSION['wordcount'] / 3);
				$sqlIncrementGold = "CALL increment_currency(".$amountToIncrement.",".$_SESSION['uid'].",0)";
				sqlRun($sqlIncrementGold);

				renderTemplate('templates/newWords.html',getNewWord());
			} else {
				echo "FALSE";
			}
		} else if (isset($params[2]) && $params[2] == 'currentWord') {			
			echo $_SESSION['lastNewWord']['word'];
		} else if (isset($params[2]) && $params[2] == 'getNew') {
			getNewWords();
		} else if (isset($params[2]) && $params[2] == 'getGold') {
			echo getGold($_SESSION['uid']);
		} else {
			$vars = getNewWord();
			renderTemplate('templates/words.html',$vars);
		}
	};

	$inventory = function ($params) {
		$sql = "SELECT * FROM inventory WHERE player_id = ".$_SESSION['pid'];
		$sqlRes = sqlSelect($sql);
		
	};

	if(isset($_SESSION['uid'])) {
		$path = parse_url($_SERVER['REQUEST_URI'])['path'];

		$matches = array(
			"/^\/$/" => $toplevel,
			"/^\/stats/" => $stats,
			"/^\/battle/" => $battle,
			"/^\/fish/" => $fish,
			"/^\/words/" => $words
		);

		foreach($matches as $key => $value) {
			if(preg_match($key,$path)) {
				$path_exp = explode('/',$path);
				$value(F\tail($path_exp));
				break;
			}	
		} 
	}
?>

