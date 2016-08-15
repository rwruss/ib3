<?php

// Menu for depositing resources held by a unit or army into a town.

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

//Load the unit
$selectedUnit = loadUnit($postVals[1], $unitFile, 400);

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