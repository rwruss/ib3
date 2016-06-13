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
//$unitList = array_filter(unpack("i*", readSlotData($slotFile, $playerObj->unitSlot, 40)));
$unitList = new itemSlot($playerObj->unitSlot, $slotFile, 40);
$noUnitsHere = true;
//print_r($taskDat);

/*
newTabMenu("task_'.$postVals[1].'");
newTab("task_'.$postVals[1].'", 1, "Description");
workers = newTab("task_'.$postVals[1].'", 2, "Workers available");
tabSelect("task_'.$postVals[1].'", 1);

newTaskDetail("tDtl_'.$postVals[1].'", "task_'.$postVals[1].'_header", '.($taskDat[6]/$taskDat[5]).', 1);
*/
echo '<script>
useDeskTop.newPane("characters");
thisDiv = useDeskTop.getPane("characters");

taskList.newUnit({unitType:"task", unitID:'.$postVals[1].', unitName:"char name", actionPoints:'.$taskDat[6].', reqPts:'.$taskDat[5].', strength:75});
taskList.renderSum('.$postVals[1].', thisDiv)

var thisTask = makeTabMenu("newChars", thisDiv);
var taskDesc = newTab("newChars", 1, "Description");
var taskWork = newTab("newChars", 2, "Workers available");
tabSelect("newChars", 1);';

//print_r($unitList->slotData);
foreach ($unitList->slotData as $unitID) {
	fseek($unitFile, $unitID*$defaultBlockSize);
	$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));


	if ($unitDat[4] == 8) { // this is an elligable civilian unit
		if ($unitDat[1] == $taskDat[1] && $unitDat[2] == $taskDat[2]) {
			// this unit is at the task location and can add points
			$noUnitsHere = false;

			// Get total number of production points available for this unit
      $actionReplenishRate = max(1, $unitDat[17]);
      //$actionPoints = min(1000, $workUnit->unitDat[16] + floor((time()-$workUnit->unitDat[27])*$workUnit->unitDat[17]/360000));
			$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])*$unitDat[17]/360000));
      echo 'Replensih: '.$actionReplenishRate;

			// Show option to add production points to this task

      echo '
        unitList.newUnit({unitType:"warband", unitID:'.$unitID.', unitName:"unit name", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitDat[4].'});
        var objContain = addDiv("", "selectContain", taskWork);
  			unitList.renderSum('.$unitID.', objContain);
  			var newButton = optionButton("", objContain, "25%");
  			newButton.objectID = "'.$postVals[1].','.$unitID.',1";
  			newButton.addEventListener("click", function () {scrMod("1093,"+this.objectID)});
  			var newButton = optionButton("", objContain, "50%");
  			newButton.objectID = "'.$postVals[1].','.$unitID.',2";
  			newButton.addEventListener("click", function () {scrMod("1093,"+this.objectID)});
  			var newButton = optionButton("", objContain, "100%");
  			newButton.objectID = "'.$postVals[1].','.$unitID.',3";
  			newButton.addEventListener("click", function () {scrMod("1093,"+this.objectID)});';
		} else {
      //echo 'Wrong loc';
    }
	} else {
    //echo 'Found '.$unitDat[4].'<br>';
  }

}
echo '</script>';
if ($noUnitsHere) {
		//echo 'There are no units here that can contribute to the progress of this task.';
	}
fclose($slotFile);
fclose($unitFile);
?>
