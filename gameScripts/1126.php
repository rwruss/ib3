<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');
// Get list of all units for this faction
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
//fseek($unitFile, $pGameID*$defaultBlockSize);
//$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

$playerObj = loadPlayer($pGameID, $unitFile, 400);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("i*", readSlotData($slotFile, $playerObj->get("unitSlot"), 40)));
$unitDesc = explode('<->', file_get_contents($scnPath.'/units.desc'));

//print_r($unitList);
$armyItems = [];


echo '<script>
useDeskTop.newPane("c1126");
thisDiv = useDeskTop.getPane("c1126");

var thisGroup = groupSort(thisDiv, 0, 1127, 1);';
if (sizeof($unitList)>0) {
	foreach ($unitList as $unitID) {

		fseek($unitFile, $unitID*$defaultBlockSize);
		//$showUnit = new warband($unitID, $unitFile, 400);
		$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

			$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
			array_push($armyItems ,$unitDat[15] ,$unitID);

			$thisInfo = explode('<-->', $unitDesc[$unitDat[10]]);
			echo '
      var objContain = addDiv("", "selectContain", thisGroup.left);
      unitList.newUnit({unitType:"warband", unitID:'.$unitID.', unitName:"'.trim($thisInfo[0]).'", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitDat[4].'});
      unitList.renderSum('.$unitID.', objContain);
      groupButton(objContain, '.$unitID.');';
		}
} else {
	echo 'You don\'t controll any units at this time';
}
echo '</script>';
fclose($slotFile);
fclose($unitFile);



?>
