<?php

include("./slotFunctions.php");

// look up player information to get slot IDS
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*400);
$playerDat = unpack('i*', fread($unitFile, 400));

// Read the character slot for the player
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerDat[19], 40)));

print_r($unitList);
echo '<hr>';

foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*400);
	$unitDat = unpack('i*', fread($unitFile, 400));
	echo '<div class="charDetail" onclick="makeBox(\'unitDetail\', \'1002,'.$unitID.'\', 500, 500, 200, 50);">Unit #'.$unitID.' found @ '.$unitDat[1].', '.$unitDat[2].'</div>';
	//print_r($unitDat);
}


fclose($slotFile);
fclose($unitFile);
?>