<?php

echo 'Order/task detail for a unit';

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Load unit exeperience for doing tasks


// Load unit desc to determine what tasks this unit can do
$unitDesc = explode('<->', file_get_contents($scnPath.'/units.desc'));
$typeDesc = explode('<-->', $unitDesc[$unitDat[10]]);
$unitTasks = explode(',', $typeDesc[8]);

echo 'Tasks taht can be done:';
print_r($unitTasks);
echo '<br>Type '.gettype($unitTasks[0]).' ('.gettype($unitTasks[0]+100).')<br>';

// Load task file to get list of tasks that can be done by this unit
$jobsDesc = explode('<->', file_get_contents($scnPath.'/jobs.desc'));
$typeInfo = explode('<-->', $jobDesc[$postVals[1]]);
$jobType = explode(',', $typeInfo[1]);

if (array_search($postVals[1], $unitTasks) !== false) {
	echo 'Approved';
	include('../gameScripts/1060-'.$jobType[0].'.php');
} else {
	echo 'This unit cannot perfrom this task';
}

?>
