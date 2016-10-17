<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');
require_once('./sysMessage.php');

$warFile = fopen($gamePath.'/wars.war', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');


//Load war info
fseek($warFile, $postVals[1]*$defaultBlockSize);
$warDat = unpack('i*', fread($warFile, $warBlockSize));

// verify that player can make offer and get place
if ($warDat[5] == $pGameID) {
	$warPosition = 0;
	$opponent = $warDat[6];
}
elseif ($warDat[6] == $pGameID) {
	$warPosition = 1;
	$opponent = $warDat[5];
}
else exit("6211-1 Not able to take this action");

// Review selected options
$resources = [];
$objPass = true;
$rscCheck = false;
$rscPass = true;
$check = 2;
$objList = [];
while ($check < sizeof($postVals)) {
	//echo $check.' vs '.sizeof($postVals);
	switch($postVals[$check]) {
		case 1: // resources
			echo 'Resources';
			if (array_key_exists($postVals[$check+1], $resources)) {
				$resources[$postVals[$check+1]] += $postVals[$check+2];
			} else {
				$resources[$postVals[$check+1]] = $postVals[$check+2];
			}
			$rscCheck = true;
			$check +=3;
		break;

		case 2: //  object
		echo 'Object';
		// Verify that the object is available to offer
		$thisObj = loadUnit($postVals[$check+1], $unitFile, 400);
		if ($thisObj->get('controller') != $pGameID || $thisObj->get('warPrize') != 0) {
			echo 'Cant offer this unit';
			$objPass = false;
		} else $objList[] = $postVals[$check+1];

		$check +=2;
		break;
	}
	//$check+=100;
}

$neededRsc = [];
if ($rscCheck) {
	// Verify that requested resources are available and place them in "escrow"
	$thisPlayer = loadPlayer($pGameID, $unitFile, 400);
	$playerCity = loadUnit($thisPlayer->get('homeCity'), $unitFile, 400);
	$rscList = new itemSlot($playerCity->get('carrySlot'), $slotFile, 40);
	for ($i=1; $i<sizeof($rscList->slotData); $i+=2) {
		$rscStores[$rscList->slotData[$i]] = $rscList->slotData[$i+1];
	}
	print_r($rscStores);

	foreach ($resources as $rscID => $rscAmt) {
		echo 'Need '.$rscAmt.' of '.$rscID;
		if (array_key_exists($rscID, $rscStores)) {
			if ($rscStores[$rscID] < $rscAmt) {
				$rscPass = false;
				$neededRsc = $rscID;
			} else {
				echo 'Have '.$rscStores[$rscID].' Need '.$rscAmt;
			}
		} else {
		$neededRsc = $rscID;
		$rscPass = false;
		}
	}
}

if ($rscPass && $objPass) {
	// Place the offered goods in "Escrow"
	echo 'Resources ok';
	foreach ($resources as $rscID => $rscAmt) {
		echo 'Reserve resources '.$rscID;
		$playerCity->adjustRsc($rscID, -$rscAmt, $slotFile);
	}
	for ($i=0; $i<sizeof($objList); $i++) {
		echo 'Reserve object '.$objList[$i];
	}
} else {
	for ($i=0; $i<sizeof($neededRsc); $i++) {
		echo 'You need more '.$neededRsc[$i];
	}
}
echo 'Done';
// Verify that the player has enouh resources in they treasury
/*

*/
/*
// Record the offer and notify the other player
fseek($warFile, $postVals[1]*100+52+$warPosition*24);
fwrite($warFile, pack('i*', $postVals[2], $postVals[3], $postVals[4], $postVals[5], $postVals[6], $postVals[7]));

// Notify the target player
sendMessage($opponent, "Peace offer", "you have been offered peace", 0, 4, $unitFile, $slotFile);
*/
fclose($warFile);
fclose($unitFile);
fclose($slotFile);

?>
