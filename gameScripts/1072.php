<?php

include('./slotFunctions.php');
include('./unitClass.php');

echo 'Join battle '.$postVals[1].' on side '.$postVals[2];

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');

$baseUnit = new warband($_SESSION['selectedUnit'], $unitFile, 400);
$thisBattle = new battle($postVals[1], $unitFile, 100);

// Verify that unit can join battle

// Update unit status and save the battle ID to the unit
$baseUnit->set('status', 2);
$baseUnit->set('battleID', $postVals[1]);

// Remove the unit from the map
$mapSlotNum = floor($baseUnit->get('yLoc')/120)*120+floor($baseUnit->get('xLoc')/120);
$mapSlotItem = new itemSlot($mapSlotNum, $mapSlotFile, 404);
$location = array_search($_SESSION['selectedUnit'], $mapSlotItem->slotData);
if ($location) {
	$mapSlotItem->deleteItem($location, $mapSlotFile);
}

// Add the unit to the battle list for the selected side
$battleList = new itemSlot($thisBattle->get('sideList_1'), $slotFile, 40);
$battleList->addItem($_SESSION['selectedUnit'], $slotFile);

fclose($unitFile);
fclose($slotFile);
fclose($mapSlotFile);
?>