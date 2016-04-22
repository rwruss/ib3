<?php
include('./slotFunctions.php');
// Get list of all units for this faction
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

$playerObj = new player($playerDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerObj->unitSlot, 40)));

if (sizeof($unitList)>0) {
	echo '<script>';
	foreach ($unitList as $unitID) {
		
		fseek($unitFile, $unitID*$defaultBlockSize);
		$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
		if ($unitDat[4] == 3) {
			echo 'addDiv("armyList_'.$unitID.'", "stdContainer", "militaryContent");
			textBlob("desc", "armyList_'.$unitID.'", "Army Information")';
		} else {
			//echo '<div onclick="passClick(\'1034,'.$unitID.'\', \'rtPnl\');">Unit #'.$unitID.'</div>';
			$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
			if ($unitDat[15] == 0) $dtlTarget = 'militaryContent';
			else $dtlTarget = 'armyList_'.$unitDat[15];
			
			echo '
			newUnitDetail('.$unitID.', "'.$dtlTarget.'");
			//newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
			document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
			document.getElementById("Udtl_'.$unitID.'").addEventListener("click", function() {passClick("1034,'.$unitID.'", "rtPnl")});
			setUnitAction('.$unitID.', '.($actionPoints/1000).');
			setUnitExp('.$unitID.', 0.5);';
		}
	}
	
} else {
	echo 'You don\'t controll any units at this time';
}
echo "</script>Faction Military";
fclose($slotFile);
fclose($unitFile);

?>
