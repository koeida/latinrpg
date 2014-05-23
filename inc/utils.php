<?php
function checkLogin() {
	global $DBURL, $DBID, $DBPW, $DBNAME,$login;
	$con = mysqli_connect($DBURL,$DBID,$DBPW,$DBNAME);
	if(mysqli_connect_errno()) {
		echo "Failed to connect: " . mysqli_connect_error();
		return;
	}

	if(!isset($_SESSION['uid'])) {
		$login();
	} 
	mysqli_close($con);
}

function sqlSelect($sql) {
	global $DBURL, $DBID, $DBPW, $DBNAME;
	$con = mysqli_connect($DBURL,$DBID,$DBPW,$DBNAME);
	mysqli_query($con,"SET NAMES 'utf8'");
	$qres = mysqli_query($con,$sql);
	if($qres == false) {
		echo mysqli_error($con);
		return;
	}
	$result = array();
	while($row = mysqli_fetch_array($qres)) {
		array_push($result,$row);
	}
	return $result;
}

function sqlRun($sql) {
	global $DBURL, $DBID, $DBPW, $DBNAME;
	$con = mysqli_connect($DBURL,$DBID,$DBPW,$DBNAME);
	mysqli_query($con,"SET NAMES 'utf8'");
	$qres = mysqli_query($con,$sql);
	if($qres == false) {
		echo mysqli_error($con);
		return;
	}
}

function getRandomFrom($a,$not) {
	$res = "";
	do {
		$res = $a[rand(0,count($a) - 1)];
	} while($a == $not);
	return $res;
}

function generate($f,$times) {
	$result = array();
	for($x = 0; $x < $times; $x++) {
		echo 'here';
		array_push($result,$f());
	}
	return $result;
}
?>
