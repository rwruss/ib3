<?php

echo 'Specific building information for building '.$postVals[1].' <br>
This might include specific options for the selected building and type.';

// Get building information
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$bldgDat = unpack('i*', fread($unitFile, $defaultBlockSize));

include('../gameScripts/objects/bldg_'.$bldgDat[10].'.php');

?>
