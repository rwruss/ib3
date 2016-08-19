<?php

print_r($postVals);

include('./unitClass.php');
include('./cityClass.php');
include('./slotFunctions.php');

$srcUnit = loadUnit($postVals[1], $unitFile, 400);
$dstUnit = loadUnit($postVals[2], $unitFile, 400);

// Confirm that units are close enough to each other for the transfer


// Confirm that the army/unit in questions has the resources being transfered.
$pvSize = sizeof($postVals);
$passedCheck = 0;
$checkCount = 0;
$neededSpace = 0;
$spotList = [];
$srcSupply = new itemSlot($srcUnit->get('carrySlot'), $slotFile, 40);
for ($i=3; $i<$pvSize; $i+=2) {
	$checkCount++;
	$neededSpace += $postVals[$i+1];
	for ($j=1; $j<sizeof($srcSupply->slotData); $j+=2) {
		if ($postVals[$i] == $srcSupply->slotData[$j] && $postVals[$i+1] <= $srcSupply->slotData[$j+1]) {
		$passedCheck++;
		$spotList[$postVals[$i]] = $j+1;
		break;
		}
	}
}

if ($checkCount == $passedCheck) exit('error 1-6011');

// Confirm that the unit that is receiving the resources has enough space for what is being given.
$dstSupply = new itemSlot($dstUnit->get('carrySlot'), $slotFile, 40);
$dstStorageUsed = 0;

for ($i=1; $i<sizeof($dstSupply->slotData); $i+=2) {
	$dstStorageUsed += $dstSupply->[$i+1];
}

if ($dstStorageUsed + $neededSpace > $dstUnit->get('carryCap')) exit ('error 2-6011');

// Remove the resources from the source listUnitID
for ($i=3; $i<$pvSize; $i+=2) {
	$srcSupply->addItemAtSpot($srcSupply->slotData[$spotList[$postVals[$i]]]-$postVals[$i+1], $spotList[$postVals[$i]], $slotFile);
}

// Index the spots for the destination slot
$dstSpotList = [];
for ($i=3; $i<sizeof($postVals); $i+=2) {
	$dstSpotList[$postVals[$i]] = 0;
	for ($j=1; $j<=sizeof($dstSupply->slotData); $j+=2) {
		if ($postVals[$i] == $dstSupply->slotData[$j]) {
			$dstSpotList[$postVals[$i]] = $j;
			break;
		}
	}
}

// Add the resources to the destination list
for ($i=3; $i<$pvSize; $i+=2) {
	if ($dstSpotList[$postVals[$i]]> 0 {
		$dstSuppy->slotData[$dstSpotList[$i]] = $postVals[$i+1];
	} else {
		array_push($dstSupply->slotData[], $postVals[$i], $postVals[$i+1]);
	}
	$dstSupply->saveSlot();
}


?>
