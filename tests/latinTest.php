<?php
	require '/inc/latin.php';
	class LatinTests extends PHPUnit_Framework_TestCase {

		public function testRelativeSentence() {
			global $people;
			global $verbs;
			global $objects;
			$subjectRelative = [
				[SUBJECT,NOMINATIVE],
				[REL_CLAUSE,NOMINATIVE],
				[OBJ,ACCUSATIVE],
				[VERB]];
			$objectRelative = [
				[SUBJECT,NOMINATIVE],
				[REL_CLAUSE,ACCUSATIVE],
				[OBJ,ACCUSATIVE],
				[VERB]];

			$res = sentenceToString(generateRelativePronounSentence($subjectRelative,$people,$objects,$verbs));
			echo $res."\r\n";

			$res = sentenceToString(generateRelativePronounSentence($objectRelative,$people,$objects,$verbs));
			echo $res;
			$this->assertTrue(true);		
		}
	}
?>
