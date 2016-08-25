<?php

// Show a list of units that can be added or removed from an army

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

// postvas - > 1 is army ID

// verify that player controls the army in question
// Commander of army can drop and add units
// Owner of units can pull the units from the army
echo '<script>
	useDeskTop.newPane("selUnits");
	thisDiv = useDeskTop.getPane("selUnits");

  var thisGroup = groupSort(thisDiv, '.$postVals[1].', 1108);';
$trgArmy = loadUnit($postVals[1], $unitFile, 400);

// Can add/remove units from this army
$armyUnits = new itemSlot($trgArmy->get('unitListSlot'), $slotFile, 40);
for ($i=1; $i<=sizeof($armyUnits->slotData); $i++) {
	if ($armyUnits->slotData[$i] > 0) {
		$unitObj = loadUnit($armyUnits->slotData[$i], $unitFile, 400);

		if ($trgArmy->get('owner') == $pGameID || $trgArmy->get('controller') == $pGameID || $unitObj->get('controller') == $pGameID) {
			echo 'var objContain = addDiv("", "selectContain", thisGroup.left);
			unitList.newUnit({unitType:"warband", unitID:'.$armyUnits->slotData[$i].', unitName:"unit Name", actionPoints:0, strength:75, tNum:'.$unitObj->get('uType').'});
			unitList.renderSum('.$armyUnits->slotData[$i].', objContain);
      groupButton(objContain, '.$armyUnits->slotData[$i].');';
		} else {
			echo 'var objContain = addDiv("", "selectContain", armyDiv);
			unitList.newUnit({unitType:"warband", unitID:'.$armyUnits->slotData[$i].', unitName:"unit Name", actionPoints:0, strength:75, tNum:'.$unitObj->get('uType').'});
			unitList.renderSum('.$armyUnits->slotData[$i].', thisGroup.left);
      groupButton(objContain, '.$armyUnits->slotData[$i].');';
		}
	}
}

// Verify that the player controls the units in question

fclose($unitFile);
fclose($slotFile);

?>
