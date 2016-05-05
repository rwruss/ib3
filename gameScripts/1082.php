<?php

include('./slotFunctions.php');

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile, 200);

$plotChars = new itemSlot($plotDat[11], $slotFile, 40);
$plotChars->addItem($_SESSION['selectedItem'], $slotFile);

echo 'Added character to plot';

?>