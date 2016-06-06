<?php
// Show current tasks/projects in the city
include("./slotFunctions.php");

// verify that user is ok to view this info
$cityID = $_SESSION['selectedItem'];
// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

//print_r($cityDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
//$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);
//echo 'Approved level '.$approved.'<br>';
echo '<script>
useDeskTop.newPane("tasks");
thisDiv = useDeskTop.getPane("tasks");';
if ($approved) {
	// Read the block data for the projects in progress
	if ($cityDat[21] > 0) {
		//echo 'Project slot is '.$cityDat[21];
		$taskList = new itemSlot($cityDat[21], $slotFile, 40);
		$taskSize = sizeof($taskList->slotData);
		$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
		//echo '<div id="incomplete" style="width:100%; display:inline"></div>';
		for ($i=1; $i<=$taskSize; $i++) {
			if ($taskList->slotData[$i] > 0) {
				fseek($taskFile, $taskList->slotData[$i]*$jobBlockSize);
				$taskDtl = unpack('i*', fread($taskFile, $jobBlockSize));
				//print_r($taskDtl);
				if ($taskDtl[3] > $taskDtl[4]) { // Task is not complete
					$requiredPoints = max(1000,$taskDtl[7]);
					echo 'taskList.newUnit({unitType:"task", unitID:'.$taskList->slotData[$i].', unitName:"char name", actionPoints:'.$taskDtl[6].', reqPts:'.$taskDtl[5].', strength:75});
					taskList.renderSum('.$taskList->slotData[$i].', thisDiv);';
					//echo 'newTaskSummary("'.$taskList->slotData[$i].'", "incomplete", '.($taskDtl[6]/$requiredPoints).');';

					//echo 'Incomplete: <div onclick="makeBox(\'taskDtl\', \'1040,'.$taskList[$i].'\', 500, 500, 200, 50);">'.$i.' - '.$taskList[$i].')Task Type '.$taskDtl[7].' is '.$taskDtl[6].'/'.$taskDtl[5].' Complete</div>';
				} else {
					$requiredPoints = max(1000,$taskDtl[7]);
					echo 'taskList.newUnit({unitType:"task", unitID:'.$taskList->slotData[$i].', unitName:"char name", actionPoints:'.$taskDtl[6].', reqPts:'.$taskDtl[5].', strength:75});
					taskList.renderSum('.$taskList->slotData[$i].', thisDiv);';
					//echo 'newTaskSummary("'.$taskList->slotData[$i].'", "incomplete", '.($taskDtl[6]/$requiredPoints).');';
					//echo 'Complete ('.$jobBlockSize.'): <div onclick="makeBox(\'taskDtl\', \'1040,'.$taskList[$i].'\', 500, 500, 200, 50);">'.$i.' - '.$taskList[$i].')Task Type '.$taskDtl[7].' is '.$taskDtl[6].'/'.$taskDtl[5].' Complete</div>';
				}
			}
		}
		fclose($taskFile);
		echo '</script>';
	} else {
		echo 'No tasks right now';
	}
} else {
	echo 'You are not authorized to view this information.';
}

?>
