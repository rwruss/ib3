<?php

/*
This script gives a menu for transfering control of a unit to another player
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Confirm that you are the owner of the unit
$thisUnit = loadUnit($postVals[1], $unitFile, 400);
if ($thisUnit->get('owner') != $gGameID) exit('error 1-5111');

// Get a list of players to transfer the unit to (run the chain of player's lords)
$thisPlayer = loadPlayer($pGameID, $unitFile, 400);
$lordID = $thisPlayer->get('lordID');
while ($lordID > 0) {
	echo 'Assign to player #'.$lordID;
	
	$nextLord = loadPlayer($lordID, $unitFile, 400);
	$lordID = $nextLord('lordID');
}


fclose($unitFile);
fclose($slotFile);

?>