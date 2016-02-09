<?php

include("./slotFunctions.php");

// Load building Names
$bldgNames = explode('<-->', file_get_contents($gamePath.'/buildings.desc'));
$rscNames = explode('<-->', file_get_contents($gamePath.'/resources.desc'));

// Load building information
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*400);
$bldgDat = unpack('i*', fread($unitFile,400);

// confirm that the user is the owner of the building and/or authorized to view information
fseek($unitFile, $bldgDat[12]*400);
$cityDat = unpack('i*', fread($unitFile, 400));

$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);

if ($approved != false) {
	$credLevel = $credList[$approved-1]*(-1);
} else {
	$credLevel = 0;
}

if ($credLevel > 0) {
	echo 'Building Type: '.$bldgNames[$bldgDat[10]*7+5].'<br>
	Producing '.$bldgDat[21].'<br>';
	
	$unitNameFile = fopen($gamePath./'unitNames.txt');
	// Garrison:
	$garrisonList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
	foreach($garrisonList as $garID) {
		fseek($unitFile, $garID*400);
		$garDat = unpack('i*', fread($unitFile, 400));
		fseek($unitNameFile, $garDat[16]*20);
		// Show Icon for unit type
		echo 'Unit Type '.$garDat[10].'<br>
		Size: '.$garDat[14].'<br>
		Name: '.fread($unitNameFile, 20).'<hr>';
	}
	fclose($unitNameFile);
} else {
	echo 'you are not authorized to view this building.  You may send a scout to try to gather information.
	<div onclick="makeBox(\'scoutOrdes\', 1038, 500, 500, 200, 50)">Send Scout</div>';
}

?>