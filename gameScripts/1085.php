<?php

// Add a ringleader to a plot

include("./slotFunctions.php");

$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load player data to get char slot
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Load all characters available
echo 'Check slot '.$playerDat[19];
$charList = new itemSlot($playerDat[19], $slotFile, 40);
print_r($charList->slotData);
// Load plot Data
fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile, 100));

echo '<script>
useDeskTop.newPane("ringLeader");
selectedItem = false;
var selectHead = selectionHead(useDeskTop.getPane("ringLeader"));
scrButton("1087,"+selectedItem, buttonBox, "100%");';
for($i=1; $i<=sizeof($charList->slotData); $i++) {
	echo 'unitList.newUnit({unitID : '.$charList->slotData[$i].', unitType : "character", rating : 50, status : 1, unitName : "char '.$charList->slotData[$i].'", cost: 90});
	objButton = addDiv("", "selectContain", useDeskTop.getPane("ringLeader"));
	itemList.renderSum('.$charList->slotData[$i].', objButton);
	selectButton(objButton, "hai", 1, [selectHead.left]);';
	/*
	echo 'unitList.newUnit({unitID : '.$charList->slotData[$i].', unitType : "character", rating : 50, status : 1, unitName : "char '.$charList->slotData[$i].'", cost: 90});
	scrSelectBox('.$charList->slotData[$i].', "1086.'.$charList->slotData[$i].','.$postVals[1].'", "Select This Char");';
	*/
}
echo '</script>';

fclose($slotFile);
fclose($unitFile);
fclose($taskFile);

?>
