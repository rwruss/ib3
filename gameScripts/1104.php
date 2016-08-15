<?php

include("./slotFunctions.php");
include('./unitClass.php');

echo 'Gather from point '.$postVals[1].' with unit '.$postVals[2];

$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$rscPoint = unpack('i*', fread($unitFile, 400));
include('../gameScripts/1061-1.php');
?>
