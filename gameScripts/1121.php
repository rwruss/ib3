<?php

include('./slotFunctions.php');
include("./unitClass.php");

$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

echo '<script>
useDeskTop.newPane("spyMenu");
thisDiv = useDeskTop.getPane("spyMenu");';

// Look for units around the target
$spyUnit = loadUnit($postVals[1], $unitFile, 400);

fclose($unitFile);
fclose($slotFile);

?>
