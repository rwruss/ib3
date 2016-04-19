<?php

// Process a player leaving a city
/*
 - The player will keep his normal city ID but that city will be removed from the parent city (if there is one).
 - A resource cart unit will be created that will link the city items that are being transported.  This can be moved to a new location and settled as a new city (process a task)
 - Any non-transferrable builindgs are left behind at the old city (or a ruin locaiton) to decay
*/

include("./slotFunctions.php");

// Load current city data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $_SESSION['selectedItem']*$defaultblocksize);
$subCityDat = unpack('i*', fread($unitFile, 400));

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $subCityDat[19], 40)));
$approved = array_search($pGameID, $credList);
echo 'Approved level '.$approved.'<br>';

if ($approved) {

	if ($subCityDat[29] > 0) {
		fseek($unitFile, $subCityDat[29]*$defaultblocksize);
		$parCityDat = unpack('i*', fread($unitFile, 400));
	}
}

fclose($slotFile);
fclose($unitFile);
?>