<?php

include('./slotFunctions.php');
// postvals 1=> task ID
echo 'Task Detail for task '.$postVals[1].'<br>
<div id="taskArea"></div><div id="unitArea"></div>';

// Load task detail
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
fseek($taskFile, $postVals[1]*$jobBlockSize);
$taskDat = unpack('i*', fread($taskFile, $jobBlockSize));
fclose($taskFile);

$required = max(1000, $taskDat[5]);
echo '<Script>
document.getElementById("taskDtlContent").style.diplay = "inline"
newTaskDetail('.$postVals[1].', "taskArea", '.$taskDat[6]/$required.')</script>';

//print_r($taskDat);

include('../gameScripts/tasks/td_'.$taskDat[7].'.php');

?>
