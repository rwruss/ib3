<?php

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$intelFile = fopen($gamePath.'/intel.slt', 'rb');

// Spy/gather intel on an object
$srcUnit = loadUnit($postVals[1], $unitFile, 400);

// verify source has enough action points to perform the observation
$usePoints = min($srcUnit->actionPoints, $postVals[3]);
if ($usePoints < 1) {
	exit("No action points to use");
}

// Deduct the observation points used
$trgUnit->adjustEnergy(-$usePoints);

$trgUnit = loadUnit($postVals[2], $unitFile, 400);

// Load the source object gathering the intel

// Load the target object

// Compare source and target skills to determine outcome

// Produce a report of the info gathered

fclose($intelFile);
fclose($unitFile);

?>