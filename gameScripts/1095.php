<?php

include('./slotFunctions.php');
include('./unitClass.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Gathering options for a resource point

// Get data about this resource point
$thisPoint = loadUnit($postVals[1], $unitFile, 400);

// Check for nearby units that can work at this locaiton
$thisPlayer = loadPlayer($pGameID, $unitFile, 400);
$unitList = new itemSlot($thisPlayer->get('unitSlot'), $slotFile, 40);

echo '<script>useDeskTop.newPane("somePane");
thisDiv = useDeskTop.getPane("somePane");';

for ($i=1; $i<=sizeof($unitList->slotData); $i++) {
	$checkUnit = loadUnit($unitList->slotData[$i], $unitFile, 400);

	if ($checkUnit->get('uType') == 8) {

		$xDist = $checkUnit->get('xLoc') - $thisPoint->get('xLoc');
		$yDist = $checkUnit->get('yLoc') - $thisPoint->get('yLoc');
		if ($xDist*$xDist + $yDist*$yDist <= 400) {
			$actionPoints = $checkUnit->actionPoints();
			// unit is close enough to gather -> show the unit as an option
			echo 'unitList.newUnit({unitType:"warband", unitID:'.$unitList->slotData[$i].', unitName:"unit name", actionPoints:'.$actionPoints.', strength:75, tNum:'.$checkUnit->get('uType').'});
			var orderBox = actionBox(thisDiv, "1122,'.$postVals[1].','.$unitList->slotData[$i].'", '.$actionPoints.');
			unitList.renderSum('.$unitList->slotData[$i].', orderBox.unitSpace);';
		}
	}
}
echo '</script>';
fclose($unitFile);
fclose($slotFile);

?>
