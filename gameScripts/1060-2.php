<?php

include("./slotFunctions.php");
echo 'Job at specific resource point';

// Look for available resource points nearby
$mapSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);

$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');
$mapItems = new itemSlot($mapSlot, $mapSlotFile, 404); // start, file, slot size

$orderRadius = 5;
$oRadiusSq = $orderRadius*$orderRadius;

$cityID = 0;

for ($i=1; $i<=sizeof($mapItems->slotList); $i++) {
	// Check to see if the item is a city or is in the radius of the unit orders
	fseek($unitFile, $mapItems->slotList[$i]*$defaultBlockSize);
	$checkDat = unpack('i*', fread($unitFile, 400));

	if ($checkDat[4] == 2) {
		$distCheck = ($checkDat[1]-$unitDat[1])*($checkDat[1]-$unitDat[1])+($checkDat[2]-$unitDat[2])*($checkDat[2]-$unitDat[2]);
		if ($distCheck == 0) {
			// Check to see if the unit is on a city (this gives access to all of the city resource buildings)
			if ($checkDat[4] == 1) {
				$cityID = $mapItems->slotList[$i];
			} else {
				$workItems[] = $mapItems->slotList[$i];
			}
		}
		else if ($distCheck <= $oRadiusSq) {
			// Object is close enough to be worked on by this unit
			$workItems[] = $mapItems->slotList[$i];
		}
	}
}

// See if unit is near the edges of a gird - and check surrounding grids as needed
$left = false;
$right = false;
$top = false;
$bot = false;

if ($unitDat[1] - (floor($unitDat[1]/120)*120 <= $orderRadius)) $left = true;
else if (floor($unitDat[1]/120)*120+120 - $unitDat[1] <= $orderRadius) $right = true;

if ($unitDat[2] - floor($unitDat[2]/120)*120 <= $orderRadius) $bot = true;
else if (floor($unitDat[2]/120)*120+120 - $unitDat[2] <= $orderRadius) $top = true;

$checkItemList = [];
if ($left) {
	echo 'Left<br>';
	$xVal = $unitDat[1] - 120;
	$mapSlot = floor($unitDat[2]/120)*120+floor($xVal/120);
	$list = checkSlot($mapSlot, $mapSlotFile, 404);
	$checkItemList = array_merge($checkItemList, $list->slotList);
	if ($top) {
		echo 'Top<br>';
		$yVal = $unitDat[2]+120;
		$mapSlot = floor($yVak/120)*120+floor($xVal/120);
		$list = checkSlot($mapSlot, $mapSlotFile, 404);
		$checkItemList = array_merge($checkItemList, $list->slotList);
	}
	else if ($bot) {
		echo 'Bot<br>';
		$yVal = $unitDat[2]-120;
		$mapSlot = floor($yVal/120)*120+floor($xVal/120);
		$list = checkSlot($mapSlot, $mapSlotFile, 404);
		$checkItemList = array_merge($checkItemList, $list->slotList);
	}
}
else if ($right) {
	echo 'Right<br>';
	$xVal = $unitDat[1] + 120;
	$mapSlot = floor($unitDat[2]/120)*120+floor($xVal[1]/120);
	$list = checkSlot($mapSlot, $mapSlotFile, 404);
	$checkItemList = array_merge($checkItemList, $list->slotList);
	if ($top) {
		$yVal = $unitDat[2]+120;
		$mapSlot = floor($yVal/120)*120+floor($xVal/120);
		$list = checkSlot($mapSlot, $mapSlotFile, 404);
		$checkItemList = array_merge($checkItemList, $list->slotList);
	}
	else if ($bot) {
		$yVal = $unitDat[2]-120;
		$mapSlot = floor($yVal/120)*120+floor($xVal/120);
		$list = checkSlot($mapSlot, $mapSlotFile, 404);
		$checkItemList = array_merge($checkItemList, $list->slotList);
	}
}

if ($top) {
	$yVal = $unitDat[2]+120;
	$mapSlot = floor($yVal/120)*120+floor($unitDat[1]/120);
	$list = checkSlot($mapSlot, $mapSlotFile, 404);
	$checkItemList = array_merge($checkItemList, $list->slotList);
}
else if ($bot) {
	$yVal = $unitDat[2]-120;
	$mapSlot = floor($yVal/120)*120+floor($unitDat[1]/120);
	$list = checkSlot($mapSlot, $mapSlotFile, 404);
	$checkItemList = array_merge($checkItemList, $list->slotList);
}

fclose($mapSlotFile);

// Check the unit list for intersections
for ($i=0; $i<sizeof($checkItemList); $i++) {
	fseek($unitFile, $checkItemList[$i]*$defaultBlockSize);
	$checkDat = unpack('i*', fread($unitFile, 400));

	if ($checkDat[4] == 2) {
		$distCheck = ($checkDat[1]-$unitDat[1])*($checkDat[1]-$unitDat[1])+($checkDat[2]-$unitDat[2])*($checkDat[2]-$unitDat[2]);
		if ($distCheck <= $oRadiusSq) {
			$workItems[] = $checkItemList[$i];
		}
	}
}

// Output each item to an order option

function checkSlot($slotNum, $slotFile) {
	echo 'Look up slot '.$slotNum.'<br>';
	return new itemSlot($slotNum, $slotFile, 404);
}

?>
