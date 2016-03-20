<?php

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Process if unit has action points to spend
$divisor = max(1,$unitDat[17]);
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/$divisor));

if ($actionPoints > 20) {

	// Load Task Data
	$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
	fseek($taskFile, $postVals[2]*200);
	$taskDat = unpack('i*', fread($taskFile, 100));

	include('../gameScripts/tasks/ta_'.$taskDat[7].'.php');

	// If task is complete, make no changes

	fclose($taskFile);

	echo '<script>
		setUnitAction('.$postVals[1].', '.($actionPoints/1000).');
		alert("'.$spentPoints.' total action point added!");
	</script>';
}
fclose($unitFile);



?>
