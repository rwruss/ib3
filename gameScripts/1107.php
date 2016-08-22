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
	useDeskTop.newPane("military");
	thisDiv = useDeskTop.getPane("military");
	armyDiv = addDiv("", "stdContainer", thisDiv);
	listDiv = addDiv("", "stdContainer", thisDiv);';
$trgArmy = loadUnit($postVals[1], $unitFile, 400);

// Can add/remove units from this army
$armyUnits = new itemSlot($trgArmy->, $slotFile, 40);
for ($i=1; $i<=sizeof($armyUnits->slotData); $i++) {
	if ($armyUnits->slotData[$i] > 0) {
		$unitObj = loadUnit($armyUnits->slotData[$i], $unitFile, 400);
		
		if ($trgArmy->get('owner') == $pGameID || $trgArmy->get('controller') == $pGameID || $unitObj->get('controller') == $pGameID) {			
			echo 'var objContain = addDiv("", "selectContain", armyDiv);
			unitList.newUnit({unitType:"warband", unitID:'.$armyUnits->slotData[$i].', unitName:"unit Name", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitObj->get('uType').'});
			unitList.renderSum(armyItems['.$armyUnits->slotData[$i].'], objContain);
			selectButton(objContain, "hai", '.$gridList->slotData[$i].', [selectHead]);';
		} else {
			echo 'var objContain = addDiv("", "selectContain", armyDiv);
			unitList.newUnit({unitType:"warband", unitID:'.$armyUnits->slotData[$i].', unitName:"unit Name", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitObj->get('uType').'});
			unitList.renderSum(armyItems['.$armyUnits->slotData[$i].'], objContain);
			selectButton(objContain, "hai", '.$gridList->slotData[$i].', [selectHead]);';
		}
	}
} else {
	// Can only remove owned units from this army
}

// Verify that the player controls the units in question

fclose($unitFile);
fclose($slotFile);

?>