<?php

/*
Process overview: When a area job order is recieved, the terrain data for that particular location is loaded.  Each terrain type has a set production value for the given resource types.
An array is created that represents each cell of the terrain area and the total production for that terrain type is stored.

Next, the terrain affects are loaded and referenced against the terrain cells.  Any impact to production is deducted from that cell.
Finally, the gathering object's parameters are loaded and caluclated for the particular resource.  Each terrain cell is the calculated to produce the minimum of the remaining production value
or the unit's production rate.
*/
// postvals 1 = resource ID, 2 = energy to use

//include("./slotFunctions.php");
$meSlotFile = fopen($gamePath.'/mapEffects.slt', 'r+b');

// Process a type 1 task (gathering from an area on the map)
$jobRadius = 10;  // Need to adjust this to be pulled from the task parameters
$jRowSize = 2*$jobRadius+1; // Row size for the job
$jobX = [$rscPoint[1]-$jobRadius, $rscPoint[1]+$jobRadius];
$jobY = [$rscPoint[2]-$jobRadius, $rscPoint[2]+$jobRadius];

// Load a job radius template
$jobDistanceMod = array_fill(0, $jRowSize*$jRowSize, 1);

// Determine the amount of action points to use
// 1= minimum amount, 2 = 25%, 3 = 50%, 4 = max
/*
$workLevel = [0, 20, 250, 500, 1000];
$divisor = max(1,$rscPoint[17]);
$actionPoints = min(1000, min($workLevel[$postVals[2]], $rscPoint[16] + floor((time()-$rscPoint[27])/$divisor)));
*/
// Load map terrain information for the base production from this area
$rowSize = 14400;
$terrainDat = '';
$terrainFile = fopen('../scenarios/common/terrainDat.dat', 'rb');
for ($rowCount = 0; $rowCount<$jobRadius*2+1; $rowCount++ ){
	fseek($terrainFile, ($jobY[0]+$rowCount)*$rowSize+$jobX[0]);
	$terrainDat .= fread($terrainFile, $jobRadius*2+1);
}
$terrainArray = unpack('C*', $terrainDat);

// Load the terrain description to get the max production values
echo 'Load resource production for resource ID #'.$postVals[1];
$rscList = explode('<->', file_get_contents($scnPath.'/resources.desc'));
$rscItem = explode('<-->', $rscList[$rscPoint[10]]);
$rscProd = explode(',', $rscItem[1]);

/*
echo 'Terrain production matrix:';
print_r($rscProd);
*/
// This puts the base amount of resource that this type of terrain produces into the jobArray for each tile
for ($i=0; $i=sizeof($terrainArray); $i++) {
	$jobArray[$i]=$rscProd[$terrainArray[$i+1]];
}

// Load the map effects informatuion for the affected area
echo 'Load map effects<br>';
$mapSlot = floor($rscPoint[2]/120)*120+floor($rscPoint[1]/120);
$mapEffects = new mapEventSlot($mapSlot, $meSlotFile, 404); //$start, $slotFile, $size

$now = time();

// Override loaded jobArray for testing
$jobArray = array_fill(0, $jRowSize*$jRowSize, 100);
//print_r($mapEffects->slotData);

for ($i=sizeof($mapEffects->slotData); $i>2; $i-=6) {
	// Event format: x location, y location, type, time, magnitude, radius

	// Check if the event is too old to consider
	if ($mapEffects->slotData[$i-2] + 345600 < $now) {
		echo 'Past the affect time';
		break;
	} else {
		// Determine if the radius of the effect overlaps with the job radius

		$xStart = max($mapEffects->slotData[$i-5]-$mapEffects->slotData[$i], $jobX[0]);
		$xEnd = min($mapEffects->slotData[$i-5]-$mapEffects->slotData[$i], $jobX[1]);
		$yStart = max($mapEffects->slotData[$i-4]-$mapEffects->slotData[$i], $jobY[0]);
		$yEnd = min($mapEffects->slotData[$i-4]-$mapEffects->slotData[$i], $jobY[1]);
		if ($xStart <= $xEnd && $yStart <= $yEnd) {
			// The boxes intersect so overlay the arrays
			$colStart = $xStart - ($mapEffects->slotData[$i-5]-$mapEffects->slotData[$i]);
			$colEnd = $xEnd-($mapEffects->slotData[$i-5]-$mapEffects->slotData[$i]);
			$colOffset = $mapEffects->slotData[$i-5]-$jobX[0];

			$rowStart = $yStart - ($mapEffects->slotData[$i-4]-$mapEffects->slotData[$i]);
			$rowEnd = $yEnd - ($mapEffects->slotData[$i-4]-$mapEffects->slotData[$i]);
			$rowOffset = $mapEffects->slotData[$i-4]-$jobY[0];

			$eRowSize = 2*$mapEffects->slotData[$i]+1; // Riw suze for the effects

			// Use the circle array that has the same radius as the effect
			switch ($mapEffects->slotData[$i]) {
				case 5:
					$circleArray = $radiusArray_5;
					break;
				case 10:
					$circleArray = $radiusArray_10;
					break;
				case 15:
					$circleArray = $radiusArray_15;
					break;
				case 20:
					$circleArray = $radiusArray_20;
					break;
			}
			$circleArray = array_fill(0, $eRowSize*$eRowSize, 1);
			for ($row=$rowStart; $row<=$rowEnd; $row++) {
				for ($col=$colStart; $col<=$colEnd; $col++) {
					// Add in the effects
					$jobArray[($row+$rowOffset)*$jRowSize+($col+$colOffset)] -= $circleArray[$row*$eRowSize+$col]*$mapEffects->slotData[$i-1];
				}
			}
		}
	}
}

echo 'Finished job array<p>';
//print_r($jobArray);

// Get unit data
echo 'Get data for unit '.$postVals[2];
fseek($unitFile, $postVals[2]*$defaultBlockSize);
$workingUnit = loadUnit($postVals[2], $unitFile, 400);
print_r($workingUnit->unitDat);
echo 'Working unit carry Slot '.$workingUnit->get('carrySlot');
//$unitDat = unpack('i*', fread($unitFile, 400));


// Check for perks based on army ID or commander
$cmdBoost = 1;
if ($rscPoint[15] > 0 ) {
	// Load the army to get the commander ID
	fseek($unitFile, $rscPoint[15]*$defaultBlockSize);
	$armyDat = unpack('i*', fread($unitFile, 200));


	if ($armyDat[10] > 0) {
		// Load the commander infomration to get the list of traits
		fseek($unitFile, $armyDat[10]*$defaultBlockSize);
		$cmdDat = unpack('i*', fread($unitFile, 200));

		if ($cmdDat[15] > 0) {
			$cmdTraits = new itemSlot($cmdDat[15], $unitSlotFile, 40);

			// Load the traits desc file to get the affects
			$traitItems = explode('<->', file_get_contents($scnPath.'/traits.desc'));
			for ($i=0; $i<sizeof($cmdTraits->slotData); $i++) {
				$traitMods = explode('<-->', $traitItems[$cmdTraits->slotData[$i]]);

				// Look through the loaded traits for a relevant resource boost
				$foundKey = array_search('rsc_'.$rscPoint[10], explode(',', $traitMods[1]));
				if ($foundKey) $cmdBoost += $traitMods[$foundKey+1];
			}
		}
	}
}

// Load unit descriptions
$unitList = explode('<->', file_get_contents($scnPath.'/units.desc'));
$typeDesc = explode('<-->', $unitList[$workingUnit->unitDat[10]]);
$unitBoosts = explode(',', $typeDesc[11]);


// Read the unit boots/nerfs
$unitMod = 1.0;
for ($i=0; $i<sizeof($unitBoosts); $i+=2) {
	if ($unitBoosts[$i] == $rscPoint[10]) {
		$unitMod += $unitBoosts[$i+1]/100;
		echo 'Boost for rsc '.$unitBoosts[$i].' now '.$unitMod;
	}
}
//$foundKey = array_search('rsc_'.$rscPoint[10], $unitBoosts);
//if ($foundKey) $unitMod = $unitBoosts[$foundKey+1];

// Load the unit experience
$unitSlotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$expBoost = 1.0;
/*
// Changed how experience is calced.  Need to update.

if ($rscPoint[14] > 0) {
$unitExp = new mapEventSlot($rscPoint[14], $unitSlotFile, 40);

	// Adjust the production rate based on experience
	for ($i=2; $i<sizeof($unitExp->slotData); $i+=2) {
		if ($unitExp->slotData[$i] == $postVals[1]) $expBoost = $unitExp->slotData[$i+1]/100;
	}
}
*/

// Calculate the production power of the unit given the order
$magnitude = 1 * $expBoost * $unitMod;

// Determine amount of resources collected -> compare the unit's gathering rate to the allowable amount for each terrain cell.
$collected = 0;
for ($i=0; $i<sizeof($jobArray); $i++) {
	$collected += min($jobArray[$i], $magnitude);
}
echo 'Collected '.$collected;

// Make new data for this event and record to map events.
$actionType = $rscPoint[10];
$eventData = pack('i*', $rscPoint[10], $postVals[2], $actionType, time(), $magnitude, $jobRadius);
$mapEffects->addItem($meSlotFile, $eventData, 1); //($testFile, $sendData, $addTarget);

// Save resources collected to unit slot
$carried = 0;

// Check to see if the unit is part of an army (if so, the rsc go to the army storage).
if ($workingUnit->unitDat[15] > 0) {
	$trgArmy = loadUnit($workingUnit->unitDat[15], $unitFile, 400);
	//$trgArmy = new army($workingUnit->unitDat[15], $unitFile, 400);
	$capacity = $trgArmy->get('carryCap');
	if ($trgArmy->get('carrySlot') == 0) {
		$newSlot = startASlot($unitSlotFile, "../users/userSlots.slt");
		$trgArmy->set('carrySlot', $newSlot);
		$trgArmy->saveAll($unitFile);
		$useRscSlot = $newSlot;
	} else $useRscSlot = $trgArmy->get('carrySlot');
} else {
	$capacity = $workingUnit->get('carryCap');
	echo 'Working unit stuff:';
	print_r($workingUnit->unitDat);
	if ($workingUnit->get('carrySlot') == 0) {
		$newSlot = startASlot($unitSlotFile, "../users/userSlots.slt");
		$workingUnit->set('carrySlot', $newSlot);
		$workingUnit->saveAll($unitFile);
		$useRscSlot = $newSlot;
	} else $useRscSlot = $workingUnit->get('carrySlot');
}

$unitRSC = new blockSlot($useRscSlot, $unitSlotFile, 40);
echo 'List of carried resources (Slot '.$useRscSlot.')';
print_r($unitRSC->slotData);
$rscStart = [0,0,0];
for ($i=1; $i<sizeof($unitRSC->slotData); $i+=2) {
	if ($unitRSC->slotData[$i] == $rscPoint[10]) {
		$rscStart[0] = $i;
		$rscStart[1] = $unitRSC->slotData[$i+1];
	}
	else if ($unitRSC->slotData[$i] == 0) $rscStart[2] = $i;
	$carried += $unitRSC->slotData[$i+1];
}

$capacity = $typeDesc[12];
//NEED TO VERIFY THAT THE CORRECT PRODUCTION ID IS USED - POSTVALS 1 IS THE ID OF THE FARM OBJECT.  NEED TO LOAD RESORUCE TYPE AND USE THAT.
if ($carried < $capacity) {
	$space = $capacity - $carried;
	$location = sizeof($unitRSC->slotData);
	if ($rscStart[0]>0) {
		$location = $rscStart[0];
	}
	else if ($rscStart[2] > 0) $location = $rscStart[2];

	echo 'Add new resoruces to locatoinb '.$location;

	$unitRSC->addItem($unitSlotFile, pack('i*', $rscPoint[10], $rscStart[1]+min($space, $collected)), $location);
} else {
	echo 'Can not carry any more (Max: '.$carried.'/'.$capacity.')<br>';
}

fclose($unitSlotFile);
fclose($meSlotFile);
fclose($unitFile);

?>
