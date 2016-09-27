<?php

/*
Post Vals: 1 = spy ID, 2 = target ID, 3 = actionpoints Used
*/

include('./unitClass.php');
include('./slotFunctions.php');


$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$intelFile = fopen($gamePath.'/intel.slt', 'rb');

// Load the source object gathering the intel
$spyUnit = loadUnit($postVals[1], $unitFile, 400);

// verify source has enough action points to perform the observation
$usePoints = min($spyUnit->actionPoints(), $postVals[3]);
if ($usePoints < 1) {
	exit("No action points to use");
}

// Deduct the observation points used
$spyUnit->adjustEnergy(-$usePoints);

// Load the target object
$trgUnit = loadUnit($postVals[2], $unitFile, 400);

// Compare source and target skills to determine outcome
$spySkills = new itemSlot($spyUnit->get('traitSlot'), $slotFile, 40);
$trgSkills = new itemSlot($tryUnit->get('traitSlot'), $slotFile, 40);

// Produce a report of the info gathered

fclose($intelFile);
fclose($unitFile);

?>