<?php

include("./slotFunctions.php");
// Process a type 1 task (gathering from an area on the map)

// Determine the amount of action points to use

// Load the map informatuion for the affected area 
$mapSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);

$mapEffects = new mapEffectSlot($mapSlot, $slotFile, 404); //$start, $slotFile, $size

$now = time();

for ($i=sizeof($mapEffects->slotData); $i>0; $i-=5) {
	// Check if the event is too old to consider
	if ($mapEffects->slotData[$i-1] + 345600) {
		break;
	} else {
		
	}
}

// Determine amount of resources used

// Record the event for future actions
?>