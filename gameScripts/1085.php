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
console.log("place in " + selectHead);
//addDiv("", "selectContain", selectHead.left);
scrButton("1087,"+selectedItem, selectHead.right, "100%");';

for($i=1; $i<=sizeof($charList->slotData); $i++) {
	if ($charList->slotData[$i] > 0) {
		echo 'unitList.newUnit({unitID : '.$charList->slotData[$i].', unitType : "character", rating : 50, status : 1, unitName : "char '.$charList->slotData[$i].'", cost: 90});
		objButton = addDiv("", "selectContain", useDeskTop.getPane("ringLeader"));
		unitList.renderSum('.$charList->slotData[$i].', objButton);
		selectButton(objButton, "hai", '.$charList->slotData[$i].', [selectHead.left]);';
	}
}

echo '</script>';
fclose($slotFile);
fclose($unitFile);
fclose($taskFile);

?>
