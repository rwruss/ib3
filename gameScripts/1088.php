<?php

echo 'Leave a plot #'.$postVals[1];

// Remove plot from players list of plots
include('./slotFunctions.php');

//$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, 200));

$plotSlot = new itemSlot($playerDat[20], $slotFile, 40);

print_r($plotSlot->slotData);
$loc = array_search($postVals[1], $plotSlot->slotData);
echo "Delete at ".$loc;
if ($loc) $plotSlot->deleteItem($loc, $slotFile);
fclose($unitFile);
fclose($slotFile);
?>
