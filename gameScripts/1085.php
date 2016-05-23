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
// Load plot Data
fseek($taskFile, $postVals[1]*200);
$plotDat = unpack('i*', fread($taskFile, 100));
print_r($plotDat);

fseek($unitFile, $plotDat[13]*$defaultBlockSize);
$leaderDat = unpack('i*', fread($unitFile, 200));

echo '<script>
unitList.newUnit({unitID : '.$plotDat[14].', unitType : "character", rating : 50, status : 1, unitName : "char '.$plotDat[13].'", cost: 90});
useDeskTop.newPane("ringLeader");
selectedItem = false;
selectedID = 0;
var selectHead = selectionHead(useDeskTop.getPane("ringLeader"));
saveButton = addDiv("", "button", selectHead.center);
saveButton.innerHTML = "Save!";
saveButton.addEventListener("click", function() {scrMod("1087,'.$postVals[1].',"+selectedID)});
unitList.renderSum('.$plotDat[13].', selectHead.left);
';

for($i=1; $i<=sizeof($charList->slotData); $i++) {
	if ($charList->slotData[$i] > 0) {
		echo 'unitList.newUnit({unitID : '.$charList->slotData[$i].', unitType : "character", rating : 50, status : 1, unitName : "char '.$charList->slotData[$i].'", cost: 90});
		objButton = addDiv("", "selectContain", useDeskTop.getPane("ringLeader"));
		unitList.renderSum('.$charList->slotData[$i].', objButton);
		selectButton(objButton, "hai", '.$charList->slotData[$i].', [selectHead.right]);';
	}
}

echo '</script>';
fclose($slotFile);
fclose($unitFile);
fclose($taskFile);

?>
