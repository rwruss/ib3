<?php

// Assign a ringleader to a plot - process

include('./slotFunctions.php');

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');

// Confirm that player is the controller for this plot
fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile, 200));
if ($plotDat[9] == $pGameID) {

	// Confirm that player is the controller for this character
	fseek($unitFile, $postVals[2]*200);
	$unitDat = unpack('i*', fread($unitFile, 200));
	if ($unitDat[6] == $pGameID) {

	// Record the character as the new plot leader
	fseek($taskFile, $postVals[1]*200+32);
	fwrite($taskFile, pack('i', $postVals[2]);
	}
}

fclose($unitFile);
fclose($taskFile);
fclose($slotFile);

?>