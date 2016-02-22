<?php
include("./slotFunctions.php");
// Verify that person is authorized to view this project
//// // Load city Dat

$cityID = $_SESSION['selectedItem'];
$charID = $_SESSION['selectedChar'];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Verify that player has credentials to view this info
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);

if ($approved != false) {
	$credLevel = $credList[$approved-1]*(-1);
} else {
	$credLevel = 0;
}

//// // Get list of characters for this town
$townLeaders = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[13], 40)));
$numLeaders = sizeof($townLeaders);
$rankList[0] = array();
$rankList[1] = array();
$rankList[2] = array();
$rankList[3] = array();
$rankList[4] = array();
$rankList[5] = array();
$rankList[6] = array();
$rankList[7] = array();
$rankList[8] = array();
$rankList[9] = array();
for ($i=0; $i<$numLeaders; $i++) {
	if ($townLeaders[$i] < 0) $trgRank = -$townLeaders[$i];
	else $rankList[$trgRank][] = $townLeaders[$i];
}

if ($approved) {

} else {
}

// update the character's rank


// up the leadership slot for the city



fclose($unitFile);
fclose($slotFile);
?>
