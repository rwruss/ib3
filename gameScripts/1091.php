<?php

// Process importing a permanant character into a game
$newLoc = [1000,1000];

include("./slotFunctions.php");
include("./charClass.php");

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Get player data
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Creatre the data for the new character
$charTemplateFile = fopen($scnPath.'/charTemplates.dat', 'rb');
$newChar = new character($postVals[1]*4, $charTemplateFile);
fclose($charTemplateFile);

// Save character with stats into game

if (flock($unitFile, LOCK_EX)) {
	fseek($unitFile, 0, SEEK_END);
	$size = ftell($unitFile);
	$newID = $size/$defaultBlockSize;
	//fseek($unitFile, $newID*$defaultBlockSize);	
	//fwrite($unitFile, $charDat);
	$newChar->save($unitFile);

	
	flock($unitFile, LOCK_UN);
}

// Load information for the building that is creating the character

// Add game specific infomrationf

/// Record locations, controller, etc


// Add character into player's list of available characters
$unitList = new itemSlot($playerDat[19], $slotFile, 40);
$unitList->addItem($newID, $slotFile);

// Add to map if necessary


/// Load map data
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');
$mapSlotNum = floor($startLocation[1]/120)*120+floor($startLocation[0]/120);
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

// Record time that the character was imported into the game information file
fclose($slotFile);
fclose($unitFile);

?>