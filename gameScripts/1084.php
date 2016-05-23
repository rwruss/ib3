<?php

// Process an plot order given to a unit

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$unitID = $_SESSION['selectedItem'];

// Process if unit has action points to spend
$divisor = max(1,$unitDat[17]);
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/$divisor));

echo 'Points available: '.$actionPoints.'<br>';

$actionPct = [0, 10, 25, 50, 100];
$maxPoints = $actionPct[$postVals[3]]*10;
$usePoints = min($maxPoints, $actionPoints);

// Load unit exeperience for doing tasks


// Load unit desc to determine what tasks this unit can do
$charItems = explode('<->', file_get_contents($scnPath.'/chars.desc'));
$charDesc = explode('<-->', $charItems[$unitDat[10]]);
$charTasks = explode(',', $charDesc[8]);

print_r($charDesc);

// Load task file to get list of tasks that can be done by this unit
$jobDesc = explode('<->', file_get_contents($scnPath.'/plots.desc'));
//$jobType = explode(',', $jobDesc[$postVals[1]*4+1]);

// verify that unit can perform this task
if (array_search($postVals[1], $charTasks) !== false) {
	echo 'Approved to do the task';
	// Load Task Data
	$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
	fseek($taskFile, $postVals[2]*200);
	$taskDat = unpack('i*', fread($taskFile, 100));

	$spentPoints = $usePoints;

	//$spentPoints = min($usePoints, ($taskDat[5] - $taskDat[6]));

	echo 'Use '.$spentPoints.' points!';

	$actionPoints -= $spentPoints;
	$taskDat[6] += $spentPoints;
	//echo 'Record '.$taskDat[6].' points';

	fseek($taskFile, $postVals[2]*200+20);
	fwrite($taskFile, pack('i', $taskDat[6]));

	fseek($unitFile, $unitID*$defaultBlockSize+60);
	fwrite($unitFile, pack('i', $actionPoints));

	echo 'Record time '.time();
	fseek($unitFile, $unitID*$defaultBlockSize+104);
	fwrite($unitFile, pack('i', time()));
	/*
	if ($taskDat[6] >= $taskDat[5]) { // process completion of task
		// Adjust the building information to reflect a complete structure
		fseek($unitFile, $taskDat[11]*$defaultBlockSize+24);
		fwrite($unitFile, pack('i', 1));
		fseek($unitFile, $taskDat[11]*$defaultBlockSize+104);
		fwrite($unitFile, pack('i', time()));
	}
	*/
	$unitDat[16] = $actionPoints;

	fclose($taskFile);
echo '<script>unitList.change('.$unitID.', "actionPoints", '.$unitDat[16].');
unitList.add('.$postVals[2].', "actionPoints", '.$spentPoints.')</script>';
} else {
	echo 'This unit cannot perfrom this task';
}

fclose($unitFile);

?>
