<?php

include('./slotFunctions.php');
// postvals 1=> task ID
//echo 'Task Detail for task '.$postVals[1].'<br><div id="taskArea"></div><div id="unitArea"></div>';

// Load task detail
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
fseek($taskFile, $postVals[1]*$jobBlockSize);
$taskDat = unpack('i*', fread($taskFile, $jobBlockSize));
fclose($taskFile);

$required = max(1000, $taskDat[5]);

echo '
<div class="taskHeader" id="task_'.$postVals[1].'_header"></div>
<div class="centeredmenu" id="task_'.$postVals[1].'_tabs"><ul id="task_'.$postVals[1].'_tabs_ul"></ul></div>
<div class="taskOptions" id="task_'.$postVals[1].'_options"></div>';

//print_r($taskDat);

include('../gameScripts/tasks/td_'.$taskDat[7].'.php');

?>
