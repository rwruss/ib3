<?php

if (isset($unitAssign)) {
  if ($unitAssign != 0) {
    echo 'Task type 2 Detail - unit assigned #'.$unitAssign.'<br>';
    print_r($taskDat);
  }
} else {
  echo 'Task type 2 Detail';
}

echo 'This task is construction of a building.  The task was started at '.date('m/d/y', $taskDat[4]).'<br>
This building has '.$taskDat[6].' of '.$taskDat[5].' required progress points.';

// Get list of workers avaialble to work on this task.
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

$playerObj = new player($playerDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerObj->unitSlot, 40)));
foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*$defaultBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize);
	
	if ($unitDat[4] == 8) { // this is an elligable civilian unit
		if ($unitDat[1] == $taskDat[1] && $unitDat[2] == $taskDat[2]) {
			// this unit is at the task location and can add points
			
			// Get total number of production points available for this unit
			
			// 
		}
	}
}
fclose($slotFile);
fclose($unitFile);
?>
