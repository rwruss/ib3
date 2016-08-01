<?php

// Load data for the targeted building
fseek($unitFile, $postVals[3]*$defaultBlockSize);
$bldgDat = unpack('i*', fread($unitFile, 400));

// Confirm that building type matches task type

// Calculate amount of action points available
$workLevel = [0, 20, 250, 500, 1000];
$divisor = max(1,$unitDat[17]);
$actionPoints = min(1000, min($workLevel[$postVals[2]], $unitDat[16] + floor((time()-$unitDat[27])/$divisor)));

// Calculate the amount of resources generated
$production = $actionPoints/1000 * $bldgDat[20]/100 * $bldgDat[18];
//echo 'Make something or something.'
?>
