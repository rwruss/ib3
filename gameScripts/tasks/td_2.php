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

$noUnitsHere = true;
foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*$defaultBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize);
	
	
	if ($unitDat[4] == 8) { // this is an elligable civilian unit
		if ($unitDat[1] == $taskDat[1] && $unitDat[2] == $taskDat[2]) {
			// this unit is at the task location and can add points
			$noUnitsHere = false;
			
			// Get total number of production points available for this unit
			$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/$unitDat[17]));			
			
			// Show option to add production points to this task
			echo '<script>
			newUnitDetail('.$unitID.', "militaryContent");
			//newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
			document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
			document.getElementById("Udtl_'.$unitID.'").addEventListener("click", function() {scrMod("1046,'.$unitID.','.$postVals[1].'")});
			setUnitAction('.$unitID.', '.($actionPoints/1000).');
			setUnitExp('.$unitID.', 0.5);</script>';
		}
	}
	
}

if ($noUnitsHere) {
		echo 'There are no units here that can contribute to the progress of this task.';
	}
fclose($slotFile);
fclose($unitFile);
?>
