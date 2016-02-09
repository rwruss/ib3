<?php

// postvals 1=> task ID
echo 'Task Detail';

// Load task detail
$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
fseek($taskFile, $postVals[1]);
$taskDat = unpack('i*', fread($taskFile, 400));
fclose($taskFile);

include("../taskScripts/td_".$postVals[1].".php");

?>