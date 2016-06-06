<?php

include('./slotFunctions.php');
include('./unitClass.php');
include('./cityClass.php');
/*
Process work on a building production item
Post Vals 1 = Building ID, 2 = Production Slot #, 3 = %
*/
echo 'Work on item '.$postVals[1];
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
$trgTask = new task($postVals[1], $taskFile);

// Get data for unit producing the item
$workUnit = new unit($postVals[2], $unitFile, 400);
//fseek($unitFile, $postVals[1]*$defaultBlockSize);
//$bldgDat = unpack('i*', fread($unitFile, 200));

// Caluclate action points available
$divisor = max(1,$workUnit->unitDat[17]);
$actionPoints = min(1000, $workUnit->unitDat[16] + floor((time()-$workUnit->unitDat[27])*$workUnit->unitDat[17]/360000));

$actionPct = [0, 250, 500, 1000];
//$maxPoints = $actionPct[$postVals[3]]*10;
$availablePoints = min($actionPct[$postVals[3]], $actionPoints);

$neededPts = $trgTask->taskDat[5]-$trgTask->taskDat[6];
$usedPoints = min($availablePoints, $neededPts);

echo 'Use '.$usedPoints.' Points';

// Record new stats for unit production
if ($usedPoints > 0) {
	// Updadate stats for producing building
	$workUnit->unitDat[16] -= $usedPoints;
	$workUnit->unitDat[27] = time();

	if ($trgTask->taskDat[6]+$usedPoints >= $trgTask->taskDat[5]) {
	// Process completion of building construction
	// create a new building
		echo 'Task complete ('.$trgTask->taskDat[6].' + '.$usedPoints.' >= '.$trgTask->taskDat[5].')';
		$newBuilding = new building($trgTask->taskDat[11], $unitFile);
		$newBuilding->bldgData = array_fill(1, 100, 0);
		$newBuilding->bldgData[1] = $workUnit->unitDat[1]; // Building X
		$newBuilding->bldgData[2] = $workUnit->unitDat[2]; // Building Y
		$newBuilding->bldgData[7] = 1; // Set Status to complete
		$newBuilding->bldgData[10] = $trgTask->taskDat[12]; // Building Type
		$newBuilding->bldgData[16] = 0; // Energy
		$newBuilding->bldgData[17] = 4167; // Energy Regen Rate
		$newBuilding->bldgData[27] = time(); // Update time

		$newBuilding->saveAll($unitFile);
		$trgTask->taskDat[6] += $usedPoints;

	} else {
		// Update stats for unit in production
		$trgTask->taskDat[6] += $usedPoints;
		$trgTask->saveAll($taskFile);
	}

	$workUnit->saveAll($unitFile);
}

echo '<script>unitList.change('.$postVals[2].', "actionPoints", '.$workUnit->unitDat[16].');
taskList.change('.$postVals[1].', "actionPoints", '.$trgTask->taskDat[6].')</script>';

fclose($unitFile);
fclose($taskFile);
?>
