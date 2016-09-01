<?php

/*
Process a player purchasing a mercenary unit
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$mercFile = fopen($gamePath.'/mercenaries.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load transacation informatino
fseek($mercFile, $postVals[1]*100);
$thisTrade = unpack('i*', fread($mercFile, 100));

// Confirm that the purchasing player has the required resources
$thisPlayer = loadPlayer($pGameID, $unitFile, 400);
$thisHomeCity = loadUnit($thisPlayer->get('homeCity'), $unitFile, 400);
$thisRsc = new itemSlot($thisHomeCity->get('carrySlot'), $slotFile, 40);
for ($i=1; $i<sizeof($thisRsc->slotData); $i+=2) {
	
}


// Confirm that the unit is not in an army
$trgUnit = loadUnit($postVals[1], $unitFile, 400);
if ($trgUnit->get('armyID') > 0) exit('Can\'t sell a unit that is in an army');

// Change the controller in the unit's information
$trgUnit->save('controller', $pGameID);

// Add the unit to the new controller's unit list
$playerUnits = new itemSlot($thisPlayer->get('unitSlot'), $slotFile, 40);
$playerUnits->addItem($postVals[1], $slotFile);

fclose($slotFile);
fclose($mercFile);
fclose($unitFile);

?>