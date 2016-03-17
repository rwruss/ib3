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

	// If task is not complete, add action point and subtract from the unit
	if ($taskDat[6] < $taskDat[5])  {
		$spentPoints = max($taskDat[5] - $taskDat[6], 20);
		
		$actionPoints -= $spentPoints;
		$taskDat[6] += $spentPoints;
		
		fseek($taskFile, $taskID*100+20);
		fwrite($taskFile, pack('i', $taskDat[6]));
		
		fseek($unitFile, $postVals[1]*$defaultBlockSize+60);
		fwrite($unitFile, pack('i', $actionPoints));
		
		fseek($unitFile, $postVals[1]*$defaultBlockSize+104);
		fwrite($unitFile, pack('i', time()));
		
		if ($taskDat[6] >= $taskDat[5]) { // process completion of task
		
		}		
	}

	// If task is complete, make no changes

	fclose($taskFile);
}
fclose($unitFile);

echo '<script>
	alert("action point added!");
</script>';

?>