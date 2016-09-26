<?php

include('./slotFunctions.php');
include("./unitClass.php");

$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');

echo '<script>
useDeskTop.newPane("spyMenu");
thisDiv = useDeskTop.getPane("spyMenu");';

// Look for player units around the target
$spyUnit = loadUnit($postVals[1], $unitFile, 400);
$mapSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);

$mapItems = new itemSlot($mapSlot, $mapSlotFile, 404); // start, file, slot size

$orderRadius = 5;
$oRadiusSq = $orderRadius*$orderRadius;

// out put list of units that can spy on the target
for ($i=1; $i<=sizeof($mapItems->slotData); $i++) {
	$checkUnit = loadUnit($mapItems->slotData);
	
	$xDiff = $checkUnit->unitDat[1] - $spyUnit->unitDat[1];
	$yDiff = $checkUnit->unitDat[2] - $spyUnit->unitDat[2];
	if ($xDiff*$xDiff + $yDiff*$yDiff < $oRadiusSq) {
		// output option box for this unit
	}
}
 
fclose($mapSlotFile);
fclose($unitFile);
fclose($slotFile);

?>
