<?php

// Get date for this unit
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));


// Give unit options and stats dependant upon unit type
include('../gameScripts/units/wbsc_'.$unitDat[4].'.php');
fclose($unitFile);
?>
