<?php

include('./slotFunctions.php');

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');

// Get plot Data
fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile));

// Look up chars affiliated with the task
$plotChars = new itemSlot($plotDat[11], $slotFile, 40);

// Get player Data
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerData = unpack('i*', fread($unitFile, 400));

// Load list of player chars
$playerChars = new itemSlot($playerData[19], $slotFile, 40);

$hideDtl = true;
for ($i=1; $i<=sizeof($plotChars->slotData); $i++) {
	if (array_search($plotChars->slotData[$i], $playerChars->slotData)) {
		// Player controls a char involved in the plot and can view the details
		$hideDtl = false;
		echo 'Details on this plot....';
		
		// Show basics of the plot (type, start time, progress, etc....)
		
		// Look up information that you have about this plot....
		
		// Show the known informaiton on this plot
		
	}
}

if ($hideDtl) {
	echo 'You have no information on this plot';
}

fclose($taskFile);
fclose($unitFile);
fclose($slotFile);
?>