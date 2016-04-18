<?php

// Process an job order given to a unit

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$unitID = $_SESSION['selectedUnit'];

// Process if unit has action points to spend
$divisor = max(1,$unitDat[17]);
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/$divisor));

echo 'Points available: '.$actionPoints.'<br>';

$actionPct = [0, 10, 25, 50, 100];
$maxPoints = $actionPct[$postVals[3]]*10;
$usePoints = min($maxPoints, $actionPoints);

// Load unit exeperience for doing tasks


// Load unit desc to determine what tasks this unit can do
$unitDesc = explode('<-->', file_get_contents($gamePath.'/units.desc'));
$unitTasks = explode(',', $unitDesc[$unitDat[10]*9+8]);

// Load task file to get list of tasks that can be done by this unit
$jobDesc = explode('<->', file_get_contents($gamePath.'/jobs.desc'));
$jobType = explode(',', $jobDesc[$postVals[1]*4+1]);

// verify that unit can perform this task
if (array_search($postVals[1], $unitTasks) !== false) {
	//echo 'Approved - '.$jobType[0].'<p>';
	include('../gameScripts/1061-'.$jobType[0].'.php');
} else {
	echo 'This unit cannot perfrom this task';
}

?>
