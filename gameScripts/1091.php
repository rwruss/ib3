<?php

// Postvals: 1: building ID, 2: taks option (char/unit type);

// Process importing a permanant character into a game

$newLoc = [1000,1000];

include("./slotFunctions.php");
include("./charClass.php");

// Get player data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Load information for the building that is creating the character
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$bldgDat = unpack('i*', fread($unitFile, 400));
$bldgDesc = explode('<->', file_get_contents($scnPath.'/buildings.desc'));
$bTypeDesc = explode('<-->', $bldgDesc[$bldgDat[10]]);

// Determine if there are available slots for importing a character
echo 'Check '.$bTypeDesc[7].' queue spots<br>';
$queueSpot = false;
for ($i=0; $i<$bTypeDesc[7]; $i++) {
	if ($bldgDat[$i+18] == 0) {
		$queueSpot = $i+18;
		break;
	}
}
if ($queueSpot) {
	echo 'Open spot - proceed';

	// Creatre the data for the new character
	$charTemplateFile = fopen($scnPath.'/charTemplates.dat', 'rb');
	$newChar = new character($postVals[2]*4, $charTemplateFile);
	fclose($charTemplateFile);

	// Save character with stats into game

	if (flock($unitFile, LOCK_EX)) {
		fseek($unitFile, 0, SEEK_END);
		$size = ftell($unitFile);
		$newID = $size/$defaultBlockSize;
		//fseek($unitFile, $newID*$defaultBlockSize);
		//fwrite($unitFile, $charDat);

		$newChar->changeID($newID);


		flock($unitFile, LOCK_UN);
	}

	// Add game specific infomrationf

	/// Record locations, controller, etc
	$newChar->charData[1] = $bldgDat[1];
	$newChar->charData[2] = $bldgDat[2];
	$newChar->charData[18] = 0;
	$newChar->charData[19] = 500;

	// Save the new character
	$newChar->save($unitFile);

	// Add the unit to the building slot
	echo 'Save unit '.$newID.' in building #'.$postVals[1].' queue spot '.$queueSpot;
	fseek($unitFile, $postVals[1]*$defaultBlockSize+4*($queueSpot-1));
	fwrite($unitFile, pack('i', $newID));
	//$bldgDat[$queueSpot+18]
} else {
	echo 'No available spots';
}




// Add character into player's list of available characters - NOT UNTIL COMPLETE WITH IMPORT
/*
$unitList = new itemSlot($playerDat[19], $slotFile, 40);
$unitList->addItem($newID, $slotFile);
*/
// Add to map if necessary


/// Load map data
/*
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');
$mapSlotNum = floor($bldgDat[2]/120)*120+floor($bldgDat[1]/120);
$mapSlotDat = new itemSlot($mapSlotNum, $mapSlotFile, 404);

for ($i=1; $i<=sizeof($mapSlotDat->slotData); $i++) {
	fseek($unitFile, $mapSlotDat->slotData[$i]*$defaultBlockSize);
	$mapCheckDat = unpack('i*', fread($unitFile, 400));

	if ($mapCheckDat[1] == $newLoc[0] && $mapCheckDat[2] == $newLoc[1]) {
		switch($mapCheckDat[4]) {
			/// Add to city if location is in city
			case 1:

			/// Add to army if location is in army`
			case 3:
				fseek($unitFile, $newID*$defaultBlockSize+76);
				fwrite($unitFile, pack('i', $mapSlotDat->slotData[$i]));
				break 2;
		}
	}
}
*/
// Record time that the character was imported into the game information file

fclose($unitFile);

?>
