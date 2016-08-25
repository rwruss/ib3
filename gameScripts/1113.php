<?php

/*
This script shows options for adding a unit to an army or combinging it with other free units to form an army
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');

// Load unit infrmation
$thisUnit = loadUnit($postVals[1], $unitFile, 400);

// Load other units that are in this location and can be paired
$mapSlot = floor($thisUnit->get('yLoc')/120)*120+floor($thisUnit->get('xLoc')/120);
$gridList = new itemSlot($mapSlot, $mapSlotFile, 404);

echo '<script>
	var indieList = [];';
for ($i=1; $i<=sizeof($gridList->slotData); $i+=2) {
	$mapUnit = loadUnit($gridList->slotData[$i]);
	if ($thisUnit->get('xVal') == $mapUnit->get('xVal') && $thisUnit->get('yVal') == $mapUnit->get('yVal')) {
		// units are at the same spot - can they be put together?
		if ($mapUnit->get('uType') == 2) {
			// Load army units for display
			$armyUnitList = new itemSlot($mapUnit->get('unitListSlot'), $slotFile, 40);
			echo 'armyBox = addDiv("", "stdContain", rtPnl);'
			for ($j=1; $j<=sizeof($armyUnitList->slotData); $j++) {
				$armyUnit = loadUnit($armyUnitList->slotData[$j], $untiFile, 400);
				echo 'unitList.newUnit({unitType:"warband", unitID:'.$armyUnitList->slotData[$j].', unitName:"some unit", actionPoints:100, strength:75, tNum:'.$armyUnit->get('uType').'});
				unitList.renderSum('.$armyUnitList->slotData[$j].', armyBox);';
			}
		} else {
			echo 'unitList.newUnit({unitType:"warband", unitID:'.$armyUnitList->slotData[$j].', unitName:"some unit", actionPoints:100, strength:75, tNum:'.$armyUnit->get('uType').'});'
		}
	}
}
echo 'indieBox = addDiv("", "stdContain", rtPnl);
	for (var i=0; i<indieList.length; i++) {
		unitList.renderSum(indieList[i], indieBox);
	}
	</script>';
fclose($mapSlotFile);
fclose($unitFile);
fclose($slotFile);

?>