<?php

include('../slotFunctions.php');
$uSlotFile = fopen();
$pDatFile = fopen('../users/userDat.dat', 'rb');
$pCharFile = fopen();
$gameSlotFile = fopen();

// Load list of available permanant characters

fseek($pDatFile, $_SESSION['playerId']*500);
$playerDat = fread($pDatFile, 500);
$charList = new itemSlot(start Slot, $uSlotFile, 40);

// Read list and times of chars that have been previously imported
$usedList = new itemSlot($playerDat[35], $gameSlotFile, 40);

echo '<script>

useDeskTop.newPane("addChars");
thisDiv = useDeskTop.getPane("addChars");
var newCharTabs = makeTabMenu("newChars", thisDiv);
var newCharTabs_1 = newTab("newChars", 1, "My Chars");
var newCharTabs_2 = newTab("newChars", 2, "New");
';
for ($i=1; $i<=sizeof($charList->slotData); $i++) {
	$currentList[$charList->slotData[$i]] = 0;
}
for ($i=1; $i<=sizeof($usedList->slotData); $i+=2) {
	$currentList[$usedList->slotData[$i]] = $usedList->slotData[$i+1];
}

for ($i=1; $i<=sizeof($charList->slotData); $i++) {
	// Load character information
	fseek($pCharFile, $charList->slotData[$i]*200);
	$pCharDat = unpack('i*', fread($pCharFile, 200));
	
	
	
	// Output char detail screen
	// Output button to add this char (if allowed)
	echo 'unitList.newUnit({unitID:"p'.$charList->slotData[$i].'", unitType:character, actionPoints:1000, status:9, exp:0, str:1, subType:0});
		var objContain = addDiv("", "selectContain", newCharTabs_1);
		unitList.renderSum("p'.$charList->slotData[$i].'", objContain);
		var newButton = optionButton("", objContain, "Import Char");
		newButton.objectID = '.$charList->slotData[$i].';
		newButton.addEventListener("click", function () {scrMod("1090,"+this.objectID)});
';
}

echo 'var objContain = addDiv("", "selectContain", newCharTabs_2);
	objContain.innerHTML = "SOmething new";
	objContain.addEventListener("click", function () {scrMod("1091,1")});
	</script>';

fclose($gameSlotFile);
fclose($pDatFile);
fclose($pDatFile);
fclose($uSlotFile);

/*
objButton = addDiv("", "selectContain", useDeskTop.getPane("ringLeader"));
unitList.renderSum('.$charList->slotData[$i].', objButton);
selectButton(objButton, "hai", '.$charList->slotData[$i].', [selectHead.right]);
*/

?>