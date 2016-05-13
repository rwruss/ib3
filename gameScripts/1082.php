<?php

include('./slotFunctions.php');

$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');

// Record chatacter in list of plot chars
fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile, 200));

// check if character is already in the plot

if ($plotDat != 0) {
  $plotChars = new blockSlot($plotDat[11], $slotFile, 40);
  if (array_search($_SESSION['selectedItem'], $plotChars->slotData)) {
    echo 'Already in the plot';
    exit;
  }
}

if ($plotDat[11] == 0) {
  $plotDat[11] = startASlot($slotFile, $gamePath.'/gameSlots.slt');
  fseek($taskFile, $postVals[1]*200+40);
  fwrite($taskFile, pack('i', $plotDat[11]));
}

echo 'Char slot for plot is '.$plotDat[11].'<br>';

$plotChars = new blockSlot($plotDat[11], $slotFile, 40);
$loc = sizeof($plotChars->slotData);
for ($i=1; $i<=$plotChars->slotData; $i+=2) {
  if ($plotChars->slotData[$i]==0) {
    $loc = $i;
    break;
  }
}
$plotChars->addItem($slotFile, pack('i*', $_SESSION['selectedItem'], 1), $loc);


echo 'Added character to plot at slot '.$plotDat[11];

// Record plot in list of char plots
fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, 200));

echo 'Record plot in slot '.$unitDat[35].' for the unit';
if ($unitDat[35] == 0) {
  $unitDat[35] = startASlot($slotFile, $gamePath.'/gameSlots.slt');
  fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize+136);
  fwrite($unitFile, pack('i', $unitDat[35]));
}
$charPlots = new itemSlot($unitDat[35], $slotFile, 40);
$charPlots->addItem($postVals[1], $slotFile);


fclose($slotFile);
fclose($unitFile);
fclose($taskFile);

?>
