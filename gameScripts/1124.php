<?php

echo 'Declared war on player '.$postVals[1];

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
  $warDat[6] = $postVals[1]; // side A (attacker)
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

$firstPlayer = loadPlayer($pGameID, $unitFile, 400);
if ($firstPlayer->get('warList') == 0) {
  $addedSlot = newSlot($slotFile);
  //$firstPlayer->set('warList', $addedSlot);
  $firstPlayer->save('warList', $addedSlot);
}
$firstSlot = new itemSlot($firstPlayer->get('warList'), $slotFile, 40);
echo 'Add war to slot '.$firstPlayer->get('warList').' for player 1';
$firstSlot->addItem($newWar, $slotFile);

$trgObj = loadUnit($postVals[1], $unitFile, 400);
echo 'Target object is a ('.$trgObj->get('uType').') - '.get_class($trgObj).' War target is '.$trgObj->objectTarget();
$secondPlayer = loadPlayer($trgObj->objectTarget(), $unitFile, 400);
if ($secondPlayer->get('warList') == 0) {
  $addedSlot = newSlot($slotFile);
  $secondPlayer->set('warList', $addedSlot);
  $secondPlayer->save('warList', $addedSlot);
}
echo 'Add war to slot '.$secondPlayer->get('warList').' for player 2';
$secondSlot = new itemSlot($secondPlayer->get('warList'), $slotFile, 40);
$secondSlot->addItem($newWar, $slotFile);

fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>
