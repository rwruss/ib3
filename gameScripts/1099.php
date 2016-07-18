<?php
date_default_timezone_set('America/Chicago');
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
  echo '<table>';
  for ($i=1; $i<=sizeof($msgSlot->slotData); $i+=3) {
    if ($msgSlot->slotData[$i] > 0) {
      fseek($msgFile, $msgSlot->slotData[$i]);
      //$msgLen = unpack('i', fread($msgFile, 4));
      $msgDat = explode('<-!->', fread($msgFile, 50));
	    $msgHead = unpack('i*', substr($msgDat[0], 0, 16));
      if ($msgHead[4] > 0) {
        $subPrefix = "RE:";
      } else {
        $subPrefix = "";
      }

		  echo '<tr class="msgSum_'.$msgSlot->slotData[$i+2].'" onclick="makeBox(\'readMsg\', \'1100,'.$msgSlot->slotData[$i].','.$msgHead[1].'\', 0, 0, 0, 0)"><td>'.date('d/m/y H:i:s', $msgHead[2]).'<td><td>'.$subPrefix.substr($msgDat[0],16).'</td></tr>';
    }
  }
  echo '</table>';
  fclose($msgFile);
}
fclose($unitFile);
fclose($slotFile);
?>
