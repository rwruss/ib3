<?php

date_default_timezone_set('America/Chicago');
include("./slotFunctions.php");
include("./unitClass.php");
$slotFile = fopen($gamePath.'/msgSlots.slt', 'rb');
$msgFile = fopen($gamePath.'/messages.dat', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$trgPlayer = loadPlayer($pGameID, $unitFile, 400);
$msgSlot = new blockSlot($trgPlayer->unitDat[25], $slotFile, 40);

// Verify that message is listed in players slot
$approved = true;

// Load and show the message
/*
echo '<script>
useDeskTop.newPane("readMsg");
thisDiv = useDeskTop.getPane("readMsg");';*/
if ($approved) {
  fseek($msgFile, $postVals[1]);
  //$msgLen = unpack('i', fread($msgFile, 4));
  $msgContent = explode('<-!->', fread($msgFile, $postVals[2]));
  $msgDat = unpack('i*', substr($msgContent[0], 0, 16));
  //print_R($msgDat);
  if ($msgDat[4] > 0) {
    $subPrefix = 'RE: ';
  } else {
    $subPrefix = '';
  }
  echo $msgDat[3].' at '.date('d/m/y H:i:s', $msgDat[2]).'<br>
  '.$subPrefix.substr($msgContent[0], 16).'<br>'.$msgContent[1].'<br>
  <span onclick=scrMod("1101,'.$postVals[1].'")>Reply</span>';

  while ($msgDat[4] > 0) {
    fseek($msgFile, $msgDat[4]);
    $headDat = fread($msgFile, 16);
    $msgDat = unpack('i*', $headDat);
    //print_R($msgDat);
    $body = explode('<-!->', fread($msgFile, $msgDat[1]-16));
    echo '<hr>'.$msgDat[3].' at '.date('d/m/y H:i:s', $msgDat[2]).'<br>'.$body[1];
  }
}
fclose($slotFile);
fclose($msgFile);
fclose($unitFile);
?>
