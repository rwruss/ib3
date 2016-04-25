<?php

echo 'Options for joining a battle';
include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

// Load battle information
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$battleDat = unpack('i*', fread($unitFile, 100));

// Get information about starting units for the battle
fseek($unitFile, $battleDat[11]*$defaultBlockSize);
$baseUnit_1 = unpack('i*', fread($unitFile, 400));

fseek($unitFile, $battleDat[12]*$defaultBlockSize);
$baseUnit_2 = unpack('i*', fread($unitFile, 400));

// Get player information to see what wars are involved
fseek($unitFile, $baseUnit_1[6]*$defaultBlockSize)
$playerDat_1 = unpack('i*', fread($unitFile, 400));

fseek($unitFile, $baseUnit_2[6]*$defaultBlockSize)
$playerDat_2 = unpack('i*', fread($unitFile, 400));

$warList_1 = new itemSlot($playerDat_1[32], $slotFile, 40);
$warList_2 = new itemSlot($playerDat_2[32], $slotFile, 40);

$warList = compareWarList($warList_1->slotData, $warList_2->slotData);

echo '<script>';
// Get data for each war
for ($i=0; $i<sizeof($warList); $i++) {
	fseek($unitDat, $warList[$i]*$defaultBlockSize);
	$warDat = unpack('i*', fread($unitFile, 100));
	
	// Output description based on war tpye
	switch ($warDat[10]) {
		case 1:
			echo 'textBlob("bDesc", "battleInfoContent", "This is a war for no reason");';
			break;
			
		case 2:
			echo 'textBlob("bDesc", "battleInfoContent", "This is a war over religion");';
			break;
			
		case 3:
			echo 'textBlob("bDesc", "battleInfoContent", "This is a war over land");';
			break;
			
		case 4:
			echo 'textBlob("bDesc", "battleInfoContent", "This is a war over lordship");';
			break;
	}
}

// Show the two sides in the battle and give the option for joining each side
echo 'addDiv("sidesDesc", "stdContainer", document.getElementById("battleInfoContent"));
	addDiv("side_1", "halfContain", document.getElementById("battleInfoContent"));
	optionbutton("1072,'.$postVals[1].',1", "side_1", "Join Side");
	addDiv("side_2", "halfContain", document.getElementById("battleInfoContent"));
	optionbutton("1072,'.$postVals[1].',2", "side_2", "Join Side");
';

// Get unit list for each side
$sideAUnits = new itemSlot($battleDat[15], $slotFile, 40);
$sideBUnits = new itemSlot($battleDat[16], $slotFile, 40);
showUnits($sideAUnits->slotData, $unitFile, $slotFile, "side_1");
showUnits($sideBUnits->slotData, $unitFile, $slotFile, "side_2");


function compareWarLists(&$shortList, &$longList) {
	echo 'Coparing war lists<br>';
	$returnList = [];

	// List format is war #, side #
	for($w=1; $w<=sizeof($shortList); $w+=2) {
		$matchKey = array_search($shortList[$w], $longList);
		if ($matchKey) {
			if ($shortList[$w+1] != $longList[$matchKey+1]) {
				$returnList[] = $shortList[$w];
			}
		}
	}
	return $returnList;
}

function showUnits ($unitList, $unitFile, $slotFile, $parent) {
	for ($i=0; $i<sizeof($unitList); $i++) {
		fseek($unitFile, $unitList[$i]*$defaultBlockSize);
		$dat = unpack('i*', fread($unitFile, 400));
		if ($dat[4] == 3) {
			echo 'addDiv("armyList_'.$unitList[$i].'", "stdContainer", document.getElementById("'.$parent.'"));
				textBlob("desc", "armyList_'.$unitList[$i].'", "Army Information")';
				
				$nextParent = "armyList_".$unitList[$i];
				$tmpDat = new itemSlot($dat[14], $slotFile, 40);
				showUnits($tmpDat->slotData, $unitFile, $sloFile, $nextParent);
		} else {
			echo 'newUnitDetail("bUnit_'.$sideAUnits->slotData[$i].', "'.$parent.'");'
		}
	}
}

?>