<?php

// postvals 1 = resource ID, 2 = energy to use

include("./slotFunctions.php");
$meSlotFile = fopen($gamePath.'/mapEffects.slt', 'rb');

// Process a type 1 task (gathering from an area on the map)
$jobRadius = 10;  // Need to adjust this to be pulled from the task parameters
$jobX = [$unitDat[1]-$jobRadius, $unitDat[1]+$jobRadius];
$jobY = [$unitDat[2]-$jobRadius, $unitDat[2]+$jobRadius];

// Determine the amount of action points to use
// 1= minimum amount, 2 = 25%, 3 = 50%, 4 = max
$workLevel = [0, 20, 250, 500, 1000];
$divisor = max(1,$unitDat[17]);
$actionPoints = min($workLevel[$postVals[2]], $unitDat[16] + floor((time()-$unitDat[27])/$divisor));

// Load map terrain information for the base production from this area
$rowSize = 14400;
for ($rowCount = 0; $rowCount<$jobRadius*2+1; $rowCount++ ){
	fseek($terrainFile, ($jobY[0]+$rowCount)*$rowSize+$jobX[0]);
	$terrainDat .= fread($terrainFile, $jobRadius*2+1);
}
$terrainArray = unpack('C*', $terrainDat);

// Load the terrain description to get the base production values
echo 'Load resource production for resource ID #'.$postVals[1];
$rscDesc = explode('<-->', file_get_contents($gamePath.'/rsc.desc'));
$rscProd = explode(',', $rscDesc[$postVals[1]]);
for ($i=0; $i=sizeof($terrainArray); $i++) {
	$jobArray[$i]=$rscProd[$terrainArray[$i+1]];
}

// Load the map effects informatuion for the affected area
$mapSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);
$mapEffects = new mapEffectSlot($mapSlot, $meSlotFile, 404); //$start, $slotFile, $size

$now = time();
$jRowSize = 2*$jobRadius+1; // Row size for the job
$jobArray = array_fill(0, $jRowSize*$jRowSize, 0);
//print_r($mapEffects->slotData);
for ($i=$mapEffects->numEffects*6+1; $i>1; $i-=6) {
	// Check if the event is too old to consider
	if ($mapEffects->slotData[$i-2] + 345600) {
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


			$effectArray = arra_fill(0, $eRowSize*$eRowSize, 1);
			for ($row=$rowStart; $row<=$rowEnd; $row++) {
				for ($col=$colStart; $col<=$colEnd; $col++) {
					// Add in the effects
					$jobArray[($row+$rowOffset)*$jRowSize+($col+$colOffset)] += $effectArray[$row*$eRowSize+$col]*$mapEffects->slotData[$i-1];
				}
			}
		}
	}
}

// Determine amount of resources collected
echo 'Collected '.array_sum($jobArray);

// Record the event for future actions

fclose($meSlotFile);
?>
