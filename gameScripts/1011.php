<?php
include("./slotFunctions.php");
// Get list of all units for this faction
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*400);
$playerDat = unpack('i*', fread($unitFile, 400));

$playerObj = new player($playerDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerObj->unitSlot, 40)));

if (sizeof($unitList)>0) {
	foreach ($unitList as $unitID) {
		fseek($unitFile, $unitID*400);
		$unitDat = unpack('i*', fread($unitFile, 400));
		echo '<div onclick="makeBox(\'unitDetail\', \'1034,'.$unitID.'\', 500, 500, 200, 50);">Unit #'.$unitID.'</div>>';
	}
} else {
	echo 'You don\'t controll any units at this time';
}
echo "Faction Military";

?>