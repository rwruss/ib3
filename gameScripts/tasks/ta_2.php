<?php

// If task is not complete, add action point and subtract from the unit
	$spentPoints = 0;
	if ($taskDat[6] < $taskDat[5])  {
		$spentPoints = min($taskDat[5] - $taskDat[6], 20);

		$actionPoints -= $spentPoints;
		$taskDat[6] += $spentPoints;



		//echo 'Record '.$taskDat[6].' points';

		fseek($taskFile, $postVals[2]*200+20);
		fwrite($taskFile, pack('i', $taskDat[6]));

		fseek($unitFile, $postVals[1]*$defaultBlockSize+60);
		fwrite($unitFile, pack('i', $actionPoints));

		fseek($unitFile, $postVals[1]*$defaultBlockSize+104);
		fwrite($unitFile, pack('i', time()));

		if ($taskDat[6] >= $taskDat[5]) { // process completion of task
			// Adjust the building information to reflect a complete structure
			fseek($unitFile, $taskDat[11]*$defaultBlockSize+24);
			fwrite($unitFile, pack('i', 1));
			fseek($unitFIle, $taskDat[11]*$defaultBlockSize+104);
			fwrite($unitFIle, pack('i', time()));
		}
	}

?>
