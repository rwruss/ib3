<?php

include('./slotFunctions.php');
include('./unitClass.php');
include('./cityClass.php');
/*
Process work on a building production item
Post Vals 1 = Building ID, 2 = Production Slot #, 3 = %
*/

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

// Get data for building in production
$useBldg = new building($postVals[1], $unitFile);
//fseek($unitFile, $postVals[1]*$defaultBlockSize);
//$bldgDat = unpack('i*', fread($unitFile, 200));

// Caluclate action points available
$divisor = max(1,$useBldg->bldgData[17]);
$actionPoints = min(1000, $useBldg->bldgData[16] + floor((time()-$useBldg->bldgData[27])/$divisor));

$actionPct = [0, 100, 250, 500, 1000];
//$maxPoints = $actionPct[$postVals[3]]*10;
$availablePoints = min($actionPct[$postVals[3]], $actionPoints);

// Calculate points needed to complete the training
$trgUnit = new unit($useBldg->bldgData[$postVals[2]], $unitFile, 400);

$neededPts = $trgUnit->unitDat[17]-$trgUnit->unitDat[16];
$usedPoints = min($usePoints, $neeedPoints);


// Record new stats for unit production
else if ($usedPoints > 0) {
	// Updadate stats for producing building
	$useBldg->bldgData[16] -= $usedPoints;
	$useBldg->bldgData[27] = time();
	
	
	if ($trgUnit->unitDat[16]+$usedPoints >= $trgUnit->unitDat[17]) {
	// Process completion of unit training
		$trgUnit->unitDat[1] = $useBldg->bldgData[1];
		$trgUnit->unitDat[2] = $useBldg->bldgData[2];
		$trgUnit->unitDat[16] = 0;
		$trgUnit->unitDat[17] = 1;
		
		$useBldg->bldgData[$postVals[2]] = 0;
		
		// Add unit to map location slot
		$mapSlotNum = floor($trgUnit->unitDat[2]/120)*120+floor($trgUnit->unitDat[1]/120);
		$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');		

		$mapSlot = new itemSlot($mapSlotNum, $mapSlotFile, 404); /// start, file, size
		$mapSlot->addItem($useBldg->bldgData[$postVals[2]], $mapSlotFile); // value, file
		
		fclose($mapSlotFile);
	} else {
		// Update stats for unit in production
		$trgUnit->unitDat[16] += $usedPoints;
	}
	$trgUnit->unitDat[27] = time();
	
	$usedBldg->saveAll($unitFile);
	$trgUnit->saveAll($unitFile);
}

fclose($unitFile);
?>