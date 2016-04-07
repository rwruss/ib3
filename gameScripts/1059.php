<?php

// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

//echo 'Unit Order ('.$postVals[1].') options for unit type '.$unitDat[4];

include ('../gameScripts/1059-'.$unitDat[4].'.php')

?>
