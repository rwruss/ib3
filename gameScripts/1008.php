<?php

require_once("./slotFunctions.php");
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
for ($i=3; $i<=sizeof($diplSlot->slotData); $i+=3) {
	$dipTree[$dipSlot->$slotData[$i]][] = $dipTree[$dipSlot->$slotData[$i+1]]; // Action #
	$dipTree[$dipSlot->$slotData[$i]][] = $dipTree[$dipSlot->$slotData[$i+2]]; // Time
}

foreach ($dipTree as $trgID => $action) {
	echo 'Action '.$action[0].' with faction '.$trgID.' at time '.$action[1].'<br>';
}

$warFile = fopen($gamePath.'/wars.war', 'rb');
foreach ($warList->slotData as $warID) {
	if ($warID > 0) {
		fseek($warFile, $warID*$defaultBlockSize);
		$warDat = unpack('i*', fread($warFile, 100));

		echo '<p>War '.$warID.' - Player '.$warDat[5].' vs '.$warDat[6].'<br>';
		switch ($warDat[1]) {
			case 0:
				echo 'War for no reason!';
				break;

			case 1:
				echo 'War for no reason!';
				break;

			case 2:
				echo 'War for no religious conversion!';
				break;

			case 3:
				echo 'War for no land/towns!';
				break;

			case 4:
				echo 'War for conquest';
				break;
		}
	echo '<p>Score is '.$warDat[8].'<br>';
	echo '<span onclick=scrMod("1126,'.$warID.',1")>Surrender</span>, <span onclick=scrMod("1125,'.$warID.',2")>Negotiate</span>, <span onclick=scrMod("1126,'.$warID.',3")>Enforce Demands</span>';
	}
}
fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>
