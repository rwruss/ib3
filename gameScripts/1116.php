<?php

/*
Mercenary options screen from miltary menu
*/

include('./unitClass.php');
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$mercFile = fopen($gamePath.'/mercenaries.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

/*
fwrite($mercFile, pack('i', 4));
fseek($mercFile, 496);
fwrite($mercFile, pack('i', 0));
*/

// Load list of pending mercenary sales
fseek($mercFile, 0);
$mercHead = unpack('i*', fread($mercFile, 400));
$mercOffers = new itemSlot($mercHead[1], $mercFile, 100);

echo '<script>';
for ($i=1; $i<=sizeof($mercOffers->slotData); $i++) {
	if ($mercOffers->slotData[$i] > 0) {
		fseek($mercFile, $mercOffers->slotData[$i]*100);
		$thisOffer = unpack('i*', fread($mercFile, 100));

		// Load unit information
		$trgUnit = loadUnit($thisOffer[2], $unitFile, 400);
		echo 'var objContain = addDiv("", "selectContain", rtPnl);
				unitList.newUnit({unitType:"warband", unitID:'.$thisOffer[2].', unitName:"unit Name", actionPoints:0, strength:75, tNum:'.$trgUnit->get('uType').'});
				unitList.renderSum('.$thisOffer[2].', objContain);
				var newButton = addDiv("button", "button", objContain);
				newButton.addEventListener("click", function () {passClick("1117,'.$mercOffers->slotData[$i].'")});';
			}
}


// Show list of player controlled units that can be sold
$thisPlayer = loadPlayer($pGameID, $unitFile, 400);
$playerUnits = new itemSlot($thisPlayer->get('unitSlot'), $slotFile, 40);

for ($i=1; $i<=sizeof($playerUnits->slotData); $i++) {
	if ($playerUnits->slotData[$i] > 0) {
		$checkUnit = loadUnit($playerUnits->slotData[$i], $unitFile, 400);
		if ($checkUnit->mercApproved) {
			if ($checkUnit->get('armyID') == 0) {
				echo 'var objContain = addDiv("units to sell", "selectContain", rtPnl);
			unitList.newUnit({unitType:"warband", unitID:'.$playerUnits->slotData[$i].', unitName:"unit Name", actionPoints:0, strength:75, tNum:'.$checkUnit->get('uType').'});
			unitList.renderSum('.$playerUnits->slotData[$i].', objContain);
			var newButton = addDiv("", "button", objContain);
			newButton.innerHTML = "Pimp Unit";
			newButton.addEventListener("click", function() {scrMod("1118,'.$playerUnits->slotData[$i].'")});';
			}
		}
	}
}
echo '</script>';
fclose($mercFile);
fclose($unitFile);
fclose($slotFile);
?>
