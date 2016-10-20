<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');
require_once('./sysMessage.php');

$warFile = fopen($gamePath.'/wars.war', 'r+b');
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

print_r($postVals);

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
$offers = 0;
$offerStrings = [];
while ($check < sizeof($postVals) && $offers < 3) {
	echo 'Offer #'.$offers;
	switch($postVals[$check]) {
		case 1: // resources
			echo 'Resources';
			if (array_key_exists($postVals[$check+1], $resources)) {
				$resources[$postVals[$check+1]] += $postVals[$check+2];
			} else {
				$resources[$postVals[$check+1]] = $postVals[$check+2];
			}
			$rscCheck = true;
			$offerStrings[$offers] = pack('i*', 1, $postVals[$check+1], $postVals[$check+2]);
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
			$offerStrings[$offers] = pack('i*', 2, $postVals[$check+1]);
			$check +=2;

		break;

		default:
			$offerStrings[$offers] = pack('i*', 0, 0, 0);
		break;
	}
	$offers++;
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
	// Record the offered items
	echo ' Record the offerings';
	fseek($warFile, $postVals[1]*$defaultBlockSize+36+36*$warPosition);
	fwrite($warFile, $offerStrings[0].$offerStrings[1].$offerStrings[2]);
} else {
	for ($i=0; $i<sizeof($neededRsc); $i++) {
		echo 'You need more '.$neededRsc[$i];
	}


}
/*
// Process demands made
$demands = 0;
$demandStrings = [];
while ($check < sizeof($postVals) && $demands < 3) {
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
			$demandStrings[$demands] = pack('i*', 1, $postVals[$check+1], $postVals[$check+2]);
		break;

		case 2: //  object
		echo 'Object';
			$check +=2;
			$demandStrings[$demands] = pack('i*', 2, $postVals[$check+1]);
		break;

		case 3: // A default character type/rank
			$check+=2;
			$demandStrings[$demands] = pack('i*', 3, $postVals[$check+1]);
		break;

		case 4: // A default unit type/rank
			$check+=2;
			$demandStrings[$demands] = pack('i*', 3, $postVals[$check+1]);
		break;

		default:
			$demandStrings[$demands] = pack('i*', 0,0,0);
		break;
	}
	$demands++;
}

// Record the demanded items
fseek($warFile, $postVals[1]*$defaultBlockSize+108+36*$warPosition);
fwrite($warFile, $demandStrings[0].$demandStrings[1].$demandStrings[2]);
*/
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
