<?php
include('./slotFunctions.php');
include('./unitClass.php');
// Get list of all units for this faction
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
//fseek($unitFile, $pGameID*$defaultBlockSize);
//$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

$playerObj = new player($pGameID, $unitFile, 400);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("i*", readSlotData($slotFile, $playerObj->get("unitSlot"), 40)));
$unitDesc = explode('<->', file_get_contents($scnPath.'/units.desc'));

//print_r($unitList);
$armyItems = [];
if (sizeof($unitList)>0) {
	echo '<script>
	useDeskTop.newPane("military");
	thisDiv = useDeskTop.getPane("military");
	addDiv("armyList_0", "stdContainer", thisDiv);
	textBlob("desc", "armyList_0", "Unattached");
	';
	foreach ($unitList as $unitID) {

		fseek($unitFile, $unitID*$defaultBlockSize);
		$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
		//echo 'Type '.$unitDat[4].'<br>';

		if ($unitDat[4] == 3) {
			echo 'var thisArmy = addDiv("armyList_'.$unitID.'", "stdContainer", thisDiv);
			thisArmy.addEventListener("click", function() {
				scrMod("1027,'.$unitID.'");
			});
			textBlob("desc", "armyList_'.$unitID.'", "Army Information - '.$unitID.'");
			';
		} else {
			//echo '<div onclick="passClick(\'1034,'.$unitID.'\', \'rtPnl\');">Unit #'.$unitID.'</div>';
			$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
			array_push($armyItems ,$unitDat[15] ,$unitID);
			/*
			echo '
			newUnitDetail('.$unitID.', "militaryContent");
			//newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
			document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
			document.getElementById("Udtl_'.$unitID.'").addEventListener("click", function() {passClick("1034,'.$unitID.'", "rtPnl")});
			setUnitAction('.$unitID.', '.($actionPoints/1000).');
			setUnitExp('.$unitID.', 0.5);';*/
			$thisInfo = explode('<-->', $unitDesc[$unitDat[10]]);
			echo 'unitList.newUnit({unitType:"warband", unitID:'.$unitID.', unitName:"'.trim($thisInfo[0]).'", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitDat[4].'});';
		}
	}
	echo 'armyItems = ['.implode(',', $armyItems).'];
		for (var i=0; i<armyItems.length; i+=2) {
			unitList.renderSum(armyItems[i+1], "armyList_"+armyItems[i]);
			console.log("add to " + "armyList_"+armyItems[i+1])
		}';
} else {
	echo 'You don\'t controll any units at this time';
}
echo "</script>Faction Military";
fclose($slotFile);
fclose($unitFile);

?>
