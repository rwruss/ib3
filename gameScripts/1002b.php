<?php

echo 'Non owner details for a character';

// Load player Dat to get intelligence slot
fseek($unitFile, $pGameID*400);
$playerDat = unpack('i*', fread($unitFile, 400));

$playerObj = new player($playerDat);

// Load intelligence to see if you know anything about this character
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$intelDat = array_filter(unpack("i*", readSlotData($slotFile, $playerObj->intelSlot, 40)));

echo 'Details for character named '.$charDat[19].' '.$charDat[20].'<br>';

$intelSize = sizeof($intelDat)/3;
for ($i=0; $i<$intelSize; $i++) {
	if ($intelDat[$i*3] == $postVals[1]) {
		echo 'Intel of type '.$intelDat[$i*3+1].' with a value of '.$intelDat[$i*3+2].' at time of '.$intelDat[$i*3+3].' by character '.$intelDat[$i*3+4];
	}
}

?>