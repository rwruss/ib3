<?php

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
			// Adjust the building information to reflect a complete structure
			fseek($unitFile, $taskDat[11]*$defaultBlockSize);
			
		}		
	}

?>