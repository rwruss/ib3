<?php

include("./slotFunctions.php");
echo 'This is a battle that is waiting to take place';

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load list of units that the player controls
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));
$playerObj = new player($playerDat);

$unitList = array_filter(unpack("i*", readSlotData($slotFile, $playerObj->unitSlot, 40)));

$matchedSide = 0;
// Look for units that the player controls to determine which side they are on
$sideA = new itemSlot($unitDat[15], $slotFile, 40);
$sideB = new itemSlot($unitDat[16], $slotFile, 40);
fclose($slotFile);

for ($i=1; $i<=sizeof($unitList->slotData); $i++) {
	if (array_search($unitList->slotData[$i], $sideA->slotData)) $matchedSide = 1;
	else if (array_search($unitList->slotData[$i], $sideB->slotData)) $matchedSide = 2;
}

// Get information on the wars 
for ($i=0; $i<=5; $i++) {
	fseek($unitFile, $unitDat[17+$i]*$defaultBlockSize);
	$warDat = unpack('i*', fread($unitFile, 200));
	echo 'War #'.$unitDat[17+$i].'<br>';
}

echo '<div style="position:absolute; bottom:0; left:0;" onclick="scrMod(1094,'.$unitID.');">Battle Detail</div>';


?>