<?php

/*
This will convert a resource cart into a NEW permanant sub-city
*/
include('./cityClass.php');
include('./slotFunctions.php');


// Load the resource cart data (transported resources and buildings) and save to the players city slot (if not already linked)
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, 400));

$newCityId = 0;
// Create a new parent city ID and set parameters
if (flock($unitFile, LOCK_EX)) {
	fseek($unitFile, 0, SEEK_END);
	$size = ftell($unitFile);
	$newCityId = $size/$defaultBlockSize;

	fseek($unitFile, $size*$defaultBlockSize-4);
	fwrite($unitFile, pack('i', 0));

	echo 'New city aID is '.$newCityId.'<br>';
	$townInf = [$unitDat[1], $unitDat[2], $pGameID, $_SESSION['game_'.$gameID]['culture']];
	newTown($newCityId, $unitFile, $slotFile, $townInf); //($id, $townFile, $slotFile)

	flock($unitFile, LOCK_UN); // release the lock  on the player File
}


// Add the parent city ID to the current city
fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize+112);
fwrite($unitFile, pack('i', $newCityId));


// Remove the unit cart / other items from the map and change the mapslot to display the new city
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');
$mapSlotNum = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);
echo 'Mapo slot number is '.$mapSlotNum.'<br>';
$mapSlot = new itemSlot($mapSlotNum, $mapSlotFile, 404);
print_r($mapSlot->slotData);
$currentPos = array_search($_SESSION['selectedItem'], $mapSlot->slotData);
echo 'Current Pos: '.$currentPos.'<br>';
if ($currentPos) $mapSlot->deleteItem($currentPos, $mapSlotFile);
echo 'Add new city to map item List<br>';
$mapSlot->addItem($newCityId, $mapSlotFile);

// Add this player to the credential list for the city
echo 'Open city '.$newCityId.'<br>';
$thisCity = new city($newCityId, $unitFile);
$cityCredentials = new blockSlot($thisCity->cityData[19], $slotFile, 40);
$addLoc = array_search(0, $cityCredentials->slotData);
echo 'Add credintials to slot ('.$thisCity->cityData[19].')';
print_r($cityCredentials->slotData);
$cityCredentials->addItem($slotFile, pack('i*', -9, $pGameID), $addLoc);


// Change this cart object to a city object
fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize+12);
fwrite($unitFile, pack('i', 1));

fclose($unitFile);
fclose($slotFile);

?>
