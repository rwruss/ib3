<?php

// Process an job order given to a unit

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Load unit exeperience for doing tasks


// Load unit desc to determine what tasks this unit can do
$unitDesc = explode('<-->', file_get_contents($gamePath.'/units.desc'));
$unitTasks = explode(',', $unitDesc[$unitDat[10]*9+8]);

// Load task file to get list of tasks that can be done by this unit
$jobDesc = explode('<->', file_get_contents($gamePath.'/jobs.desc'));

// verify that unit can perform this task
if (array_search($postVals[1], $unitTasks) !== false) {
	echo 'Approved<p>';
	include('../gameScripts/1061-'.$jobDesc[$postVals[1]*4+1].'.php');
} else {
	echo 'This unit cannot perfrom this task';
}

?>
