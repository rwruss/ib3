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
		$target = $plotDat[8];
		fseek($unitFile, $target*$defaultBlockSize);
		$targetDat = unpack('i*', fread($unitFile, 200));
		echo 'Details on this plot....<script>
		plotSummary();
		progressBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		charBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		otherBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		unitList.newUnit({unitID : '.$targetDat.', unitType : "character", actionPoints : 50, status : 1, unitName : "unit name", exp : 500});
		unitList.renderSum(1, document.getElementById("plot_'.$postVals[1].'_targets"));
		document.getElementById("plot_'.$postVals[1].'progress").innerHTML = "'.$plotDat[6].'"
		';
	break;
	}
}

if ($hideDtl) {
	// Show plot object but only fill with known items
	echo '<script>
		plotSummary();
		progressBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		charBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		otherBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		';
}

// Look up intel you have about this plot
$intelFile = fopen($gameScr.'/plotIntel.dat', 'rb');
$plotIntel = new itemSlot($playerData[34], $intelFile, 404);
for ($i=1; $i<=sizeof($plotIntel->slotData); $i+=5) {
	if ($plotIntel->slotData[$i] == $postVals[1]) {
		switch ($plotIntel->slotData[$i+1]) {
			case 1: // Character involved
				// get char detail
				fseek($unitFile, $plotIntel->slotData[$i+2]*$defaultBlockSize);
				$charDat = unpack('i*', fread($unitFile, 200));
				echo 'unitList.newUnit({unitID : '.$plotIntel->slotData[$i+2].', unitType : "warband", actionPoints : 50, status : 1, unitName : "unit name", exp : 500});
				unitList.renderSum(1, charBox);';
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
fclose($taskFile);
fclose($unitFile);
fclose($slotFile);
?>