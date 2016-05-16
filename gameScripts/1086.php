<?php

// Attempt to run a plot

// Load plot data
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile, 200));
include('../gameScripts/tasks/runPlot_'.$plotDat[7].'.php');

fclose($taskFile);

?>