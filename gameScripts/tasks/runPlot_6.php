<?php

// Attempt and execution!

include('./slotFunctions.php');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load target data
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

$assMod = 1.0;
// Load executor data
if ($plotDat[14] > 0) {
	fseek($unitFile, $plotDat[14]*$defaultBlockSize);
	$assDat = unpack('i*', fread($unitFile, $unitBlockSize));
	
	// Search for assassin ability modifiers
	
}

// Load target data
fseek($unitFile, $plotDat[8]*$defaultBlockSize);
$trgDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Load target modifiers
$trgMod = 1.0;

// Proximity modifiers



// Calculate probability of success
$chanceLvl = $plotDat[6]*$assMod/($trgMod*1000);
$chance = (1-1/sqrt($chanceLvl))*1000;

$dieRoll = rand(0,1000);
$killResult = 1;
$notifyTrg = false;
if ($dieRoll >= $chance) {
	// Attempt was successful (target dead)
	$killResult = 2;
	// Kill the character
	fseek($unitFile, $plotDat[8]*$defaultBlockSize+24);
	fwrite($unitFile, pack('i', 2)); // he gone
	
	// Notify the controller and the owner	
	$notifyTrg = true;
	
}

// Check if the assasin is caught or plot is discovered
$catchChance = $trgMod*1000/($trgMod+$assMod);
$dieRoll = rand(0,1000);
$caughtResult = 1;
$closePlot = 1;
if ($dieRoll >= $catchChance) {
	// The assassin has been captured
	$caughtResult = 2;
	$closePlot = 2;
}
else if ($dieRoll >= $catchChane/1.5) {
	// Attempt is discovered but plotter not caught or identified
	$closePlot = 2;
}

// if plot discovered, notify the target
if ($notifyTrg) sendMessage([0, 0, time(), 2, 0, $postVals[1], $_SESSION['selectedItem'], $killResult, $caughtResult, $plotDat[14]], "", $trgDat[6]);

// Notify plotters of the result
// Generate list of plotting players
$tmpToList = [];
$plotChars = new itemSlot($plotDat[11], $slotFile, 40);
for ($i=0; $i<sizeof($plotChars); $i+=2) {
	fseek($unitFile, $plotChars[$i]*$defaultBlockSize);
	$charDat = unpack('i*', fread($unitFile, 100));
	$tmpToList[] = $charDat[6];
}
$msgToList[] = array_unique($tmpToList);
sendMessage([0, 0, time(), 3, 0, $postVals[1], $_SESSION['selectedItem'], $killResult, $caughtResult, $plotDat[14]], "", $msgToList);

// Record result and close the task/plot if needed
fseek($taskFile, $postVals[1]*200+8);
fwrite($taskFile, pack('i', $closePlot));

fseek($taskFile, $postVals[1]*200+56);
fwrite($taskFile, pack('i*', $killResult, $caughtResult, time()));

fclose($unitFile);
fclose($slotFile);

?>