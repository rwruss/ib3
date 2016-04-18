<?php

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
$unitID = $postVals[1];
// Process if unit has action points to spend
$divisor = max(1,$unitDat[17]);
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/$divisor));

$actionPct = [0, 10, 25, 50, 100];
$maxPoints = $actionPct[$postVals[3]]*10;
$usePoints = min($maxPoints, $actionPoints);

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
		setBarSize("tDtl_'.$postVals[2].'_prog", '.($taskDat[6]/$taskDat[5]).', 150);
		setBarSize("tSum_'.$postVals[2].'_prog", '.($taskDat[6]/$taskDat[5]).', 150);
		//alert("'.$spentPoints.' total action point added!");
	</script>';
}
fclose($unitFile);



?>
