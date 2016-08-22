<?php

print_r($postVals);

include('./unitClass.php');
include('./cityClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

$srcUnit = loadUnit($postVals[1], $unitFile, 400);
echo 'SOURCE UNIT';
print_r($srcUnit);
$dstUnit = loadUnit($postVals[2], $unitFile, 400);
echo 'DST UNIT';
print_r($dstUnit);

// Confirm that units are close enough to each other for the transfer


// Confirm that the army/unit in questions has the resources being transfered.
$pvSize = sizeof($postVals);
for ($i=0; $i<$pvSize; $i++) {
  if (!is_numeric($postVals[$i]) || $postVals[$i] <= 0) exit('error 3-6011-');
  //else echo $postVals[$i].' is num';
}
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

if ($checkCount != $passedCheck) exit('error 1-6011-'.$checkCount.'/'.$passedCheck);

// Confirm that the unit that is receiving the resources has enough space for what is being given.
$dstSupply = new itemSlot($dstUnit->get('carrySlot'), $slotFile, 40);
$dstStorageUsed = 0;
print_r($dstSupply->slotData);

for ($i=1; $i<sizeof($dstSupply->slotData); $i+=2) {
	$dstStorageUsed += $dstSupply->slotData[$i+1];
}

if ($dstStorageUsed + $neededSpace > $dstUnit->get('carryCap') && $dstStorageUsed + $neededSpace > 100000) exit ('error 2-6011-'.($dstStorageUsed + $neededSpace).'/'.$dstUnit->get('carryCap'));

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
			$dstSpotList[$postVals[$i]] = $j+1;
			break;
		}
	}
}
echo 'DestSpotList';
print_r($dstSpotList);
// Add the resources to the destination list
for ($i=3; $i<$pvSize; $i+=2) {
	if ($dstSpotList[$postVals[$i]]> 0) {
		$dstSupply->slotData[$dstSpotList[$postVals[$i]]] = $dstSupply->slotData[$dstSpotList[$postVals[$i]]] + $postVals[$i+1];
	} else {
		array_push($dstSupply->slotData[], $postVals[$i], $postVals[$i+1]);
	}
	$dstSupply->saveSlot();
}

fclose($slotFile);
fclose($unitFile);
?>
