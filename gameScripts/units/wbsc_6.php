<?php

echo 'Warband type 6<br>
Unit info detail<br>';

// Get information about the taske that is currently being worked on
if ($unitDat[11] > 0) {
	// Load task
	$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
	fseek($taskFile, $unitDat[11]*200);
	$taskDat = unpack('i*', fread($taskFile, 200));

	include('../gameScripts/units/tp_'.$taskDat[5].'.php');
} else {
	echo 'This unit is not currently working on anything';
}

// Get list of tasks available at the home city or the home army
fseek($unitFile, $unitDat[12]*400);
$homeDat = unpack('i*', fread($unitFile, 400));

if ($homeDat[4] == 1) {
	// unit is based in a city

} else {
	// unit is part of an army
}

?>
