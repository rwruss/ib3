<?php
include("./slotFunctions.php");
// Get list of units available for foraging at this location
//$playerFile = fopen($gamePath.'/players.plr', 'rb');
echo 'seek to place '.$pGameID.'<br>';
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*400);
$playerDat = unpack('i*', fread($unitFile, 400));

print_r($playerDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
//readSlotData($file, $slot_num, $slot_size)
$unitList = array_filter(unpack("N*", readSlotData($slotFile, $playerDat[22], 40)));
fclose($slotFile);



echo '<script>groupList = [];</script>Forage from a city options...<p>
Select units to forage with:';

foreach ($unitList as $unitID) {
	fseek($unitFile, $unitID*400);
	$unitDat = unpack('i*', fread($unitFile, 400));
	echo '<div id="selOpt_'.$unitID.'" class="unselected" onclick="groupSelect('.$unitID.')">Unit '.$unitID.' - Type '.$unitDat[4].'</div>';
}
echo '
<div class="unselected" onclick="passClick(\'1025,\'+groupList, \'forageOptContent\')">Give Order</div>';
print_r($unitList);
fclose($unitFile);

?>