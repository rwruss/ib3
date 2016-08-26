<?php

/*
Process a player purchasing a mercenary unit
*/

include('./unitClass.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$mercFile = fopen($gamePath.'/mercenaries.dat', 'rb');

// Load transacation informatino
fseek($mercFile, $postVals[1]*100);
$thisTrade = unpack('i*', fread($mercFile, 100));

// Change the controller in the unit's information

// Add the unit to the new controller's unit list

fclose($mercFile);
fclose($unitFile);

?>