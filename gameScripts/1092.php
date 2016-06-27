<?php

include('./slotFunctions.php');
include('./unitClass.php');
include('./cityClass.php');
/*
Process work on a building production item
Post Vals 1 = Building ID, 2 = Production Slot #, 3 = %
*/
echo 'Work at building '.$postVals[1];
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');

// Get data for building producing the item
$useBldg = new building($postVals[1], $unitFile);

// Caluclate action points available
echo 'Rate is '.$useBldg->bldgData[17].' Time is '.(time()-$useBldg->bldgData[27]);
$actionPoints = min(1000, $useBldg->bldgData[16] + 100*floor((time()-$useBldg->bldgData[27])*$useBldg->bldgData[17]/360000));

$actionPct = [0, 250, 500, 1000];
$availablePoints = min($actionPct[$postVals[3]], $actionPoints);
echo 'Points available min('.$actionPct[$postVals[3]].', '.$actionPoints.'):'.$availablePoints.' = '.$useBldg->bldgData[16].' +  Time: '.time().' - '.$useBldg->bldgData[27].' = '.(time()-$useBldg->bldgData[27]);
echo 'Since update 100*floor(('.time().' - '.$useBldg->bldgData[27].') * '.$useBldg->bldgData[17].'/360000) = ('.($useBldg->bldgData[16] + 100*floor((time()-$useBldg->bldgData[27])*$useBldg->bldgData[17]/360000)).')';
// Calculate points needed to complete the training
$trgUnit = new unit($useBldg->bldgData[$postVals[2]+18], $unitFile, 400);

$neededPts = $trgUnit->unitDat[19]-$trgUnit->unitDat[18];
$usedPoints = min($availablePoints, $neededPts);

echo 'Use '.$usedPoints.' of '.$actionPoints.'  Points to add to production of object #'.$useBldg->bldgData[$postVals[2]+18];

// Record new stats for unit production
if ($usedPoints > 0) {
	// Updadate stats for producing building
	$useBldg->bldgData[16] = $actionPoints-$usedPoints;
	$useBldg->bldgData[27] = time();

	if ($trgUnit->unitDat[18]+$usedPoints >= $trgUnit->unitDat[19]) {
		echo 'Unit is complete<br>';
		// Process completion of unit training
		$trgUnit->unitDat[1] = $useBldg->bldgData[1];
		$trgUnit->unitDat[2] = $useBldg->bldgData[2];
		$trgUnit->unitDat[5] = $pGameID; // Owner
		$trgUnit->unitDat[6] = $pGameID; // Controller
		$trgUnit->unitDat[7] = 1; // Controller

		$trgUnit->unitDat[18] = 0;
		$trgUnit->unitDat[19] = 0;

		fseek($unitFile, $pGameID*$defaultBlockSize);
		$playerDat = unpack('i*', fread($unitFile, 400));
		$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

		switch ($trgUnit->unitDat[4]) {
			case 4:
				// made a new character

				// Record in player character list
				echo 'Make a new character.';
				$charSlot = new itemSlot($playerDat[19], $slotFile, 40);
				$charSlot->addItem($useBldg->bldgData[$postVals[2]+18],$slotFile);
				break;

			case 6:
				// made a new war+band

				// Record in player unit list
				$unitList = new itemSlot($playerDat[22], $slotFile, 40);
				$unitList->addItem($useBldg->bldgData[$postVals[2]+18],$slotFile);
				break;
		}

		// Release building production slot
		$useBldg->bldgData[$postVals[2]+18] = 0;

		// Add unit to map location slot
		$mapSlotNum = floor($trgUnit->unitDat[2]/120)*120+floor($trgUnit->unitDat[1]/120);
		$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');
		if (flock($mapSlotFile, LOCK_EX)) {
			$mapSlot = new blockSlot($mapSlotNum, $mapSlotFile, 404); /// start, file, size
			$loc = sizeof($mapSlot->slotData);
			for ($m = 1; $m<sizeof($mapSlot->slotData); $m+=2) {
				if ($mapSlot->slotData[$m] == 0) {
					$loc = $m;
					break;
				}
			}
			$mapSlot->addItem($mapSlotFile, pack('i*', $useBldg->bldgData[$postVals[2]], 0), $loc); // unit ID, not visible

			fflush($mapSlotFile);
			flock($mapSlotFile, LOCK_UN);
		}
		fclose($slotFile);
		fclose($mapSlotFile);
	} else {
		//echo 'Unit is not complete '.$trgUnit->unitDat[18].' + '.$usedPoints.' < '.$trgUnit->unitDat[19];
		// Update stats for unit in production
		$trgUnit->unitDat[18] += $usedPoints;
	}
	$useBldg->unitDat[27] = time();

	$useBldg->saveAll($unitFile);
	$trgUnit->saveAll($unitFile);
	echo '<script>unitList.newUnit({unitType:"trainingUnit", unitID:'.$useBldg->bldgData[$postVals[2]+18].', unitName:"Training", trainPts:'.($trgUnit->unitDat[18]).', trainReq:'.$trgUnit->unitDat[19].'});</script>';
}

print_r($trgUnit->unitDat);
fclose($unitFile);
?>
