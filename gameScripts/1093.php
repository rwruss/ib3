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
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
fseek($taskFile, $postVals[1]*$jobBlockSize);
$taskDat = unpack('i*', fread($taskFile, $jobBlockSize));
echo 'Task dat:';
print_r($taskDat);


// Get data for unit producing the item
$workUnit = new unit($postVals[2], $unitFile, 400);
//fseek($unitFile, $postVals[1]*$defaultBlockSize);
//$bldgDat = unpack('i*', fread($unitFile, 200));

// Caluclate action points available
$divisor = max(1,$workUnit->unitDat[17]);
$actionPoints = min(1000, $workUnit->unitDat[16] + floor((time()-$workUnit->unitDat[27])/$divisor));

$actionPct = [0, 250, 500, 1000];
//$maxPoints = $actionPct[$postVals[3]]*10;
$availablePoints = min($actionPct[$postVals[3]], $actionPoints);

$neededPts = $taskDat[5]-$taskDat[6];
$usedPoints = min($availablePoints, $neededPts);

echo 'Use '.$usedPoints.' Points';

// Record new stats for unit production
if ($usedPoints > 0) {
	// Updadate stats for producing building
	$workUnit->unitDat[16] -= $usedPoints;
	$workUnit->unitDat[27] = time();

	if ($taskDat[6]+$usedPoints >= $taskDat[5]) {
	// Process completion of building construction

	// create a new building
		$newBldg[1] = $workUnit->unitDat[1];
		$newBldg[2] = $workUnit->unitDat[2];
		$newBldg[16] = 0;
		$newBldg[17] = 1;

		$workUnit->unitDat[$postVals[2]] = 0;

		// Add unit to map location slot
		/*
		$mapSlotNum = floor($newBldg[2]/120)*120+floor($newBldg[1]/120);
		$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');

		$mapSlot = new itemSlot($mapSlotNum, $mapSlotFile, 404); /// start, file, size
		$mapSlot->addItem($useBldg->bldgData[$postVals[2]], $mapSlotFile); // value, file

		fclose($mapSlotFile);*/
	} else {
		// Update stats for unit in production
		$taskDat[6] += $usedPoints;
	}
	$trgUnit->unitDat[27] = time();
	$trgUnit->unitDat[16] -= $usedPoints;

	//$usedBldg->saveAll($unitFile);
	//$trgUnit->saveAll($unitFile);
}

fclose($unitFile);
fclose($taskFile);
?>
