<?php

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Load unit exeperience for doing tasks


// Load unit desc to determine what tasks this unit can do
$unitDesc = explode('<->', file_get_contents($gamePath.'/units.desc'));
$unitTasks = explode($unitDesc[$unitDat[10]*x+x]);

// Load task file to get list of tasks that can be done by this unit
$taskDesc = explode('<->', file_get_contents($gamePath.'/tasks.desc'));

for ($i=0; $i<sizeof($unitTasks); $i++) {
	echo 'var task = unitTaskOpt('.$i.', "ordersContent", "'.$taskDesc[$i+x].'");';
}

?>
