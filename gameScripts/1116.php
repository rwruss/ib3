<?php

/*
Mercenary options screen from miltary menu
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$mercFile = fopen($gamePath.'/mercenaries.dat', 'rb');


// Load list of pending mercenary sales
$mercHead = unpack('i*', fread($mercFile, 400));
$mercOffers = new itemSlot($mercHead[1], $mercFile, 100);

for ($i=1; $i<=sizeof($mercOffers->slotData); $i++) {
	fseek($mercFile, $mercOffers->slotData[$i]*100);
	$thisOffer = unpack('i*', fread($mercFile, 100));
	
	// Load unit information
	$trgUnit = loadUnit($thisOffer[2], $unitFile, 400);
	echo 'var objContain = addDiv("", "selectContain", thisGroup.left);
			unitList.newUnit({unitType:"warband", unitID:'.$armyUnits->slotData[$i].', unitName:"unit Name", actionPoints:0, strength:75, tNum:'.$unitObj->get('uType').'});
			unitList.renderSum('.$armyUnits->slotData[$i].', objContain);
			var newButton = addDiv("button", "button", objContain);
			newButton.addEventListener("click", function () {passClick("1117,'.$mercOffers->slotData[$i].'")});';
}

// Show list of player controlled units that can be sold
$thisPlayer = loadPlayer($pGameID, $unitFile, 400);
$playerUnits = new itemSlot($thisPlayer->get('unitSlot'), $slotFile, 40);

for ($i=1; $i<=sizeof($playerUnits->slotData); $i++) {
	if ($$playerUnits->slotData[$i] > 0) {
		$checkUnit = loadUnit($playerUnits->slotData[$i], $unitFile, 400);
		if ($checkUnit->mercApproved) {
			if ($checkUnit->get('armyID') == 0) {
				echo 'var objContain = addDiv("", "selectContain", thisGroup.left);
			unitList.newUnit({unitType:"warband", unitID:'.$armyUnits->slotData[$i].', unitName:"unit Name", actionPoints:0, strength:75, tNum:'.$unitObj->get('uType').'});
			unitList.renderSum('.$armyUnits->slotData[$i].', objContain);
			var newButton = addDiv("Pimp Unit", "button", objContain);
			newButton.addEventListener("click", function () {passClick("1118,'.$mercOffers->slotData[$i].'")});';
			}
		}
	}
}

fclose($mercFile);
fclose($unitFile);

?>