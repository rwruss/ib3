<?php
include("./slotFunctions.php");
// Get list of all units for this faction
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

$playerObj = new player($playerDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerObj->unitSlot, 40)));

if (sizeof($unitList)>0) {
	foreach ($unitList as $unitID) {
		fseek($unitFile, $unitID*$defaultBlockSize);
		$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));
		echo '<div onclick="passClick(\'1034,'.$unitID.'\', \'rtPnl\');">Unit #'.$unitID.'</div>';
	}
} else {
	echo 'You don\'t controll any units at this time';
}
echo "Faction Military";

?>
