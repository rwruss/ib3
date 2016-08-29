<?php

/*
Process putting a unit up for sale as a mercentary
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$mercFile = fopen($gamePath.'/mercenaries.dat', 'rb');

print_R($postVals);

for ($i=0; $i<5; $i++) {
  echo 'Require '.$postVals[$i*4+5].' of resource '.$postVals[$i*4+3];
}
echo 'Sell for period of '.$postVals[23];
// confirm that the player owns the unitClass
$thisUnit = loadUnit($postVals[1], $unitFile, 400);


// confirm that the unit is saaaaleable

// Create a new unit sale in the merc file and record parameters

fclose($unitFile);
fclose($slotFile);
fclose($mercFile);

?>
