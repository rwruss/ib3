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
$intelFile = fopen($gamePath.'/intel.slt', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Search for intel relating to the target item
$intelList = new itemSlot($thisPlayer->get('intelSlot'), $intelFile, 40);

// Display any found intel relating to the target item
for ($i=1; $i<sizeof($intelList->slotData); $i+=3) {
	if ($intelList->slotData[$i] == )
}

fclose($slotFile);
fclose($intelFile);

?>
