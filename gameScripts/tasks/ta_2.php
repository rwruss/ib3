<?php

// If task is not complete, add action point and subtract from the unit
	$spentPoints = 0;
	if ($taskDat[6] < $taskDat[5])  {
		$spentPoints = min($usePoints, ($taskDat[5] - $taskDat[6]));

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

		if ($taskDat[6] >= $taskDat[5]) { // process completion of task
			// Adjust the building information to reflect a complete structure
			fseek($unitFile, $taskDat[11]*$defaultBlockSize+24);
			fwrite($unitFile, pack('i', 1));
			fseek($unitFile, $taskDat[11]*$defaultBlockSize+104);
			fwrite($unitFile, pack('i', time()));
		}
	}

?>
