<?php

/*
This script will process transfering units from one player to another
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load player Dat
$thisPlayer = loadPlayer($pGameId, $unitFile, 400);

// Load list of units that the player controls
$unitList = new itemSlot($thisPlayer->get('unitSlot'), $slotFile, 40);

// Load target player Dat

$trgPlayer = loadPlayer($postVals[1], $unitFile, 400);
// Load target unit slotFile
$trgSlot = new itemSlot($trgPlayer->get('unitSlot'), $slotFile, 40);

$pvSize = sizeof($postVals);

for ($i=2; $i<$pvSize; $i++) {
	$trgUnit = loadUnit($postVals[$i], $unitFile, 400);
	
	// confirm that unit is controlled by player
	if ($trgUnit->get('controller') == $pGameId) {
		// Change the unit's controller
		$trgUnit->save('controller', $postVals[1]);
		
		// Add to the unit slot for the new controller
		$trgSlot-addItem($postVal[$i], $slotFile);
	}
}

fclose($slotFile);
fclose($unitFiel);

?>