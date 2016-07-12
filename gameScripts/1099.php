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
  $msgFile = fopen($gamePath.'/messages.dat', 'rb');
  $msgSlot = new blockSlot($trgPlayer->unitDat[25], $slotFile, 40);
  print_r($msgSlot->slotData);
  for ($i=1; $i<=sizeof($msgSlot->slotData); $i+=3) {
    if ($msgSlot->slotData[$i] > 0) {
      fseek($msgFile, $msgSlot->slotData[$i]);
      $msgLen = unpack('i', fread($msgFile, 4));
      echo fread($msgFile, $msgLen[1]).'<br>';
    }
  }
  fclose($msgFile);
}
fclose($unitFile);
fclose($slotFile);
?>
