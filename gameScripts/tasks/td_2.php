<?php

include("./unitClass.php");
date_default_timezone_set('America/Chicago');

if (isset($unitAssign)) {
  if ($unitAssign != 0) {
    echo 'Task type 2 Detail - unit assigned #'.$unitAssign.'<br>';
    //print_r($taskDat);
  }
} else {
  //echo 'Task type 2 Detail';
}

// Get list of workers avaialble to work on this task.
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$pDat = unpack('i*', fread($unitFile, 400));
$playerObj = new player($pGameID, $pDat, $unitFile);
//print_R($playerObj);
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
//$unitList = array_filter(unpack("i*", readSlotData($slotFile, $playerObj->unitSlot, 40)));
$unitList = new itemSlot($playerObj->get('unitSlot'), $slotFile, 40);
//echo 'Check unit tslot '.$playerObj->get('unitSlot');
$noUnitsHere = true;

echo '<script>
useDeskTop.newPane("characters");
thisDiv = useDeskTop.getPane("characters");

taskList.newUnit({unitType:"task", unitID:'.$postVals[1].', unitName:"Task #'.$postVals[1].' on '.$taskDat[11].'", actionPoints:'.$taskDat[6].', reqPts:'.$taskDat[5].', strength:75});
taskList.renderSum('.$postVals[1].', thisDiv)

var thisTask = makeTabMenu("newChars", thisDiv);
var taskDesc = newTab("newChars", 1, "Description");
var taskWork = newTab("newChars", 2, "Workers available");
tabSelect("newChars", 1);';

//print_r($unitList->slotData);
foreach ($unitList->slotData as $unitID) {
  //echo 'CHeck '.$unitID;
	//fseek($unitFile, $unitID*$defaultBlockSize);
	//$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
	$unitDetail = loadUnit($unitID, $unitFile);


	if ($unitDetail->get('uType') == 8) { // this is an elligable civilian unit
		if ($unitDetail->get('xLoc') == $taskDat[1] && $unitDetail->get('yLoc') == $taskDat[2]) {
			// this unit is at the task location and can add points
			$noUnitsHere = false;

			// Get total number of production points available for this unit


			// Show option to add production points to this task
      /*
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
        */
        echo 'unitList.newUnit({unitType:"warband", unitID:'.$unitID.', unitName:"unit name", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitDat[4].'});
        var orderBox = actionBox(taskWork, "1093,"+this.objectID, 100);
        unitList.renderSum('.$unitID.', orderBox.unitSpace);';
		} else {
      //echo 'Wrong loc';
    }
	} else {
    //echo 'Found '.$unitDat[4].'<br>';
  }

}
echo '</script>';
if ($noUnitsHere) {
		echo 'There are no units here that can contribute to the progress of this task.';
	}
fclose($slotFile);
fclose($unitFile);
?>
