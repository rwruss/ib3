<?php

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Process if unit has action points to spend
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/$unitDat[17]));	

if ($actionPoints > 20) {
	
	// Load Task Data
	$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
	fseek($taskFile, $taskID*100);
	$taskDat = unpack('i*', fread($taskFile, 100));

	include('./tasks/ta_'.$taskDat[7].'.php');

	// If task is complete, make no changes

	fclose($taskFile);
}
fclose($unitFile);

echo '<script>
	alert("action point added!");
</script>';

?>