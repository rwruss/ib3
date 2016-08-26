<?php

/*
process creating an army with a unit or combination of units and armies.
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load unit infrmation
$thisUnit = loadUnit($postVals[1], $unitFile, 400);

// Load target unit information
$trgUnit = loadUnit($postVals[2], $unitFile, 400);

if ($trgUnit->get('uType') == 2) {
	// Add to an existing army
	
	// Record army id in unit information
	$thisUnit->save('armyID', $postVals[2]);
	
	// Record unit id in list of army units
	$unitList = new itemsSlot($trgUnit->get('unitListSlot'), $slotFile, 40);
	$unitList->addItem($postVals[1], $slotFile);
}
else if ($trgUnit->get('uType') == 6) {
	// Combine with an existing unit to create a new army
	
	// Create a new army
	$newArmy = newUnit(2, $unitFile);
	
	// Create an item List for the army
	$newArmy->save('unitListSlot', newSlot($slotFile));
	$armyUnits = new itemSlot($newArmy->get('unitListSlot'));
	
	// Record both unit IDs in the army unit list
	$armyUnits->addItem($postVals[1], $slotFile);
	$armyUnits->addItem($postVals[2], $slotFile);
	
	// Record army ID in both unit informations
	$thisUnit->save('armyID', $newArmy->get('unitListSlot'));
	$trgUnit->save('armyID', $newArmy->get('unitListSlot'));
}


fclose($unitFile);
fclose($slotFile);

?>