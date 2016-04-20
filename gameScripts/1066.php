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
		
		// Remove from the list of child citys for the parent city
		$childCityList = new itemSlot($parCityDat[30], $slotFile);
		$target = array_search($_SESSION['selectedItem'], $childCityList->slotData);
		if ($target) $childCityList->deleteItem($target, $slotFile);
	}
	
	// Change the child city to a resource cart/moving group object
	
	/// Save location of the unit to match that of the city and adjust unit types
	fseek($unitFile, $_SESSION['selectedItem']*$defaultblocksize);
	fwrite($unitFile, pack('i*', $parCityDat[1], $parCityDat[2], 10, 10));
	
	/// Update last change time
	fseek($unitFile, $_SESSION['selectedItem']*$defaultblocksize+104);
	fwrite($unitFile, pack('i', time()));
	
	/// Add to list of military units for this player
	fseek($unitFile, $pGameID*$defaultblocksize);
	$playerDat = unpack('i*', fread($unitFile, 400));
	
	$unitList = new itemSlot($playerDat[22], $slotFile, 40); // start, file, size
	$unitList->addItem($_SESSION['selectedItem'], $slotFile);
}

fclose($slotFile);
fclose($unitFile);
?>