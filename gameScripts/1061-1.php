<?php

include("./slotFunctions.php");
$meSlotFile = fopen($gamePath.'/mapEffects.slt', 'rb');

// Process a type 1 task (gathering from an area on the map)
$jobRadius = 10;
$jobX = [$unitDat[1]-$jobRadius, $unitDat[1]+$jobRadius];
$jobY = [$unitDat[2]-$jobRadius, $unitDat[2]+$jobRadius];

// Determine the amount of action points to use

// Load the map informatuion for the affected area
$mapSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);

$mapEffects = new mapEffectSlot($mapSlot, $meSlotFile, 404); //$start, $slotFile, $size

$now = time();
//print_r($mapEffects->slotData);
for ($i=$mapEffects->numEffects*6+1; $i>1; $i-=6) {
	// Check if the event is too old to consider
	if ($mapEffects->slotData[$i-2] + 345600) {
		echo 'Past the affect time';
		break;
	} else {
		// Determine if the radius of the effect overlaps with the job radius
		/*
		$dx = $mapEffects->$slotData[$i-5] - $unitDat[1];
		$dy = $mapEffects->$slotData[$i-4] - $unitDat[2];
		$r = $mapEffects->$slotData[$i] + $jobRadius;
		if ($dx*$dx + $dy*$dy <= $r*$r) {
		*/

		$xStart = max($mapEffects->$slotData[$i-5]-$mapEffects->$slotData[$i], $jobX[0]);
		$xEnd = min($mapEffects->$slotData[$i-5]-$mapEffects->$slotData[$i], $jobX[1]);
		$yStart = max($mapEffects->$slotData[$i-4]-$mapEffects->$slotData[$i], $jobY[0]);
		$yEnd = min($mapEffects->$slotData[$i-4]-$mapEffects->$slotData[$i], $jobY[1]);
		if ($xStart <= $xEnd && $yStart <= $yEnd) {
			// The boxes intersect so overlay the arrays
			$colStart = $xStart - ($mapEffects->$slotData[$i-5]-$mapEffects->$slotData[$i]);
			$colCount = $xEnd-$xStart+1;
			$rowStart =
			$rowCount = 
			for ($col=$)
		}
	}
}

// Determine amount of resources used

// Record the event for future actions

fclose($meSlotFile);
?>
