<?php

// open task file
$taskFile = fopen($gamePath.'/unitDat.dat', 'rb');

fseek($taskFile, $taskID*$defaultBlockSize);
$taskDat = unpack('i*', fread($taskFile, $jobBlockSize));

fclose($taskFile);

?>
