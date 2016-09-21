<?php

include('./unitClass.php');
include('./slotFunctions.php');

class intel {
	function __construct($trgID, $params) {
}

function intelFactory($type, $params) {
	switch($value) {
		default:
		$intelItem = new intel($trgID, $type, $value, $time, $reportingChar);
		break;
	}
	
	return $intelItem;
}

$thisPlayer = loadPlayer($pGameID, $unitFile, 400);

// Review intel on an object
echo 'Intel on this object ('.$intelTrg.')
<div style="position:absolute; bottom:40; left:0;" onclick="scrMod(\'1097,'.$intelTrg.'\');">Message</div>';

// Load Intel file and read intel slot for the player
$intelFile = fopen("", "rb");

// Search for intel relating to the target item
$intelDat = new itemSlot($thisPlayer->get('intelSlot'), $intelFile, 40);

// Display any found intel relating to the target item
$i=1;
while ($i<=sizeof($intelDat->slotData) {
	if ($intelDat->slotData[$i+1] == $intelTrg) {
		$reportLength = $intelDat->slotData[$i];
		$pos = 4;
		while ($pos<$reportLength) {
			intelFactory()
		}
	}
}

fclose($intelFile);

?>
