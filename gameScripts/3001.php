<?php

include("./slotFunctions.php");
include("./unitClass.php");
$slotFile = fopen($gamePath.'/msgSlots.slt', 'r+b');
$unitSlotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
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
$toList = [];
switch($trgObject->unitDat[4]) {
  case 1: // a town object
    echo 'Send to all members of a town';
    $townDat = new itemSlot($trgObject->unitDat[19], $unitSlotFile, 40);
    print_r($townDat->slotData);
    for ($i=1; $i<sizeof($townDat->slotData); $i+=2) {
      if ($townDat->slotData[$i] < -1) $toList[] = $townDat->slotData[$i+1];
    }
    break;

  case 10: // a tribe object
    echo 'This is a tribe... send to '.$trgObject->unitDat[6];
    $toList[] = $trgObject->unitDat[6];
    break;

  case 13: // a player object
    $toList[] = $msg[0];
}

$sendList = array_unique($toList);
if (sizeof($sendList) > 0) {
  for ($i=0; $i<sizeof($sendList); $i++) {
    echo 'Record message for player '.$sendList[$i];
    $trgPlayer = new player($sendList[$i], $unitFile, 400);
    if ($trgPlayer->unitDat[25] == 0) {
      if (flock($slotFile, LOCK_EX)) {
        fseek($slotFile, 0, SEEK_END);
        $use_slot = max(1, (ftell($slotFile))/40);
        fseek($slotFile, $use_slot*40 +39);
        fwrite($slotFile, pack("C", 0));
        fflush($slotFile);
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

    // if message is a reply, get pvs info.
    if ($postVals[2] > 0) {
      fseek($messageContentFile, $postVals[2]);
      $pvsDat = explode('<-!->', fread($messageContentFile, 100));
      $msg[1] = substr($pvsDat[0], 16);
    }
    if (flock($messageContentFile, LOCK_EX)) {
      fseek($messageContentFile, 0, SEEK_END);
      $msgSpot = ftell($messageContentFile);

      //fwrite($msg[1]);
      $blockLength = strlen($msg[1]) + strlen($msg[2]) + 5 + 4 + 4 + 4 + 4;
      // total length is subject length + message length + separator length + total length integer + time integer + sender ID + message ID in reply to
      echo 'Message length is '.$blockLength.' ('.strlen($msg[1]).') + ('.strlen($msg[2]).') + 9 written at spot '.$msgSpot.'<br>
      Subject: '.$msg[1].'<br>
      Content: '.$msg[2].'<br>';
      fwrite($messageContentFile, pack('i*', $blockLength, time(), $pGameID, $postVals[2]).$msg[1].'<-!->'.$msg[2]);
      $msgSlot->addItem($slotFile, pack('i*', $msgSpot, 1, 1), $msgSlot->findLoc(0, 3));  // message start loc, message file num, read/unread
    }
  }
}
fclose($unitSlotFile);
fclose($unitFile);
fclose($slotFile);
?>
