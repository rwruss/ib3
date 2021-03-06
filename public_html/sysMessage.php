<?php

/*
Process sending a message to something - this script determines the object type and players that should receive the message.
Send the message to this script in the following form form:



*/

function sendMessage($target, $subject, $msgContent, $replyTo, $msgType, $unitFile, $unitSlotFile) {
  global $gamePath, $pGameID;
  $msgSlotFile = fopen($gamePath.'/msgSlots.slt', 'r+b');

  $trgObject = loadUnit($target, $unitFile, 400);
  //$trgObject = new unit($target, $unitFile, 400);
  //fseek($unitFile, $target*$defaultBlockSize);
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
      echo 'Send to player '.$target;
      $toList[] = $target;
  }

  $sendList = array_unique($toList);
  if (sizeof($sendList) > 0) {
    for ($i=0; $i<sizeof($sendList); $i++) {
      echo 'Record message for player '.$sendList[$i];
      $trgPlayer = loadPlayer($sendList[$i], $unitFile, 400);
      if ($trgPlayer->unitDat[25] == 0) {
        if (flock($msgSlotFile, LOCK_EX)) {
          fseek($msgSlotFile, 0, SEEK_END);
          $use_slot = max(1, (ftell($msgSlotFile))/40);
          fseek($msgSlotFile, $use_slot*40 +39);
          fwrite($msgSlotFile, pack("C", 0));
          fflush($msgSlotFile);
          flock($msgSlotFile, LOCK_UN); // release the lock
          $trgPlayer->unitDat[25] = $use_slot;
        }
          echo 'Createa  new message slot at '.$trgPlayer->unitDat[25];
      }
      $msgSlot = new blockSlot($trgPlayer->unitDat[25], $msgSlotFile, 40);

      // Set unread flag
      $trgPlayer->unitDat[5] = 1;
      $trgPlayer->saveAll($unitFile);

      // Record message contents in message file and message index
      $messageContentFile = fopen($gamePath.'/messages.dat', 'r+b');

      // if message is a reply, get pvs info.
      if ($replyTo > 0) {
        fseek($messageContentFile, $replyTo);
        $pvsDat = explode('<-!->', fread($messageContentFile, 100));
        $subject = substr($pvsDat[0], 16);
      }
      if (flock($messageContentFile, LOCK_EX)) {
        fseek($messageContentFile, 0, SEEK_END);
        $msgSpot = ftell($messageContentFile);

        //fwrite($subject);
        $blockLength = strlen($subject) + strlen($msgContent) + 5 + 4 + 4 + 4 + 4 + 4;
        // total length is subject length + message length + separator length + total length integer + time integer + sender ID + message ID in reply to + message type
        echo 'Message length is '.$blockLength.' ('.strlen($subject).') + ('.strlen($msgContent).') + 9 written at spot '.$msgSpot.'<br>
        Subject: '.$subject.'<br>
        Content: '.$msgContent.'<br>';
        fwrite($messageContentFile, pack('i*', $blockLength, time(), $pGameID, $replyTo, $msgType).$subject.'<-!->'.$msgContent);
        $msgSlot->addItem($msgSlotFile, pack('i*', $msgSpot, 1, 1), $msgSlot->findLoc(0, 3));  // message start loc, message file num, read/unread
      }
    }
  }
  fclose($msgSlotFile);
}

?>
