<?php
require '/inc/functional/_import.php';
use Functional as F;

define("NOMINATIVE",0);
define("GENITIVE",1);
define("DATIVE",2);
define("ACCUSATIVE",3);
define("ABLATIVE",4);

define("SINGULARNOUN",0);
define("PLURALNOUN",5);
define("SINGULARVERB",0);
define("PLURALVERB",3);

define("MASCULINE",0);
define("FEMININE",1);
define("NEUTER",2);

define("NOUN","NOUN");
define("VERB","VERB");
define("REL_CLAUSE","REL_CLAUSE");
define("REL_PRO","REL_PRO");

define("SUBJECT","SUBJECT");
define("OBJ","OBJ");

define("FIRSTPERSON",0);
define("SECONDPERSON",1);
define("THIRDPERSON",2);

//WordList
$people = [
	["Iulius","Iuli",1,MASCULINE],
	["Aemilia","Aemili",0,FEMININE],
	["Marcus","Marc",1,MASCULINE],
	["Iulia","Iuli",0,FEMININE]];

$objects = [
	["vacca","vacc",0,FEMININE],
	["cuniculus","cunicul",1,MASCULINE],
	["gladius","gladi",1,MASCULINE],
	["pira","pir",0,FEMININE],
	["saccus","sacc",1,MASCULINE]];

$verbs = [
	["am","amāv","amāt",1],
	["puls","pulsāv","pulsāt",1],
	["hab","habu","habit",2],
	["port","portāv","portat",1]];

function declineNoun($word,$case,$number) {
	list($nominative,$stem,$declension) = $word;
	
	if($case == NOMINATIVE && $number == SINGULARNOUN) {
		return $nominative;
	}

	$declensions = [
		['a', 'ae', 'ae', 'am','ā','ae','ārum','īs','ās','īs'],
		['us','ī','ō','um','ō','ī','ōrum','īs','ōs','īs']];
	
	return $stem . $declensions[$declension][$case + $number]; 	
}

function getRelativePronoun($gender,$case,$number) {
	$relativePronouns = [
		['quī','cuius','cui','quem','quō','quī','quōrum','quibus','quōs','quibus'],
		['quae','cuius','cui','quem','quā','quae','quārum','quibus','quās','quibus'],
		['quod','cuius','cui','quem','quō','quae','quōrum','quibus','quod','quibus']];

	return $relativePronouns[$gender][$case + $number];
}

function pickSubject ($list) {
	$wordIndex = rand(0,count($list) - 1);
	$word = $list[$wordIndex];
	$gender = $word[3];

	return [declineNoun($word,NOMINATIVE,SINGULARNOUN),$gender,$wordIndex];
}

function pickNoun ($list,$case) {
	$wordIndex = rand(0,count($list) - 1);
	$number = (rand(0,1) == 0) ? SINGULARNOUN : PLURALNOUN;
	$word = $list[$wordIndex];
	$gender = $word[3];

	return [declineNoun($word,$case,$number),$gender,$wordIndex];
}

function pickVerb ($list,$person,$number) {
	$verbIndex = rand(0,count($list) - 1);
	return conjugate($list[$verbIndex],$person,$number);
}

function conjugate($verb,$person,$number) {
	list($presentStem,$perfectStem,$pastParticipleStem,$conjugation) = $verb;
	$conjugations = [
		['ō','ās','at','āmus','ātis','ant'],
		['eō','ēs','et','ēmus','ētis','ent'],
		['ō','is','it','imus','itis','unt'],
		['iō','īs','it','īmus','ītis','iunt']];
	return $presentStem . $conjugations[$conjugation - 1][$person + $number];
}

function sentenceToString($sentence) {
	if($sentence == null) {
			return ".";
	} else {
		list($type,$data) = F\head($sentence);
		return $data[0] . ' ' . sentenceToString(F\tail($sentence));
	}
}

function relativeSentenceRecurse($structure,$subjectList,$objectList,$verbList,$result) {
	if(count($structure) == 0) {
		return $result;
	} else {
		$current = F\head($structure);
		$sentenceElement = $current[0];
		$option = (count($current) > 1) ? $current[1] : null;

		switch($sentenceElement) {
			case SUBJECT:
				$subj = pickSubject($subjectList);
				$subjectIndex = $subj[2];
				$elem = [SUBJECT,$subj];
				array_push($result,$elem);
				unset($subjectList[$subjectIndex]);
				$subjectList = array_values($subjectList);
				break;
			case OBJ:
				$elem = [OBJ,pickNoun($objectList,ACCUSATIVE)];
				array_push($result,$elem);
				break;
			case REL_CLAUSE:
				$currentSubject = $result[0];
				$subjectGender = $currentSubject[1][1];
				$pronoun = getRelativePronoun($subjectGender,$option,SINGULARNOUN); 
				$firstSubElem = ($option == ACCUSATIVE) ? [SUBJECT,NOMINATIVE] : [OBJ, ACCUSATIVE];
				$rel_struct = [
					$firstSubElem,
					[VERB]];
				$relativeInitial = [[REL_PRO,[$pronoun,$subjectGender]]];
				$elem = relativeSentenceRecurse($rel_struct,$subjectList,$objectList,$verbList,$relativeInitial);
				$result = array_merge($result,$elem);
				break;
			case VERB:
				$elem = [VERB,[pickVerb($verbList,THIRDPERSON,SINGULARVERB)]];
				array_push($result,$elem);
				break;
		}
		return relativeSentenceRecurse(F\tail($structure),$subjectList,$objectList,$verbList,$result);
	}
}

function generateRelativePronounSentence($structure,$subjectList,$objectList,$verbList) {
		$sentenceData = relativeSentenceRecurse($structure,$subjectList,$objectList,$verbList,array());
		return $sentenceData;
}
?>
