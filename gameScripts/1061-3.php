<?php


// Load Task Data
$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
fseek($taskFile, $postVals[2]*200);
$taskDat = unpack('i*', fread($taskFile, 100));




echo 'Job type '.$jobType[0].', task type '.$jobType[1].', Task ID: '.$postVals[2].', unit '.$_SESSION['selectedUnit'].'<p>';

print_r($postVals);

include('../gameScripts/tasks/ta_'.$jobType[1].'.php');

?>
