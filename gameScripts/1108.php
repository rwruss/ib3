<?php

// Show a list of units that can be added or removed from an army

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// postvas - > 1 is army ID, 2+ is unit ID
$pvSize = sizeof($postVals);
print_r($postVals);

// verify that player controls the army in question
$trgArmy = loadUnit($postVals[1], $unitFile, 400);

// Load unit list for the army
$armyUnits = new itemSlot($trgArmy->get('unitListSlot'), $slotFile, 40);
echo 'Loaded slot '.$trgArmy->get('unitListSlot');

$inList = [];
$outList = [];
for ($i=2; $i<$pvSize; $i++) {
	echo 'Check for unit '.$postVals[$i];
	if (array_search($postVals[$i],$armyUnits->slotData)) {
		$outList[] = $postVals[$i];
	} else {
		$inList[] = $postVals[$i];
	}
}
echo 'In List:';
print_r($inList);
echo 'Out List';
print_r($outList);

// Review units being removed from the army - process all if player controls the army or only the ones that player controls.
for ($i=0; $i<sizeof($outList); $i++) {
	$outUnit = loadUnit($outList[$i], $unitFile, 400);
	if ($outUnit->get('controller') == $pGameID || $trgArmy->get('controller') == $pGameID) {
		echo 'Remove unit '.$outList[$i];
		// Process this unit out of the army
		$outUnit->save('armyID', 0);

		$armyUnits->deleteByValue($outList[$i], $slotFile);
	}
}

// Review units being added to the army - process all that the player controls if he also controls the army
if ($trgArmy->get('controller') == $pGameID) {
	for ($i=0; $i<sizeof($inList); $i++) {
		$inUnit = loadUnit($inList[$i], $unitFile, 400);
		if ($inUnit->get('controller') == $pGameID) {
			// Update unit to be in army
			$inUnit->save('armyID', $postVals[1]);

			// Add to army unit list
			$armyUnits->addItem($inList[$i], $slotFile);
		}
	}
} else {
	echo 'Can\'t add to an army you don\'t control!';
}

fclose($unitFile);
fclose($slotFile);

?>
