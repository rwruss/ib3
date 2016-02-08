<?php

// open task file
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');

fseek($taskFile, $taskID*200);
$taskDat = unpack('i*', fread($taskFile, 200));

fclose($taskFile);

?>