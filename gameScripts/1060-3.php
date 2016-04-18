<?php
include("./slotFunctions.php");
echo 'Do work in a city';

echo 'Link to job type '.$typeInfo[1].'<br>';

// Check if unit is in a city
$mapSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');

$mapItems = new itemSlot($mapSlot, $mapSlotFile, 404); // start, file, slot size
$checkItems = array_filter($mapItems->slotData);

print_r($checkItems);
//for ($i=1; $i<=sizeof($checkItems); $i++) {
echo '<script>';
foreach ($checkItems as $checkID) {
	fseek($unitFile, $checkID*$defaultBlockSize);
	$checkDat = unpack('i*', fread($unitFile, 200));

	if ($checkDat[1] == $unitDat[1] && $checkDat[2] == $unitDat[2]) {
		if ($checkDat[4] == 1)	{
			//echo 'In a city ('.$checkID.')';

			$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
			// Look up city tasks
			//echo '<p>Task slit is '.$checkDat[21].'<br>';
			$cityTasks = new itemSlot($checkDat[21], $slotFile, 40);
			//echo 'Tasks found:';
			//print_r($cityTasks->slotData);
			fclose($slotFile);

      $checkTasks = array_filter($cityTasks->slotData);
      $taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
      $count = 0;
      foreach($checkTasks as $taskID) {
        fseek($taskFile, $taskID*$jobBlockSize);
  			$taskDtl = unpack('i*', fread($taskFile, $jobBlockSize));
        if ($taskDtl[7] == $typeInfo[1]) {
          echo 'newTaskDetail("task", "taskDtlContent", '.($taskDtl[6]/$taskDtl[5]).', 1);

          addDiv("jobOptions_'.$count.'", "cButtons", document.getElementById("taskDtlContent"));

          var opt1 = optionButton("", "jobOptions_'.$count.'", "1");
          opt1.addEventListener("click", function() {scrMod("1061,'.$typeInfo[1].','.$taskID.',1")});

          var opt2 = optionButton("", "jobOptions_'.$count.'", "2");
          opt2.addEventListener("click", function() {scrMod("1061,'.$typeInfo[1].','.$taskID.',2")});

          var opt3 = optionButton("", "jobOptions_'.$count.'", "3");
          opt3.addEventListener("click", function() {scrMod("1061,'.$typeInfo[1].','.$taskID.',3")});

          var opt4 = optionButton("", "jobOptions_'.$count.'", "4");
          opt4.addEventListener("click", function() {scrMod("1061,'.$typeInfo[1].','.$taskID.',4")});';
          $count++;
        } else echo 'Task type '.$taskDtl[7].'<br>';

      }
			echo '</script>';
			$privateTasks = new itemSlot($checkDat[22], $slotFile, 40);
			echo 'Priave items:';
			print_r($privateTasks->slotData);
      fclose($taskFile);
    }
	}
}


//print_r($jobDesc);

?>
