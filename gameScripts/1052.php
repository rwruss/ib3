<?php

include('./slotFunctions.php');
include('./unitClass.php');
// Process training the unit type selected.

$unitType = $postVals[2];

// Load information for the building producing the unit
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[3]*$defaultBlockSize);
$bldgDat = unpack('i*', fread($unitFile, 200));

$bldgDesc = explode('<->', file_get_contents($scnPath.'/buildings.desc'));
$bTypeDesc = explode('<-->', $bldgDesc[$bldgDat[10]]);
$divisor = max(1,$bldgDat[17]);
$actionPoints = min(1000, $bldgDat[16] + floor((time()-$bldgDat[27])/$divisor));

// Check if slots are available in the production queue
print_r($bldgDat);
$queueSpot = false;
for ($i=0; $i<$bTypeDesc[7]; $i++) {
	echo 'Check slot '.$i;
	if ($bldgDat[$i+18] == 0)	{
		$queueSpot = $i+18;
		break;
	}
}
if ($queueSpot) {
	// Load the unit dat from the parameters file
	$unitDesc = explode('<->', file_get_contents($scnPath.'/units.desc'));
	$uTypeDesc = explode('<-->', $unitDesc[$unitType]);
	print_r($uTypeDesc);

	// Verify that the building can create this unit type

	// Verify that the building prerequisites for this unit are met

	// Verify that the resource prerequisites for this unit are met

	// Get player information for slot
	fseek($unitFile, $pGameID*$defaultBlockSize);
	$playerDat = unpack('i*', fread($unitFile, 200));

	// Get city information for where the unit is being trained
	fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
	$cityDat = unpack('i*', fread($unitFile, 400));

	// create a unit from template dat
	$templateFile = fopen($scnPath.'/charTemplates.dat', 'r+b');
	$newUnit = loadUnit($unitType*4, $templateFile, 400);
	fclose($templateFile);


	// Record specifics for this unit
	$typeDetails = explode(",", $uTypeDesc[1]);
	$newUnit->set("xLoc", $cityDat[1]);
	$newUnit->set("yLoc", $cityDat[2]);
	$newUnit->set("owner", $pGameID);
	$newUnit->set("controller", $pGameID);
	$newUnit->set("updateTime", time());
	$newUnit->set("troopType", $postVals[2]);
	$newUnit->unitDat[4] = $typeDetails[3];
	$newUnit->unitDat[19] = $uTypeDesc[2];

	// Need to get a new unit ID and save to that unit ID in the unit file
	if (flock($unitFile, LOCK_EX)) {  // acquire an exclusive lock
		fseek($unitFile, 396, SEEK_END);
		fwrite($unitFile, pack('i', 0));
		$size = ftell($unitFile);
		$newID = $size/$defaultBlockSize-4;
		fflush($unitFile);
		flock($unitFile, LOCK_UN); // release the lock  on the player File
	}
	$newUnit->changeID($newID);

	// add the unit to the list of units for this player
	/*
	$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
	addDataToSlot($gamePath.'/gameSlots.slt', $playerDat[22], pack('i', $newID), $slotFile);
	fclose($slotFile);
	*/
	$newUnit->saveAll($unitFile);

	// Record the unit in the queue spot for this building
	echo 'Record at '.$queueSpot.' in building '.$postVals[3];
	fseek($unitFile, $postVals[3]*$defaultBlockSize + ($queueSpot)*4-4);
	fwrite($unitFile, pack('i', $newID));

	echo $uTypeDesc[0].' started';
} else {
	echo 'No production spots available at this building';
}


fclose($unitFile);



?>
