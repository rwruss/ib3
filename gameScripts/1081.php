<?php

include('./slotFunctions.php');

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');

// Get plot Data
fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile, 200));
//print_r($plotDat);

// Look up chars affiliated with the task
$plotChars = new itemSlot($plotDat[11], $slotFile, 40);

// Get player Data
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerData = unpack('i*', fread($unitFile, 400));

// Load list of player chars
$playerChars = new itemSlot($playerData[19], $slotFile, 40);

$hideDtl = true;
echo 'Slot '.$plotDat[11].' chars';
//print_r($plotChars->slotData);
if (array_search($_SESSION['selectedItem'], $plotChars->slotData)) {
//for ($i=1; $i<=sizeof($plotChars->slotData); $i++) {

	// Player controls a char involved in the plot and can view the details

	$hideDtl = false;
	$target = $plotDat[8];
	fseek($unitFile, $target*$defaultBlockSize);
	$targetDat = unpack('i*', fread($unitFile, 200));
	echo 'Details on this plot....('.$postVals[1].').  Selected char '.$_SESSION['selectedItem'].', Leader char '.$plotDat[9].'<script>
	var plotBox = plotSummary({desc: "plot #'.$postVals[1].'", id:'.$postVals[1].'}, document.getElementById("plotDtlContent"));
	trgBox = addDiv("charBox", "tdHolder", plotBox);
	unitList.newUnit({unitID : '.$target.', unitType : "character", actionPoints : 50, status : 1, unitName : "unit name", exp : 500});
	unitList.renderSum('.$target.', plotBox.children[1]);

	//document.getElementById("plot_'.$postVals[1].'progress").innerHTML = "'.$plotDat[6].'";
	buttonBox = addDiv("", "fullBar", plotBox);
	scrButton("1087", buttonBox, "Leave Plot");
	scrButton("1084", buttonBox, "10%");
	scrButton("1084", buttonBox, "25%");
	scrButton("1084", buttonBox, "50%");
	boxButton("1084", buttonBox, "100%");';

	if ($plotDat[9] == $pGameID) {
		echo 'scrButton("1086", buttonBox, "Execute");
		boxButton("1085,'.$postVals[1].'", buttonBox, "ringleader");';
	}

	echo '</script>
	work options....';
}

if ($hideDtl) {
	// Show plot object but only fill with known items
	echo 'Details:<script>
		plotSummary();
		progressBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		charBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		otherBox = addDiv("", "tdHolder", document.getElementById("plotContent"));
		</script>';
}

// Look up intel you have about this plot
$intelFile = fopen($gamePath.'/plotIntel.dat', 'rb');
$plotIntel = new itemSlot($playerData[34], $intelFile, 404);
for ($i=1; $i<=sizeof($plotIntel->slotData); $i+=5) {
	if ($plotIntel->slotData[$i] == $postVals[1]) {
		switch ($plotIntel->slotData[$i+1]) {
			case 1: // Character involved
				// get char detail
				fseek($unitFile, $plotIntel->slotData[$i+2]*$defaultBlockSize);
				$charDat = unpack('i*', fread($unitFile, 200));
				echo 'unitList.newUnit({unitID : '.$plotIntel->slotData[$i+2].', unitType : "warband", actionPoints : 50, status : 1, unitName : "unit name", exp : 500});
				unitList.renderSum(1, charBox.children[2]);';
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
