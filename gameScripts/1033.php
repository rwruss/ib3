<?php
include("./slotFunctions.php");
// Verify that person is authorized to view this project
//// // Load city Dat

$cityID = $_SESSION['selectedItem'];
//$charID = $_SESSION['selectedChar'];
$charID = $postVals[1];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*400);
$cityDat = unpack('i*', fread($unitFile, 400));

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
	// get character data
	fseek($unitFile, $charID*400);
	$charDat = unpack('i*', fread($unitFile, 400));
	
	$positionList = unpack("i*", readSlotData($slotFile, $charDat[13], 40));
	$posIndex = array_search($cityID*(-1), $positionList);
	
	$oldRank = $positionList[$posIndex+1];
	
	// update the character's rank and record
	$positionList[$posIndex+1] = $postVals[1];
	$useDat = '';
	$positionSize = sizeof($positionList);
	for ($i=1; $i<=$positionSize; $i++) {
		$useDat .= pack('i', $positionList[$i]);
	}
	
	writeBlocktoSlot($gamePath.'/gameSlots.slt', $charDat[13], $useDat, $slotFile, 40) // function writeBlocktoSlot($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)
	
	// Update leadership positions
	$oldRankIndex = array_search($charID, $rankList[$oldRank]);
	unset($rankList[$oldRank][$oldRankIndex]);
	
	// Repack leadership data and save
	$useDat = '';
	for ($i=0; $i<9; $i++) {
		$useDat .= pack('i', -1*$i);
		foreach ($rankList[$i] as $rankCharID) {
			$useDat .= pack('i', $rankCharID);
		}
	}
	writeBlocktoSlot($gamePath.'/gameSlots.slt', $cityDat[13], $useDat, $slotFile, 40) // function writeBlocktoSlot($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)
	
	// check for changes to the player's credintial level - get list of characters that player controls and compare to the rank list moving up from the new 
	// rank for the current player.
	fseek($unitFile, $pGameID*400);
	$playerDat = unpack('i*', fread($unitFile, 400));
	
	$playerCharList = array_filter((unpack("i*", readSlotData($slotFile, $playerDat[19], 40)));
	$newHighRank = $postVals[1];
	$checkRank = 9;
	while ($checkRank > $newHighRank) {
		foreach ($rankList[$checkRank] as $charRankID) {
			if (array_search($charRankID, $playerCharList) {
				$newHighRank = $checkRank;
				break 2;
			}
		}
		$checkRank--;
	}
	
	if ($newHighRank < $credLevel) {
		// update the leadership slot for the city
		writeSlotPoint($slotFile, $cityDat[19], $approved-1, pack('i', $newHighRank), 40); //writeSlotPoint($slotFile, $startSlot, $targetPoint, $data, $slotSize)
	}	
	
	
	echo '<Script>alert("change saved - unit '.$charID.' from rank '.$oldRank.' to rank '.$postVals[2].'");
	alert(document.getElementById("rankBox_'.$charID.'").innerHTML);
	document.getElementById("rankBox_'.$charID.'").innerHTML = "'.$postVals[2].'"</script>';
} else {
	echo '<script>alert("You are not allowed to make that change to this character\'s poistion ('.$credLevel.')")</script>';
}





fclose($unitFile);
fclose($slotFile);
?>