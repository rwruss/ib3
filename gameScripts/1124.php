<?php

echo 'Declared war on player '.$postVals[1];

// Get owner of the object that you are declaring war on
$trgObj = loadUnit($postVals[1], $unitFile, 400);
$trgPlayer = $trgObj->get('controller');

require_once('./slotFunctions.php');
require_once('./unitClass.php');

$warFile = fopen($gamePath.'/wars.war', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

// Start a new war
if (flock($warFile, LOCK_EX)) {
  fseek($warFile, 0, SEEK_END);
  $newWar = max(1,ftell($warFile)/$warBlockSize);


  $warDat = array_fill(1, 25, 0);

  $warDat[1] = 1;
  $warDat[2] = 0; // war target
  $warDat[3] = 0; // war goal
  $warDat[4] = time(); // time started
  $warDat[5] = $pGameID; // side A (attacker)
  $warDat[6] = $trgPlayer; // side D (defender)
  $warDat[7] = $newWar+1; // player side spot
  $warDat[8] = 0; //warscore
  //fwrite($warFile, pack('i', 0));

  $writeDat = '';
  for ($i=1; $i<=25; $i++) {
    $writeDat .= pack('i', $warDat[$i]);
  }
  fseek($warFile, $newWar*$warBlockSize);
  fwrite($warFile, $writeDat);

  // Record new side slot information
  fwrite($warFile, pack('i*', 0, 1, $pGameID, 2, $postVals[1]));

  // finish out the slot
  fseek($warFile, ($newWar+1)*$warBlockSize-4);
  fwrite($warFile, pack('i', 0));

  flock($warFile, LOCK_UN);
}

// Record the war in each player's war list

$aPlayer = loadPlayer($pGameID, $unitFile, 400);
if ($aPlayer->get('warList') == 0) {
  $addedSlot = newSlot($slotFile);
  //$aPlayer->set('warList', $addedSlot);
  $aPlayer->save('warList', $addedSlot);
}
$firstSlot = new itemSlot($aPlayer->get('warList'), $slotFile, 40);
echo 'Add war to slot '.$aPlayer->get('warList').' for player 1';
$firstSlot->addItem($newWar, $slotFile);

echo 'Target object is a ('.$trgObj->get('uType').') - '.get_class($trgObj).' War target is '.$trgObj->objectTarget();
$dPlayer = loadPlayer($trgObj->objectTarget(), $unitFile, 400);
if ($dPlayer->get('warList') == 0) {
  $addedSlot = newSlot($slotFile);
  $dPlayer->set('warList', $addedSlot);
  $dPlayer->save('warList', $addedSlot);
}
echo 'Add war to slot '.$dPlayer->get('warList').' for player 2';
$secondSlot = new itemSlot($dPlayer->get('warList'), $slotFile, 40);
$secondSlot->addItem($newWar, $slotFile);

// Record in each players diplomacy list
$aDipSlot = new blockSlot($aPlayer->get('dipSlot'), $slotFile, 40);
$dDipSlot = new blockSlot($dPlayer->get('dipSlot'), $slotFile, 40);

$aDipSlot->addItem($slotFile, pack('i*', $warDat[6], 1, time(), 0);
$dDipSlot->addItem($slotFile, pack('i*', $warDat[5], 2, time(), 0);

fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>
