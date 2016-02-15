<?php
include("./slotFunctions.php");
echo 'Warband type 8<br>
Unit info detail - this is a civilian unit<br>';

// Get information about the taske that is currently being worked on
if ($unitDat[11] > 0) {
	// Load task
	$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
	fseek($taskFile, $unitDat[11]*200);
	$taskDat = unpack('i*', fread($taskFile, 200));

	include('../gameScripts/units/tp_'.$taskDat[5].'.php');
} else {
	echo 'This unit is not currently working on anything<br>';
}

// Read the data for the current city/army the unit is in
fseek($unitFile, $unitDat[12]*400);
$homeDat = unpack('i*', fread($unitFile, 400));

if ($homeDat[4] == 1) {
	// unit is based in a city
	echo 'This unit is in a city right now<br>';
	// Get list of tasks available at the home city or the home army
	if ($homeDat[21] != 0) {
		$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
		$taskList = array_filter(unpack("i*", readSlotData($slotFile, $homeDat[21], 40)));
		echo 'Tasks is slot '.$homeDat[21].' - '.sizeof($taskList).'<br>';
		$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
		foreach ($taskList as $taskID) {

		  fseek($taskFile, $taskID*200);
		  $taskDat = unpack('i*', fread($taskFile, 200));
			$unitAssign = $postVals[1];
			include('../gameScripts/tasks/td_'.$taskDat[5].'.php');
			echo '<div>Task '.$taskID.'<br>
				Description of task<br>

				<div onclick="scrMod(\'1043,'.$postVals[1].','.$taskID.'\')">work on task</div></div>';
		}
		fclose($taskFile);
		fclose($slotFile);
	} else {
		echo 'There are no tasks available at this location';
	}
} else {
	// unit is part of an army
}

?>
