<?php

include('./slotFunctions.php');
include('./unitClass.php');
include('./cityClass.php');
/*
Process work on a building production item
Post Vals 1 = Building ID, 2 = Production Slot #, 3 = %
*/
echo 'Work on item '.$postVals[1];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');

// Get data for building producing the item
$useBldg = new building($postVals[1], $unitFile);

// Caluclate action points available
$divisor = max(1,$useBldg->bldgData[17]);
$actionPoints = min(1000, $useBldg->bldgData[16] + floor((time()-$useBldg->bldgData[27])/$divisor));

$actionPct = [0, 100, 250, 500, 1000];
$availablePoints = min($actionPct[$postVals[3]], $actionPoints);

// Calculate points needed to complete the training
$trgUnit = new unit($useBldg->bldgData[$postVals[2]], $unitFile, 400);

$neededPts = $trgUnit->unitDat[19]-$trgUnit->unitDat[18];
$usedPoints = min($availablePoints, $neededPts);

echo 'Use '.$usedPoints.' Points';

// Record new stats for unit production
if ($usedPoints > 0) {
	// Updadate stats for producing building
	$useBldg->bldgData[16] -= $usedPoints;
	$useBldg->bldgData[27] = time();

	if ($trgUnit->unitDat[18]+$usedPoints >= $trgUnit->unitDat[19]) {
	// Process completion of unit training
		$trgUnit->unitDat[1] = $useBldg->bldgData[1];
		$trgUnit->unitDat[2] = $useBldg->bldgData[2];
		$trgUnit->unitDat[18] = 0;
		$trgUnit->unitDat[19] = 0;
		
		fseek($unitFile, $pGameID*$defaultBlockSize);
		$playerDat = unpack('i*', fread($unitFile, 400));
		$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
		
		switch ($trgUnit->unitDat[4]) {
			case 4:
				// made a new character
				
				// Record in player character list				
				$charSlot = new itemSlot($playerDat[19], $slotFile, 40);
				$charSlot->addItem($useBldg->bldgData[$postVals[2]]);
				break;
				
			case 6:
				// made a new warband
				
				// Record in player unit list				
				$unitList = new itemSlot($playerDat[22], $slotFile, 40)));
				$unitList->addItem($useBldg->bldgData[$postVals[2]]);
				break;
		}

		// Release building production slot
		$useBldg->bldgData[$postVals[2]] = 0;

		// Add unit to map location slot
		$mapSlotNum = floor($trgUnit->unitDat[2]/120)*120+floor($trgUnit->unitDat[1]/120);
		$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');

		$mapSlot = new blockSlot($mapSlotNum, $mapSlotFile, 404); /// start, file, size
		$mapSlot->addItem($mapSlotFile, pack('i*', $useBldg->bldgData[$postVals[2]], 0)); // unit ID, not visible

		fclose($mapSlotFile);
	} else {
		// Update stats for unit in production
		$trgUnit->unitDat[18] += $usedPoints;
	}
	$useBldg->unitDat[27] = time();

	$useBldg->saveAll($unitFile);
	$trgUnit->saveAll($unitFile);
}

fclose($unitFile);
?>
