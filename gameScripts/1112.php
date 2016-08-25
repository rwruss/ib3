<?php

/* This script processes disbanding an army*/

include('./slotFunctions.php');
include('./unitClass.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load army data
$trgArmy = loadUnit($postVals[1], $unitFile, 400);

// Confirm that the player can disband the army
if ($trgArmy->get('controller') != $pGameID) exit('error 1-2111');

// Load list of units in army
$armyList = new itemSlot($trgArmy->get('unitListSlot'), $slotFile, 400);

// Set the units to not be in any army
for ($i=1; $i<=sizeof($armyList->slotData); $i++) {
	if ($armyList->slotData[$i] > 0) {
		$trgUnit = loadUnit($armyList->slotData[$i], $unitFile, 400);
		
		$trgUnit->save('armyID', 0);
	}
}

// Remove the army from the list of player's units
$playerObj = loadPlayer($pGameID, $unitFile, 400);

$unitList = new itemSlot($playerObj->get('unitSlot'), $slotFile, 400);
$unitList->deleteByValue($postVals[1], $slotFile);

fclose($unitFile);
fclose($slotFile);

?>