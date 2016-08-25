<?php
/*
Generate a menu for players to transfer units that they own to control of another player
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load player Dat
$thisPlayer = loadPlayer($pGameId, $unitFile, 400);

// Load list of units that the player controls
$unitList = new itemSlot($thisPlayer->get('unitSlot'), $slotFile, 40);

echo '<script>
	useDeskTop.newPane("selUnits");
	thisDiv = useDeskTop.getPane("selUnits");

  var thisGroup = groupSort(thisDiv, '.$postVals[1].', 1111);';

for ($i=1; $i<=sizeof($unitList->slotData); $i++) {
	$trgUnit = loadUnit($unitList->slotData[$i], $unitFile, 400);
	if ($trgUnit->get('controller') == $pGameId) {
		echo 'var objContain = addDiv("", "selectContain", thisGroup.left);
			unitList.newUnit({unitType:"warband", unitID:'.$armyUnits->slotData[$i].', unitName:"unit Name", actionPoints:0, strength:75, tNum:'.$unitObj->get('uType').'});
			unitList.renderSum('.$armyUnits->slotData[$i].', objContain);
			groupButton(objContain, '.$armyUnits->slotData[$i].');';
	}
}

echo '</script>';

fclose($slotFile);
fclose($unitFile);

?>