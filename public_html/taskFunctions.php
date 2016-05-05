<?php


function createTask($taskFile, $parameters) {
	clearstatcache();
	if (flock($taskFile, LOCK_EX)) {
		fseek($taskFile, 0, SEEK_END);
		$endPos = ftell($taskFile);
		//$newTaskID = floor(max(1,filesize($gamePath.'/tasks.tdt')/200));
		$newTaskID = $endPos/200;
		fseek($taskFile, $newTaskID*200);
		fwrite($taskFile, $parameters);
		if (strlen($parameters < 200)) {
			fseek($taskFile, $newTaskID*200+196);
			fwrite($taskFile, pack('i', 0));
		}

	return $newTaskID;
	}

	/*
	// Save task
	clearstatcache();
	if (strlen($parameters>200)) {
		$dataBlocks = str_split($parameters, 196);
		$firstBlock = floor(max(1,filesize($gamePath.'/tasks.tdt')/200));
		for ($i=0; $i<sizeof($dataBlocks)-1; $i++) {
			fseek($taskFile, ($firstBlock+$i)*200);
			fwrite($taskFile, $parameters.pack('i', $firstBlock+$i+1));
		}
		fseek($taskFile, ($firstBlock+sizeof($dataBlocks))*200);
		fwrite($taskFile, $parameters.pack('i', $dataBlocks[sizeof($dataBlocks)-1]));
		fseek($taskFile, ($newTaskID+$i)*200+196);
		fwrite($taskFile, pack('i', 0));
	} else {
		$newTaskID = floor(max(1,filesize($gamePath.'/tasks.tdt')/200));
		fseek($taskFile, $newTaskID*200);
		fwrite($taskFile, $parameters);
		fseek($taskFile, $newTaskID*200+196);
		fwrite($taskFile, pack('i', 0));
	}

	// Get base Time
	fseek($taskFile, 0);
	$lastTime = unpack('i', fread($taskFile, 4));

	// Add task to list of tasks at expected completion time (per minute)
	if ($lastTime[1] <= 0) {
		date_default_timezone_set('America/Chicago');
		$baseTime = floor(mktime(0,0,0,1,1,2016)/60);
	} else {
		$baseTime = $lastTime[1];
	}
	// get start index for the index time and add this event to that slot (if the duration is greater than 0)
	if ($duration > 0) {
		$now = floor(time()/60);
		$indexTime = $now+$duration-$baseTime;



		if (filesize($gamePath.'/tasks.tix') < $indexTime*4+4) {
			$newSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
			fseek($taskIndex, $indexTime*4);
			fwrite($taskIndex, pack('i', $newSlot));
		} else {
			fseek($taskIndex, $indexTime*4);
			$startSlot = unpack('i', fread($taskIndex, 4));
			$newSlot = $startSlot[1];
		}
		echo 'add a task to slot '.$newSlot.'<br>';
		addDataToSlot($gamePath."/gameSlots.slt", $newSlot, pack("N", $indexTime), $slotFile);
	}
	return $newTaskID;
	*/
}

/*
function createTask($taskFile, $taskIndex, $duration, $parameters, $gamePath) {
	date_default_timezone_set('America/Chicago');
	$baseTime = floor(mktime(0,0,0,1,1,2016)/60);
	$now = floor(time()/60);
	$indexTime = $now+$duration-$baseTime;
	$endTime = $now+$duration;
	echo $now.' + '.$duration.' - '.$baseTime.' = '.($indexTime);
	clearstatcache();
	$newTaskID = filesize($gamePath.'/tasks.tdt')/200;
	if (filesize($gamePath.'/tasks.tix') < ($indexTime)*4+4) {
	} else {
		fseek($taskIndex, $indexTime*4);
		$index = unpack('i', fread($taskIndex, 4));
		$checkTask = $index[1];

		fseek($taskFile, $checkTask*200);
		$taskDat = unpack('i*', fread($taskFile, 200));
		//$nextTask = $taskDat[2];
		if ($taskDat[4] > $endTime) {
			do {
				$nextTask = $taskDat[1];
				fseek($taskFile, $nextTask*200);
				$taskDat = unpack('i*', fread($taskFile, 200));
			} while ($taskDat[4] > $endTime);
		// Record the last checked ID as the previous task for the new task
		fseek($taskFile, $newTaskID*200);
		fwrite($taskFile, pack('i', $nextTask));

		// Record the new task in the previous and next tasks as well
		fseek($taskFile, $nextTask*200+4);
		fwrite($taskFile, pack('i', $newTaskID));
		}
		elseif ($taskDat[4] < $endTime) {
			do {
				fseek($taskFile, $nextTask*200);
				$taskDat = unpack('i*', fread($taskFile, 200));
				$nextTask = $taskDat[2];
			} while ($taskDat[4] < $endTime);
		} else {
		// end times are equal
		}
	}
}
*/
?>
