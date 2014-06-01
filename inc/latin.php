<?php
define("NOMINATIVE",0);
define("GENITIVE",1);
define("DATIVE",2);
define("ACCUSATIVE",3);
define("ABLATIVE",4);

define("SINGULAR",0);
define("PLURAL",5);

function decline($word,$case,$number) {
	list($nominative,$stem,$declension) = $word;
	
	if($case == NOMINATIVE && $number == SINGULAR) {
		return $nominative;
	}

	$declensions = [
		['a', 'ae', 'ae', 'am','ā','ae','ārum','īs','ās','īs'],
		['us','ī','ō','um','ō','ī','ōrum','īs','ōs','īs']];
	
	return $stem . $declensions[$declension][$case + $number]; 	
}
?>
