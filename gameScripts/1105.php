<?php

// Menu for depositing resources held by a unit or army into a town.

include('./unitClass.php');
include('./cityClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

//Load the unit
$selectedUnit = loadUnit($postVals[1], $unitFile, 400);

echo '<script>var selectHead = selectionHead("rtPnl");';

// Look for nearby settlements that you can drop resources in
$mapSlot = floor($selectedUnit->get('yLoc')/120)*120+floor($selectedUnit->get('xLoc')/120);
//echo 'Mapslot is '.$mapSlot.' from '.$selectedUnit->get('xLoc').', '.$selectedUnit->get('yLoc');
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');
$gridList = new itemSlot($mapSlot, $mapSlotFile, 404);
//print_r($gridList->slotData);
for ($i=1; $i<=sizeof($gridList->slotData); $i+=2) {
	if ($gridList->slotData[$i] > 0) {
		//echo 'Load unit ('.$gridList->slotData[$i].')';
		$checkObj = loadUnit($gridList->slotData[$i], $unitFile, 400);
		// Check to see if unit is a city
		if ($checkObj->get('uType') == 1) {
			// Check to get city permissions
			$credList = array_filter(unpack("i*", readSlotData($slotFile, $checkObj->unitDat[19], 40)));
			$approved = checkCred($pGameID, $credList);

			if ($approved) {
				// display the place as an option to drop the stuff
				echo 'unitList.newUnit({unitType:"town", unitID:'.$gridList->slotData[$i].', unitName:"Town '.$gridList->slotData[$i].'", actionPoints:"0", strength:75, tNum:"0"});
				var objContain = addDiv("", "selectContain", rtPnl);
				unitList.renderSum('.$gridList->slotData[$i].', objContain);
				selectButton(objContain, "hai", '.$gridList->slotData[$i].', [selectHead.right]);';
			}
		}
	}
}
fclose($mapSlotFile);

// Load what the unit/army is carrying and display slider bars for deposit amounts
echo 'groupList = [];'
$carryDat = new blockSlot($selectedUnit->get('carrySlot'), $slotFile, 40);
for ($i=1; $i<sizeof($carryDat->slotData); $i+=2) {
	if ($carryDat->slotData[$i+1] > 0) {
		echo 'var newSlide = slideValBar(trg, '.$carryDat->slotData[$i].', 0, '.$carryDat->slotData[$i+1].');
		newSlide.title.innerHTML = "Resource #'.$carryDat->slotData[$i].'";
		groupList.push(newSlide.slide);';
		//echo 'R#'.$carryDat->slotData[$i].' : '.$carryDat->slotData[$i];
	}
}
echo '</script>';



fclose($unitFile);
fclose($slotFile);

?>
