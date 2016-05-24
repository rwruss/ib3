<?php
//print_r($postVals);
$unitID = $postVals[2]*255+$postVals[3];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $unitID*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$_SESSION['selectedItem'] = $unitID;
include('../gameScripts/objects/obj_'.$unitDat[4].'.php');
fclose($unitFile);
?>
