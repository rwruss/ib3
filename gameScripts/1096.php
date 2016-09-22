<?php

include('./unitClass.php');
include('./slotFunctions.php');

class intel {
	function __construct($trgID, $params) {
}

function intelFactory($type, $params) {
	switch($value) {
		default:
		$intelItem = new intel($reportData);
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
$noInfo = true;
for ($i=1; $i<sizeof($intelList->slotData); $i+=3) {
	if ($intelList->slotData[$i] == $intelTrg) {
		fseek($intelFile, $intelList->slotData[$i+1]);
		$reportData = unpack('i*', fread($intelFile, $intelList->slotData[$i+2]));
	}
	$noInfo = false;
}

if ($noInfo) {
	echo "No intel on this object";
}

fclose($slotFile);
fclose($intelFile);

?>
