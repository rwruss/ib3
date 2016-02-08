<?php

echo 'script 1035 - giver order '.$postVals[1].' to char #'.$_SESSION['selectedItem'];

// Load character information
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fclose($unitFile);

?>