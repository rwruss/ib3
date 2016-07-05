<?php
include('unitClass.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

fseek($unitFile, $postVals[1]*$defaultBlockSize);
$battleDat = unpack('i*', fread($unitFile, 200));

echo '<script>
useDeskTop.newPane("battleDtl");
thisDiv = useDeskTop.getPane("battleDtl");
textBlob("desc", thisDiv, "Battle Details for battle '.$postVals[1].'");';

// Show player units that are in the battle
$playerObj = new player($pGameID, $unitFile, 400);

$unitList = array_filter(unpack("i*", readSlotData($slotFile, $playerObj->get('unitSlot'), 40)));

for ($i=1; $i<=sizeof($unitList->slotData; $i++) {
	if ($unitList->slotData[$i] > 0) {
		fseek($unitFile, $unitList->slotData[$i]*$defaultBlockSize);
		$unitDat = unpack('i*', fread($unitFile, 200));
	}
}

// Show the wars that are in play in this battle

echo '</script>';
fclose($unitFile);
fclose($slotFile);
?>
