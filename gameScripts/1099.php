<?php

echo 'messages';
include("./slotFunctions.php");
include("./unitClass.php");
$slotFile = fopen($gamePath.'/msgSlots.slt', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

// Read message slot for player
$trgPlayer = new player($pGameID, $unitFile, 400);
echo 'Check player '.$pGameID.' message slot '.$trgPlayer->unitDat[25];
if ($trgPlayer->unitDat[25] == 0) {
  echo 'No messages';
} else {
  $msgSlot = new blockSlot($trgPlayer->unitDat[25], $slotFile, 40);
}
fclose($unitFile);
fclose($slotFile);
?>
