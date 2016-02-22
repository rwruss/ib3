<?php

// postvals 1=> task ID
echo 'Task Detail for task '.$postVals[1].'<br>';

// Load task detail
$taskFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($taskFile, $postVals[1]*$defaultBlockSize);
$taskDat = unpack('i*', fread($taskFile, $jobBlockSize));
fclose($taskFile);

print_r($taskDat);

include("../gameScripts/tasks/td_".$taskDat[5].".php");

?>
