<?php

// postvals 1=> task ID
echo 'Task Detail for task '.$postVals[1].'<br>';

// Load task detail
$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
fseek($taskFile, $postVals[1]*200);
$taskDat = unpack('i*', fread($taskFile, 200));
fclose($taskFile);

print_r($taskDat);

include("../gameScripts/tasks/td_".$taskDat[5].".php");

?>