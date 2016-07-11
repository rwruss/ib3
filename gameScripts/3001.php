<?php

include("./slotFunctions.php");
include("./unitClass.php");
$slotFile = fopen($gamePath.'/msgSlots.slt', 'r+b');
$msg = explode('<!*!>', substr($_POST['val1'], 5));
print_r($msg);

// look up player information to get slot IDS
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');


// Determine which players should receive the message

$trgObject = new unit($msg[0], $unitFile, 400);
//fseek($unitFile, $msg[0]*$defaultBlockSize);
//$trgDat = unpack('i*', fread($unitFile, 400));
print_r($trgObject->unitDat);

//Determine who all to send it to based on target type
switch($trgObject->unitDat[4]) {
  case 1: // a town object
    echo 'Send to all members of a town';
    $townDat = new itemSlot($trgObject->unitDat[19], $slotFile, 40);
    print_r($townDat->slotData);
    break;

  case 10: // a tribe object
    echo 'This is a tribe... send to '.$trgObject->unitDat[6];
    break;
}
/*
if ($trgPlayer->unitDat[25] == 0) {
  if (flock($slotFile, LOCK_EX)) {
    fseek($slotFile, 0, SEEK_END);
    $use_slot = max(1, (ftell($slotFile))/40);
    fseek($slotFile, $use_slot*40 +39);
    fwrite($slotFile, pack("C", 0));
    flock($slotFile, LOCK_UN); // release the lock
    $trgPlayer->unitDat[25] = $use_slot;
  }
    echo 'Createa  new message slot at '.$trgPlayer->unitDat[25];
}
$msgSlot = new blockSlot($trgPlayer->unitDat[25], $slotFile, 40);

// Set unread flag
$trgPlayer->unitDat[5] = 1;
$trgPlayer->saveAll($unitFile);

// Record message contents in message file and message index
$messageContentFile = fopen($gamePath.'/messages.dat', 'r+b');
if (flock($messageContentFile, LOCK_EX)) {
  fseek($messageContentFile, 0, SEEK_END);
  $msgSpot = ftell($messageContentFile);
  //fwrite($msg[1]);
  $blockLength = strlen($msg[1]) + strlen($msg[2]) + 5;
  echo 'Message length is '.$blockLength.' ('.strlen($msg[1]).') + ('.strlen($msg[2]).') written at spot '.$msgSpot.'<br>
  Subject: '.$msg[1].'<br>
  Content: '.$msg[2].'<br>';

  $msgSlot->addItem($slotFile, pack('i*', $msgSpot, 1, 1), $msgSlot->findLoc(0, 3));  // message start loc, message file num, read/unread
}
*/

fclose($unitFile);
fclose($slotFile);
?>
