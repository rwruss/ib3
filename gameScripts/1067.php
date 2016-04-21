<?php

/*
This will convert a resource cart into a NEW permanant sub-city
*/
include('./cityClass.php');
include("./slotFunctions.php");

// Load the resource cart data (transported resources and buildings) and save to the players city slot (if not already linked)
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultblocksize);
$unitDat = unpack('i*', fread($unitFile, 400));

$newCityId = 0;
// Create a new parent city ID and set parameters
if (flock($unitFile, LOCK_EX)) {
	fseek($unitFile, 0, SEEK_END);
	$size = ftell($unitFile);
	$newCityId = $size/$defaultblocksize;
	
	fseek($unitFile, $size*$defaultblocksize-4);
	fwrite($unitFile, pack('i', 0));
	
	function newTown($newCityId, $unitFile, $slotFile); //($id, $townFile, $slotFile)
	
	flock($unitFile, LOCK_UN); // release the lock  on the player File	
}


// Add the parent city ID to the current city
fseek($unitFile, $_SESSION['selectedUnit']*$defaultblocksize+112);
fwrite($unitFile, pack('i', $newCityId);


// Remove the unit cart / other items from the map and change the mapslot to display the new city
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'w+b');
$mapSlotNum = floor($startLocation[1]/120)*120+floor($startLocation[0]/120);

$mapSlot = new itemSlot($mapSlotNum, $mapSlotFile, 404);
$currentPos = array_search($_SESSION['selectedUnit'], $mapSlot->slotData);
$mapSlot->deleteItem($currentPos, $mapSlotFile);

$mapSlot->addItem($newCityId, $mapSlotFile);

// Add this player to the credential list for the city
$thisCity = new city($newCityId, $unitFile);
$cityCredentials = new blockSlot($thisCity->cityData[19], $slotFile, 40);
$addLoc = array_search(0, $cityCredentials->slotData);
$cityCredentials->addItem($slotFile, pack('i*', -9, $pGameID), $addLoc);


// Change this cart object to a city object
fseek($unitFile, $_SESSION['selectedUnit')*$defaultblocksize+12);
fwrite($unitFile, pack('i', 1));

fclose($unitFile);

?>