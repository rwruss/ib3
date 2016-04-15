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
$cityItems = [];
$workItems = [];
//print_r($mapItems->slotData);
for ($i=1; $i<=sizeof($mapItems->slotData); $i++) {
	// Check to see if the item is a city or is in the radius of the unit orders
	fseek($unitFile, $mapItems->slotData[$i]*$defaultBlockSize);
	$checkDat = unpack('i*', fread($unitFile, 400));

	if ($checkDat[4] == 2) {
		$distCheck = ($checkDat[1]-$unitDat[1])*($checkDat[1]-$unitDat[1])+($checkDat[2]-$unitDat[2])*($checkDat[2]-$unitDat[2]);
		if ($distCheck == 0) {
			// Check to see if the unit is on a city (this gives access to all of the city resource buildings)
			if ($checkDat[4] == 1) {
				$cityID = $mapItems->slotData[$i];
			} else {
				$workItems[$mapItems->slotData[$i]] = $checkDat[10];
				//$cityItems[$mapItems->slotData[$i]] = $checkDat[10];
			}
		}
		else if ($distCheck <= $oRadiusSq) {
			// Object is close enough to be worked on by this unit
			$workItems[$mapItems->slotData[$i]] = $checkDat[10];
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
	//echo 'Left<br>';
	$xVal = $unitDat[1] - 120;
	$mapSlot = floor($unitDat[2]/120)*120+floor($xVal/120);
	$list = checkSlot($mapSlot, $mapSlotFile, 404);
	$checkItemList = array_merge($checkItemList, $list->slotData);
	if ($top) {
		//echo 'Top<br>';
		$yVal = $unitDat[2]+120;
		$mapSlot = floor($yVak/120)*120+floor($xVal/120);
		$list = checkSlot($mapSlot, $mapSlotFile, 404);
		$checkItemList = array_merge($checkItemList, $list->slotData);
	}
	else if ($bot) {
		//echo 'Bot<br>';
		$yVal = $unitDat[2]-120;
		$mapSlot = floor($yVal/120)*120+floor($xVal/120);
		$list = checkSlot($mapSlot, $mapSlotFile, 404);
		$checkItemList = array_merge($checkItemList, $list->slotData);
	}
}
else if ($right) {
	//echo 'Right<br>';
	$xVal = $unitDat[1] + 120;
	$mapSlot = floor($unitDat[2]/120)*120+floor($xVal[1]/120);
	$list = checkSlot($mapSlot, $mapSlotFile, 404);
	$checkItemList = array_merge($checkItemList, $list->slotData);
	if ($top) {
		$yVal = $unitDat[2]+120;
		$mapSlot = floor($yVal/120)*120+floor($xVal/120);
		$list = checkSlot($mapSlot, $mapSlotFile, 404);
		$checkItemList = array_merge($checkItemList, $list->slotData);
	}
	else if ($bot) {
		$yVal = $unitDat[2]-120;
		$mapSlot = floor($yVal/120)*120+floor($xVal/120);
		$list = checkSlot($mapSlot, $mapSlotFile, 404);
		$checkItemList = array_merge($checkItemList, $list->slotData);
	}
}

if ($top) {
	$yVal = $unitDat[2]+120;
	$mapSlot = floor($yVal/120)*120+floor($unitDat[1]/120);
	$list = checkSlot($mapSlot, $mapSlotFile, 404);
	$checkItemList = array_merge($checkItemList, $list->slotData);
}
else if ($bot) {
	$yVal = $unitDat[2]-120;
	$mapSlot = floor($yVal/120)*120+floor($unitDat[1]/120);
	$list = checkSlot($mapSlot, $mapSlotFile, 404);
	$checkItemList = array_merge($checkItemList, $list->slotData);
}

fclose($mapSlotFile);

// Check the unit list for intersections
for ($i=0; $i<sizeof($checkItemList); $i++) {
	fseek($unitFile, $checkItemList[$i]*$defaultBlockSize);
	$checkDat = unpack('i*', fread($unitFile, 400));

	if ($checkDat[4] == 2) {
		$distCheck = ($checkDat[1]-$unitDat[1])*($checkDat[1]-$unitDat[1])+($checkDat[2]-$unitDat[2])*($checkDat[2]-$unitDat[2]);
		if ($distCheck <= $oRadiusSq) {
			$workItems[$checkItemList[$i]] = $checkDat[10];
		}
	}
}

// Output each item to an order option
if (sizeof($workItems) > 0) {
	foreach($workItems as $bldgID => $bType) {
		echo '
		addDiv("jobOptions_'.$bldgID.'", "cButtons", document.getElementById("taskDtlContent"));
		textBlob("1", "jobOptions+'.$bldgID.'", "Do you wish to work at this location?");

		var opt1 = optionButton("", "jobOptions_'.$bldgID.'", "1");
		opt1.addEventListener("click", function() {scrMod("1061,'.$postVals[1].','.$bldgID.',1")});

		var opt2 = optionButton("", "jobOptions_'.$bldgID.'", "2");
		opt2.addEventListener("click", function() {scrMod("1061,'.$postVals[1].','.$bldgID.',2")});

		var opt3 = optionButton("", "jobOptions_'.$bldgID.'", "3");
		opt3.addEventListener("click", function() {scrMod("1061,'.$postVals[1].','.$bldgID.',3")});

		var opt4 = optionButton("", "jobOptions_'.$bldgID.'", "4");
		opt4.addEventListener("click", function() {scrMod("1061,'.$postVals[1].','.$bldgID.',4")});

		var gotoBox = addDiv("goto_'.$bldgID.'", "", "jobOptions_'.$bldgID.'");
		gotoBox.innerHTML = "G";
		gotoBox.addEventListener("click", function() {goto()});
		';

	}
} else {
	echo 'Nothing';
}

function checkSlot($slotNum, $slotFile) {
	echo 'Look up slot '.$slotNum.'<br>';
	return new itemSlot($slotNum, $slotFile, 404);
}

?>
