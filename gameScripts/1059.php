<?php

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Load unit exeperience for doing tasks


// Load unit desc to determine what tasks this unit can do
$unitDesc = explode('<-->', file_get_contents($gamePath.'/units.desc'));
$unitTasks = explode(',', $unitDesc[$unitDat[10]*9+8]);

// Load task file to get list of tasks that can be done by this unit
$jobDesc = explode('<->', file_get_contents($gamePath.'/jobs.desc'));

echo '<script>';
for ($i=0; $i<sizeof($unitTasks); $i++) {
	echo 'var task = unitTaskOpt('.$unitTasks[$i].', "ordersContent", "'.$jobDesc[$unitTasks[$i]*4+2].'");';
}
echo '</script>';
?>
