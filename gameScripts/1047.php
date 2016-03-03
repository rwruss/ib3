<?php

include("./slotFunctions.php");
// Convert a city into a tribe

// Confirm that player is authorized to give this order
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, 400));


// Read tribes/players present at this city
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$tribeList = array_filter(unpack("N*", readSlotData($slotFile, $cityDat[14], 40)));

if (array_search($pGameID, $tribeList)) {
	// Remove the player from the citie's list of players
	removeFromSlot($slotFile, $cityDat[14], 40, $pGameID); //function removeFromSlot($file, $startSlot, $slot_size, $targetVal)
	
	if (sizeof($tribeList) == 1) {
		// This is the last player to leave the city - change it to abandonded.
	}
}
fclose($slotFile);

fclose($unitFile);

?>