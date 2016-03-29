<?php

date_default_timezone_set('America/Chicago');

if (isset($unitAssign)) {
  if ($unitAssign != 0) {
    echo 'Task type 2 Detail - unit assigned #'.$unitAssign.'<br>';
    print_r($taskDat);
  }
} else {
  //echo 'Task type 2 Detail';
}

// Get list of workers avaialble to work on this task.
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

$playerObj = new player($playerDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerObj->unitSlot, 40)));

$noUnitsHere = true;
//print_r($taskDat);
echo '<script>
newTabMenu("task_'.$postVals[1].'");
newTab("task_'.$postVals[1].'", 1, "Description");
newTab("task_'.$postVals[1].'", 2, "Workers available");
tabSelect("task_'.$postVals[1].'", 1);

newTaskDetail("tDtl_'.$postVals[1].'", "task_'.$postVals[1].'_header", '.($taskDat[6]/$taskDat[5]).', 1)';

foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*$defaultBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));


	if ($unitDat[4] == 8) { // this is an elligable civilian unit
		if ($unitDat[1] == $taskDat[1] && $unitDat[2] == $taskDat[2]) {
			// this unit is at the task location and can add points
			$noUnitsHere = false;

			// Get total number of production points available for this unit
      $actionReplenishRate = max(1, $unitDat[17]);
			$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/$actionReplenishRate));

			// Show option to add production points to this task

      echo '
			newUnitDetail('.$unitID.', "task_'.$postVals[1].'_tab2");
			document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
			document.getElementById("Udtl_'.$unitID.'").addEventListener("click", function() {scrMod("1046,'.$unitID.','.$postVals[1].'")});
			setUnitAction('.$unitID.', '.($actionPoints/1000).');
			setUnitExp('.$unitID.', 0.5);</script>';
		}
	}

}
echo '</script>';
if ($noUnitsHere) {
		//echo 'There are no units here that can contribute to the progress of this task.';
	}
fclose($slotFile);
fclose($unitFile);
?>
