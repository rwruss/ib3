<?php

//$sentVal = explode('.', $postVals[1]);
$unitType = $taskNum[1];
$unitDesc = explode('<->', file_get_contents($gamePath.'/units.desc'));
$typeDesc = explode('<-->', $unitDesc[$unitType]);

// Verify if prerequsites are met

echo 'Train a character

Show options for importing an existing character or training a new one....
<script>

useDeskTop.newPane("makeChars");
thisDiv = useDeskTop.getPane("makeChars");

var newCharTabs = makeTabMenu("newChars", thisDiv);
var newCharTabs_1 = newTab("newChars", 1, "New Chars");
var newCharTabs_2 = newTab("newChars", 2, "My Chars");';

for ($i=1; $i<=10; $i++) {
	echo 'unitList.newUnit({unitID:"d'.$i.'", unitType:character, actionPoints:1000, status:9, exp:0, str:1, subType:0});
		var objContain = addDiv("", "selectContain", newCharTabs_1);
		unitList.renderSum("d'.$i.'", objContain);
		var newButton = optionButton("", objContain, "Train Char");
		newButton.objectID = d'.$i.';
		newButton.addEventListener("click", function () {scrMod("1090,"+this.objectID)});';
}

// Load list of available permanant characters for the player to use

echo '
//confirmButtons("Confirm that you would like to train '.$typeDesc[0].'", "1089,'.$taskNum[0].','.$unitType.'", "taskDtlContent", 2, "Train");
</script>
';

// Create a new window for the character training

// Show a tab for generic units that can be trained

// Show a tab for units that can be imported

?>
