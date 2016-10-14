<?php

// Get player info
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Get list of wars that players is in
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$warList = new itemSlot($playerDat[32], $slotFile, 40);

// Load the war details 
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$warDat = unpack('i*', fread($unitFile, $warBlockSize));

if (array_search($postVals[1], $warList->slotData)) {

	echo 'Details about war '.$postVals[1].'

	<script>
	textBlob("You can leave this war.  Note that this may result in victory in this war for the opposing side.");
	confirmButtons("Leave this War", "1058,'.$postVals[1].'", "warDtlContent", 3, "", "Leave");
	</script>';
} else {
	echo 'Details about war '.$postVals[1].'

	<script>
	textBlob("You can leave this war.  Note that this may result in victory in this war for the opposing side.");
	addDiv("joinOpts", "textBlob", document.getElementById("warDtlContent"));
	firstButton = optionButton("Join side 1", "", document.getElementById("joinOpts"));
	firstButton.addEventListener("click", function() {confirmBox("Do you really want to join this war on side 1?", "1058,'.$postVals[1].',1", 2, "Yes!", "Nah")});
	
	secondButton = optionButton("Join side 2", "", document.getElementById("joinOpts"));
	secondButton.addEventListener("click", function() {confirmBox("Do you really want to join this war on side 1?", "1058,'.$postVals[1].',2", 2, "Yes!", "Nah")});
	</script>';
}
//confirmBox = function (msg, prm, type, trg, aSrc, dSrc) {

fclose($slotFile);
fclose($unitFile);

?>