<?php

// Get date for this unit
$unitFile = fopen($gamePath./'unitDat.dat', 'rb');
fseek($unitFile, $unitID*400);
$groupDat = unpack('i*', fread($unitFile, 400));
fclose($unitFile);

// Give unit options and stats dependant upon unit type

?>