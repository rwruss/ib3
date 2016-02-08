<?php
include("./slotFunctions.php");
$_SESSION['selectedChar'] = $postVals[1];
//echo 'Character details for char # '.$postVals[1];

// Verify if this person is authorized to view the character details
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*400);
$charDat = unpack('i*', fread($unitFile, 400));

if ($charDat[5] == $pGameID) {
	//print_r($charDat);
	include('../gameScripts/1002a.php');
	}
	else if ($charDat[6] == $pGameID) {
		// Show character info for the controller
		include('../gameScripts/1002c.php');
	} else {
	echo 'Show non owner details';
	include('../gameScripts/1002b.php');
}
?>