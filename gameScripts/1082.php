<?php

include('./slotFunctions.php');

$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');

// Record chatacter in list of plot chars
fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile, 200));

// check if character is already in the plot

if ($plotDat[11] != 0) {
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

// Record plot in list of plaayer plots
fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, 200));

//$plotChars->addItem($slotFile, pack('i*', $_SESSION['selectedItem'], 1), $loc);
$plotChars->addItem($slotFile, pack('i*', $pGameID, 1), $loc);

//echo 'Added character to plot at slot '.$plotDat[11];
fseek($unitFile, $unitDat[6]*$defaultBlockSize);
$controllerDat = unpack('i*', fread($unitFile, 200));

echo 'Record plot in slot '.$unitDat[35].' for the unit';
if ($controllerDat[20] == 0) {
  $controllerDat[20] = startASlot($slotFile, $gamePath.'/gameSlots.slt');
  fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize+136);
  fwrite($unitFile, pack('i', $controllerDat[20]));
}
$charPlots = new itemSlot($controllerDat[20], $slotFile, 40);
$charPlots->addItem($postVals[1], $slotFile);

// Send a message to the player that he has a char invited to a new plot
$trgList[] = $unitDat[6];
sendMessage([$pGameId, $unitDat[6], time(), 1, 0, $postVals[1], $_SESSION['selectedItem']], "", $trgList); 

fclose($slotFile);
fclose($unitFile);
fclose($taskFile);

?>
