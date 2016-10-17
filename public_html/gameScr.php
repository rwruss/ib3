<?php

$defaultBlockSize = 100;
$unitBlockSize = 400;
$jobBlockSize = 200;
$warBlockSize = 200;
session_start();
$gameID = $_GET['gid'];
if ($gameID != $_SESSION['instance']) {echo "<script>alert('Game mismatch')</script>";exit;}
if (!isset($_SESSION['gameIDs'][$gameID])) echo "<script>window.location.replace('./index.php')</script>";

if (!isset($_SESSION['game_'.$gameID])) {
	$paramFile = fopen('../games/'.$gameID.'/params.ini', 'rb');
	$params = unpack('i*', fread($paramFile, 100));
	$_SESSION['game_'.$gameID]['scenario'] = $params[9];
	$_SESSION['game_'.$gameID]['scenario'] = 1;
	$_SESSION['game_'.$gameID]['culture'] = 1; // Set and record player culture
	fclose($paramFile);
}
$pGameID = $_SESSION['gameIDs'][$gameID];
$postVals = explode(",", $_POST['val1']);

$inputValidate = TRUE;
foreach ($postVals as $value) {
	if (!is_numeric ($value) || $value < 0) $inputValidate = FALSE;
}
$gamePath = "../games/".$gameID;
$scnPath = "../scenarios/".$_SESSION['game_'.$gameID]['scenario'];
if ($inputValidate) {

	include("../gameScripts/".$postVals[0].".php");
} else {
	if ($postVals[0] > 3000) {
		include("../gameScripts/".$postVals[0].".php");
	} else {
	echo 'Validation error';
	print_r($postVals);
	}
}

function read_slot($file, $slot_num, $slot_size)
	{
	$next_slot = $slot_num;
	$units_a = array();
	//echo "Next slot = ".$next_slot."<br>";
	while ($next_slot > 0)
		{
		fseek($file, $next_slot*$slot_size);
		$slot_dat = fread($file, $slot_size);
		$units_a = array_merge($units_a, unpack("N*", substr($slot_dat, 4)));

		$slot_check = unpack("N", $slot_dat);
		$next_slot = $slot_check[1];
		//echo "Next Slot: ".$next_slot;
		}
	return $units_a;
	}
/*
function sendMessage($mOpt, $content, $toList) {
	include("../gameScripts/msg/messageSend.php");
}*/
?>
