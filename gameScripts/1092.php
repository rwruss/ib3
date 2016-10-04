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
$useBldg = loadUnit($postVals[1], $unitFile, 400);
$trgUnit = loadUnit($useBldg->unitDat[$postVals[2]+18], $unitFile, 400);
$trgTown = loadUnit($useBldg->get('parentCity'), $unitFile, 400);

// Load applicable building and character boosts

/*/// Run through list of city offices/characters and apply relevant boosts
$numTraits = 100;
$numBuildings = 100;
// Load buff mask for this production item
	$buffFile = fopen($scnPath.'/type2buffs.bdf', 'r+b');
	fseek($buffFile, $trgUnit->get('uType')*($numTraits+$numBuildings)*2);
	$buffDat = fread($buffFile, ($numTraits+$numBuildings)*2);
	$charBuffMask = unpack('s*', substr($buffDat, 0, $numTraits*2);
	$bldgBuffMask = unpack('s*', substr($buffDat, $numTraits*2);


$totalBuff = 0;
if ($trgTown->get($townerLeaders) > 0 ) {
	// Load list of city characters
	$townLeaders = new itemSlot($trgTown->get('townLeaders'), $slotFile, 40);
	$leaderList = [];
	for ($i=1; $i<sizeof($townLeaders->slotData); $i+=2) {
		$leaderList[] =
	}

	// Load character and traits
	for ($j=0; $j<sizeof($leaderList); $j++) {
		$testChar = loadUnit($leaderList[$j], $unitFile, 400);
		$charTraits = new itemSlot($testchar->get('traitSlot'), $slotFile, 40);

		for ($i=1; $i<=sizeof($charTraits->slotData); $i++) {
			$totalBuff += $charBuffMask[$charTraits->slotData];
		}
	}

	// Load and apply boosts from city buildings
	$bldgList = [];
	$townBuildings = new itemSlot($trgTown->get('buildingSlot'), $slotFile, 40);
	for ($i=1; $i<=sizeof($townBuildings->slotData); $i++) {
		$checkBuilding = loadUnit($townBuildings->slotData[$i], $unitFile);
		$bldgList[] = $checkBuilding->get('uType');
	}

	for ($i=0; $i<=sizeof($bldgList); $i++) {
		$totalBuff += $bldgBuffMask[$bldgList[$i]];
	}
}

//Load and apply boosts from common/joint city leaders
if ($trgTown->get('parentCity') > 0) {
	$bigCity = loadUnit($trgTown->get('parentCity'), $unitFile, 400);
	$bigLeaders = new itemSlot($bigCity->get('townLeaders'), $slotFile, 40);
	$leaderList = [];
	for ($i=1; $i<sizeof($townLeaders->slotData); $i+=2) {
		$leaderList[] =
	}

	// Load character and traits
	for ($j=0; $j<sizeof($leaderList); $j++) {
		$testChar = loadUnit($leaderList[$j], $unitFile, 400);
		$charTraits = new itemSlot($testchar->get('traitSlot'), $slotFile, 40);

		for ($i=1; $i<=sizeof($charTraits->slotData); $i++) {
			$totalBuff += $charBuffMask[$charTraits->slotData];
		}
	}

	//Load and apply boosts from common/joint city buildings
	$bldgList = [];
	$townBuildings = new itemSlot($bigCity->get('buildingSlot'), $slotFile, 40);
	for ($i=1; $i<=sizeof($townBuildings->slotData); $i++) {
		$checkBuilding = loadUnit($townBuildings->slotData[$i], $unitFile);
		$bldgList[] = $checkBuilding->get('uType');
	}

	for ($i=0; $i<=sizeof($bldgList); $i++) {
		$totalBuff += $bldgBuffMask[$bldgList[$i]];
	}
}

*/



// Caluclate action points available
echo 'Rate is '.$useBldg->unitDat[17].' Time is '.(time()-$useBldg->unitDat[27]);
$actionPoints = min(1000, $useBldg->unitDat[16] + 100*floor((time()-$useBldg->unitDat[27])*$useBldg->unitDat[17]/360000));


$availablePoints = min($postVals[3], $useBldg->actionPoints());
//echo 'Points available min('.$actionPct[$postVals[3]].', '.$useBldg->actionPoints().'):'.$availablePoints.' = '.$useBldg->unitDat[16].' +  Time: '.time().' - '.$useBldg->unitDat[27].' = '.(time()-$useBldg->unitDat[27]);
echo 'Since update 100*floor(('.time().' - '.$useBldg->unitDat[27].') * '.$useBldg->unitDat[17].'/360000) = ('.($useBldg->unitDat[16] + 100*floor((time()-$useBldg->unitDat[27])*$useBldg->unitDat[17]/360000)).')';
// Calculate points needed to complete the training


$neededPts = $trgUnit->unitDat[19]-$trgUnit->unitDat[18];
$usedPoints = min($availablePoints, $neededPts);

echo 'Use '.$usedPoints.' of '.$actionPoints.'  Points to add to production of object #'.$useBldg->unitDat[$postVals[2]+18];

// Record new stats for unit production
if ($usedPoints > 0) {
	// Updadate stats for producing building
	$useBldg->unitDat[16] = $actionPoints-$usedPoints;
	$useBldg->unitDat[27] = time();

	if ($trgUnit->unitDat[18]+$usedPoints >= $trgUnit->unitDat[19]) {
		echo 'Unit is complete<br>';
		// Process completion of unit training
		$trgUnit->unitDat[1] = $useBldg->unitDat[1];
		$trgUnit->unitDat[2] = $useBldg->unitDat[2];
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
				$charSlot->addItem($useBldg->unitDat[$postVals[2]+18],$slotFile);
				break;

			case 6:
				// made a new warband

				// Record in player unit list
				echo 'Add to slot '.$playerDat[22];
				$unitList = new itemSlot($playerDat[22], $slotFile, 40);
				$unitList->addItem($useBldg->unitDat[$postVals[2]+18],$slotFile);
				break;

			case 8:
				// made a new warband

				// Record in player unit list
				echo 'Add to slot '.$playerDat[22];
				$unitList = new itemSlot($playerDat[22], $slotFile, 40);
				$unitList->addItem($useBldg->unitDat[$postVals[2]+18],$slotFile);
				break;
		}

		// Release building production slot
		$useBldg->unitDat[$postVals[2]+18] = 0;

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
			$mapSlot->addItem($mapSlotFile, pack('i*', $useBldg->unitDat[$postVals[2]+18], 0), $loc); // unit ID, not visible

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
	echo '<script>unitList.newUnit({unitType:"trainingUnit", unitID:'.$useBldg->unitDat[$postVals[2]+18].', unitName:"Training", trainPts:'.($trgUnit->unitDat[18]).', trainReq:'.$trgUnit->unitDat[19].'});</script>';
}

//print_r($trgUnit->unitDat);
fclose($unitFile);
?>
