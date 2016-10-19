<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');

$warFile = fopen($gamePath.'/wars.war', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Verify that the player can end the war
fseek($warFile, $postVals[1]*$defaultBlockSize);
$warDat = unpack('i*', fread($warFile, $warBlockSize));

if ($warDat[5] != $pGameID && $warDat[6] != $pGameID) exit('erorr 1311-1');

$sideSwitch = 1;
$playerSide = 1;
$oppside = 2;
if ($warDat[6] == $pGameID) {
  // player is the defender
  $sideSwitch = -1;
  $playerSide = 2;
  $oppside = 1;
}

// Record the player's decision to end the war


// if both players agree, end the war and distribute the required items
if ($warDat[$oppside+45] == 1){
	// End the war
	/// update player war list
	$aPlayer = loadPlayer($warDat[5], $unitFile, 400);
	$dPlayer = loadPlayer($warDat[6], $unitFile, 400);
	
	$aWarList = new itemList($aPlayer->get('warList'), $slotFile, 40);
	$aWarList->deleteByValue($postVals[1], $slotFile);
	
	$dWarList = new itemList($dPlayer->get('warList'), $slotFile, 40);
	$dWarList->deleteByValue($postVals[1], $slotFile);
	
	// Transfer the agreed upon goods 
	$aplayerCity = loadUnit($aPlayer->get('homeCity'), $unitFile, 400);
	$dplayerCity = loadUnit($dPlayer->get('homeCity'), $unitFile, 400);
	
	//$aRscList = new itemSlot($aplayerCity->get('carrySlot'), $slotFile, 40);
	//$dRscList = new itemSlot($dplayerCity->get('carrySlot'), $slotFile, 40);
	
	// Move attacker offers to defender
	for ($i=0; $<3; $i++) {
		moveItems($warDat[10+$i*3], $warDat[10+$i*3+1], $warDat[10+$i*3+2], $warDat[6], $dPlayerCity);
	}
	
	// Move defender offers to attacker
	for ($i=0; $<3; $i++) {
		moveItems($warDat[19+$i*3], $warDat[19+$i*3+1], $warDat[19+$i*3+2], $warDat[5], $aPlayerCity);
	}
	
	// update the diplomacy for both players to put in a truce
	$aDipSlot = new blockSlot($aPlayer->get('dipSlot'), $slotFile, 40);
	$dDipSlot = new blockSlot($dPlayer->get('dipSlot'), $slotFile, 40);

	$aDipSlot->addItem($slotFile, pack('i*', $warDat[6], 3, time(), 3*24*3600);
	$dDipSlot->addItem($slotFile, pack('i*', $warDat[5], 3, time(), 3*24*3600);
	
}

fclose($warFile);
fclose($unitFile);
fclose($slotFile);

function moveItems($index, $number, $amount, $trgID, $trgCity) {
	global $unitFile, $slotFile;
	switch($index) {
		case 1: // Move resources
			$trgCity->adjustRsc($number, $amount, $slotFile);
		break;
		
		case 2: // move unit
			$thisUnit = loadUnit($number, $unitFile, 400);
			$thisUnit->save('owner', $trgID);
			$thisUnit->save('controller', $trgID);
		break;
		
		default:
		break;
	}
}

?>