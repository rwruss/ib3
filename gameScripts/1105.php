<?php

// Menu for depositing resources held by a unit or army into a town.

include('./unitClass.php');
include('./cityClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

//Load the unit
$selectedUnit = loadUnit($postVals[1], $unitFile, 400);

// Look for nearby settlements that you can drop resources in
$mapSlot = floor($selectedUnit->get('yLoc')/120)*120+floor($selectedUnit->get('xLoc')/120);
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');
$gridList = new itemSlot($mapSlot, $mapSlotFile, 400);
for ($i=1; $i<=sizeof($gridList->slotData); $i+=2) {
	if ($gridList->slotData[$i] > 0) {
		$checkObj = loadUnit($gridList->slotData[$i], $unitFile, 400);
		// Check to see if unit is a city
		if ($checkObj->get('uType') == 2) {
			// Check to get city permissions
			$credList = array_filter(unpack("i*", readSlotData($slotFile, $checkObj->unitDat[19], 40)));
			$approved = checkCred($pGameID, $credList);
			
			if ($approved) {
				// display the place as an option to drop the stuff
				echo 'unitList.newUnit({unitType:"town", unitID:'.$gridList->slotData[$i].', unitName:"Town '.$gridList->slotData[$i].'", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitDat[4].'});
				unitList.renderSum('.$gridList->slotData[$i].', "rtPnl")';
			}
		}
	}
}
fclose($mapSlotFile);

// Load what the unit/army is carrying
$carryDat = new blockSlot($selectedUnit->get('carrySlot'), $slotFile, 40);

for ($i=1; $i<=sizeof($carryDat); $i+=2) {
	if ($carryDat[$i+1] > 0) {
		echo 'Rsc #'.$carryDat[$i].': '.$carryDat[$i+1];
	}
}

fclose($unitFile);
fclose($slotFile);

?>