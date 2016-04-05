<?php

include("./slotFunctions.php");
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');

// Get player information
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Get overall diplomatic statuses
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$diplSlot = new blockSlot($playerDat[23], $slotFile, 40);

// Get list of wars player is involved in
$warList = new itemSlot($playerDat[32], $slotFile, 40);

// Build diplomacy tree and history
$dipTree = [];
for ($i=1; $i<=sizeof($dipSlot->$slotData); $i+=2) {
	$dipTree[$dipSlot->$slotData[$i]][] = $dipTree[$dipSlot->$slotData[$i+1];
	$dipTree[$dipSlot->$slotData[$i]][] = $dipTree[$dipSlot->$slotData[$i+2];
}

foreach ($dipTree as $trgID => $action) {
	echo 'Action '.$action[0].' with faction '.$trgID.' at time '.$action[1].'<br>';
}

foreach ($warList->slotData as $warID) {
	fseek($unitFile, $warID*$defaultBlockSize);
	$warDat = unpack('i*', fread($unitFile, 100));
}

fclose($unitFile);
fclose($slotFile);

?>