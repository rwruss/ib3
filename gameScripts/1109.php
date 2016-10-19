<?php

echo 'File 1109 is no long in use and should not be referenced';

/*
include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load the data for the target player
$trgPlayer = loadPlayer($postVals[1], $unitFile, 400);

// Load your diplomacy infomration
$thisPlayer = loadPlayer($pGameId, $unitFile, 400);

// Load your intelligence information
$intelSlot = new blockSlot($thisPlayer->get('intelSlot'), $slotFile, 40);
print_r($intelSlot->slotData);


for ($i=1; $i<=sizeof($intelSlot->slotData); $i+=5) {
	if ($intelSlot->slotData[$i] == $postVals[1]) {
		echo 'Intel found';
	}
}

echo '<script>
	giveOpt = textBlob("", "rtPnl", "Drop Resources");
	dropOpt.addEventListener("click", function() {scrMod("1105,'.$postVals[1].'")});
	addOpt = textBlob("", "rtPnl", "Add/Drop Units");
	addOpt.addEventListener("click", function() {scrMod("1107,'.$postVals[1].'")});
</script>';
///This is the diplomacy used in 1008
/*
// Get overall diplomatic statuses
$diplSlot = new blockSlot($playerDat[23], $slotFile, 40);

// Get list of wars player is involved in
$warList = new itemSlot($playerDat[32], $slotFile, 40);

// Build diplomacy tree and history
$dipTree = [];
for ($i=3; $i<=sizeof($diplSlot->slotData); $i+=3) {
	$dipTree[$dipSlot->$slotData[$i]][] = $dipTree[$dipSlot->$slotData[$i+1]]; // Action #
	$dipTree[$dipSlot->$slotData[$i]][] = $dipTree[$dipSlot->$slotData[$i+2]]; // Time
}

foreach ($dipTree as $trgID => $action) {
	echo 'Action '.$action[0].' with faction '.$trgID.' at time '.$action[1].'<br>';
}

foreach ($warList->slotData as $warID) {
	fseek($unitFile, $warID*$defaultBlockSize);
	$warDat = unpack('i*', fread($unitFile, 100));
	
	echo 'warDetail('.$warID.')';
}
*/
/*
fclose($slotFile);
fclose($unitFile);
*/
?>