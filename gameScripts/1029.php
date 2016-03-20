<?php
// Show current tasks/projects in the city
//print_r($postVals);
include("./slotFunctions.php");

// verify that user is ok to view this info
$cityID = $_SESSION['selectedItem'];
echo 'Show projects for city '.$cityID.'<br>';
// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

//print_r($cityDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);
echo 'Approved level '.$approved.'<br>';
if ($approved) {
	// Read the block data for the projects in progress
	if ($cityDat[21] > 0) {
		$taskDat = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[21], 40)));
		$taskSize = sizeof($taskDat);
		$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
		//print_r($taskDat);
		echo '<div id="incomplete" style="width:100%; display:inline"></div><script>';
		for ($i=1; $i<=$taskSize; $i++) {
			fseek($taskFile, $taskDat[$i]*$jobBlockSize);
			$taskDtl = unpack('i*', fread($taskFile, $jobBlockSize));
			//print_r($taskDtl);
			if ($taskDtl[3] > $taskDtl[4]) { // Task is not complete
				$requiredPoints = max(1000,$taskDtl[7]);
				echo 'newTaskDetail("'.$taskDat[$i].'", "incomplete", '.($taskDtl[6]/$requiredPoints).');';

				//echo 'Incomplete: <div onclick="makeBox(\'taskDtl\', \'1040,'.$taskDat[$i].'\', 500, 500, 200, 50);">'.$i.' - '.$taskDat[$i].')Task Type '.$taskDtl[7].' is '.$taskDtl[6].'/'.$taskDtl[5].' Complete</div>';
			} else {
				$requiredPoints = max(1000,$taskDtl[7]);
				echo 'newTaskDetail("'.$taskDat[$i].'", "incomplete", '.($taskDtl[6]/$requiredPoints).');';
				//echo 'Complete ('.$jobBlockSize.'): <div onclick="makeBox(\'taskDtl\', \'1040,'.$taskDat[$i].'\', 500, 500, 200, 50);">'.$i.' - '.$taskDat[$i].')Task Type '.$taskDtl[7].' is '.$taskDtl[6].'/'.$taskDtl[5].' Complete</div>';
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
