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
$trgUnit = loadUnit($postVals[1], $unitFile, 400);
$mapSlot = floor($trgUnit->unitDat[2]/120)*120+floor($trgUnit->unitDat[1]/120);

$mapItems = new itemSlot($mapSlot, $mapSlotFile, 404); // start, file, slot size

$orderRadius = 5;
$oRadiusSq = $orderRadius*$orderRadius;

// out put list of units that can spy on the target
	echo 'textBlob("", thisDiv, "Select a unit to spy with");';
for ($i=1; $i<=sizeof($mapItems->slotData); $i++) {
	$checkUnit = loadUnit($mapItems->slotData[$i], $unitFile, 400);

	if ($checkUnit->get('uType') == 4) {
		$xDiff = $checkUnit->unitDat[1] - $trgUnit->unitDat[1];
		$yDiff = $checkUnit->unitDat[2] - $trgUnit->unitDat[2];
		if ($xDiff*$xDiff + $yDiff*$yDiff < $oRadiusSq) {
			// output option box for this unit
			echo 'unitList.newUnit({unitID : '.$mapItems->slotData[$i].', unitType : "character", rating : 50, status : 1, unitName : "char '.$mapItems->slotData[$i].'", cost: 90});
			objBox = actionBox(thisDiv, "1120,'.$mapItems->slotData[$i].','.$postVals[1].'", '.$checkUnit->actionPoints().');
			unitList.renderSum('.$mapItems->slotData[$i].', objBox.unitSpace);';
		}
	}
}
 echo '</script>';
fclose($mapSlotFile);
fclose($unitFile);
fclose($slotFile);

?>
