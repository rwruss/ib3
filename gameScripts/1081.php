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
		$intelFile = fopen($gameScr.'/plotIntel.dat', 'rb');
		$plotIntel = new itemSlot($playerData[34], $intelFile, 404);
		for ($i=1; $i<=sizeof($plotIntel->slotData); $i+=5) {
			if ($plotIntel->slotData[$i] == $postVals[1]) {
				switch ($plotIntel->slotData[$i+1]) {
					case 1: // Character involved
						echo 'Character #'.$plotIntel->slotData[$i+2].' is reported to be involved';
						break;
						
					case 2: // Total Progress
						echo 'Progress of '.$plotIntel->slotData[$i+2].' is reported by character '.$plotIntel->slotData[$i+3].' at '.$plotIntel->slotData[$i+4];
						break;
						
					case 3: // Target
						break;
						
					case 4: // Founder 
						break;
				}
			}
		}
		
		fclose($intelFile);
		
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