<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');

$warFile = fopen($gamePath.'/wars.war', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Verify that the player can end the war
fseek($warFile, $postVals[1]*$defaultBlockSize);
$warDat = unpack('i*', fread($warFile, $warBlockSize));

if ($warDat[5] != $pGameID && $warDat[6] != $pGameID) exit('erorr 1312-1');

$sideSwitch = 1;
$playerSide = 1;
$oppside = 2;
if ($warDat[6] == $pGameID) {
  // player is the defender
  $sideSwitch = -1;
  $playerSide = 2;
  $oppside = 1;
}

$requiredScore = [0, 0, 0, 0];
// Verify that the warscore is high enough to enforce the demand
if ($warDat[8] < $requiredScore[$warDat[1]]) exit ('You cannot enforce these conditions');

$aPlayer = loadPlayer($warDat[5], $unitFile, 400);
$dPlayer = loadPlayer($warDat[6], $unitFile, 400);

// update the diplomacy for both players to put in a truce
$aDipSlot = new blockSlot($aPlayer->get('dipSlot'), $slotFile, 40);
$dDipSlot = new blockSlot($dPlayer->get('dipSlot'), $slotFile, 40);

$aDipSlot->addItem($slotFile, pack('i*', $warDat[6], 3, time(), 3*24*3600));
$dDipSlot->addItem($slotFile, pack('i*', $warDat[5], 3, time(), 3*24*3600));

// Make the demanded changes on the defender

fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>
