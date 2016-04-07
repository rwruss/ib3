<?php

echo 'Process joining war '.$postVals[1];

// Get player info
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Get list of wars that players is in
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$warList = new itemSlot($playerDat[32], $slotFile, 40);

$warList.addItem($postVals[1], $slotFile, $gamePath.'/gameSlots.slt');
$warList.save($slotFile);

fclose($slotFile);
fclose($unitFile);

?>