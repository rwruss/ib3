<?php

include("./slotFunctions.php");
include("./cityClass.php");
// Check to see if player is already in game
$playerList = unpack("i*", file_get_contents("../games/".$gameID."/players.dat"));
$idSpot = array_search($_SESSION['playerId'], $playerList);

$startLocation = [4800, 5260];
$startTown = false;

$unitFile = fopen($gamePath."/unitDat.dat", "r+b");
if (flock($unitFile, LOCK_EX)) {  // acquire an exclusive lock
	clearstatcache();
	$unitIndex = max(1,filesize($gamePath.'/unitDat.dat')/$defaultBlockSize);
	// If player is not already in game, add the player to the game and record the game in the player's list
	if ($idSpot == FALSE) {

		// Add game to players list of games
		$uDatFile = fopen("../users/userDat.dat", "r+b");
		fseek($uDatFile, $_SESSION['playerId']*500);
		$uDat = fread($uDatFile, 500);
		$pGameSlot = unpack("N", substr($uDat, 8, 4));

		// add Game ID to player game list
		$uSlot = fopen("../users/userSlots.slt", "r+b");
		if ($pGameSlot[1] == 0) {
			echo "get new Slot";

			$newSlot = startASlot($uSlot, "../users/userSlots.slt");

			fseek($uDatFile, $_SESSION['playerId']*500+8);
			fwrite($uDatFile, pack("N", $newSlot));

			addDataToSlot("../users/userSlots.slt", $newSlot, pack("N", $gameID), $uSlot);
			fclose($uSlot);
			}
		else {
			echo "Add game to slot";

			addDataToSlot("../users/userSlots.slt", $pGameSlot[1], pack("N", $gameID), $uSlot);
			fclose($uSlot);
			}
		fclose($uDatFile);

		// Add basic player info and unstarted status
		$pGameID = $unitIndex;
		$unitIndex +=4;
		fseek($unitFile, $pGameID*$defaultBlockSize+399);
		fwrite($unitFile, pack("C", 0));

		// Add player to list of players for this game
		$pListFile = fopen("../games/".$gameID."/players.dat", "ab");
		fwrite($pListFile, pack("i*", $_SESSION['playerId'], $pGameID*-1));
		fclose($pListFile);
	} else {
		$pGameID = intval($playerList[$idSpot+1]*-1);
	}


	//$pGameID = $_SESSION['gameIDs'][$_GET['gid']];


	$gameSlot = fopen($gamePath."/gameSlots.slt", "r+b");
	// Create player character
	$newCharID = $unitIndex;
	$unitIndex +=4;
	fseek($unitFile, $newCharID*$defaultBlockSize);
	fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1, 1, $postVals[1], $postVals[1], 0)); //x, y, icon, type, status, race, culture
	fseek($unitFile, $newCharID*$defaultBlockSize+36);
	//fwrite($unitFile, pack("i*", , 1, 1, $pGameID, $pGameID, 1, 1, 1)); // first name, second name, honoriffic, A controller, R controller, x Loc, yLoc
	fseek($unitFile, $newCharID*$defaultBlockSize+$unitBlockSize-1);
	fwrite($unitFile, pack("C", 0));

	// Add character to player slot
	$charSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	addDataToSlot($gamePath."/gameSlots.slt", $charSlot, pack("N", $newCharID), $gameSlot);

	// Make a position slot to hold this character's leadership position in the town
	$positionSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	fseek($unitFile, $newCharID*$defaultBlockSize+48);
	fwrite($unitFile, pack('i', $positionSlot));

	// Create map slot file
	$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'w+b');
	fseek($mapSlotFile, 120*90*404-4);
	fwrite($mapSlotFile, pack('i', 0));

	$mapSlotNum = floor($startLocation[1]/120)*120+floor($startLocation[0]/120);
	echo 'Record units at slot'.$mapSlotNum;

	$mapSlot = new itemSlot($mapSlotNum, $mapSlotFile, 404); /// start, file, size


	// Create a new town
	$townID = 0;
	$townSlot = 0;
	if ($startTown) {
		$townID = $unitIndex;
		$unitIndex +=4;
		$townData = newTown($townID, $unitFile, $gameSlot);

		// Record first city location in a new settlments slot
		$townSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
		$mapSlot->addItem($townID, $mapSlotFile); // value, file
	}

	// This records data in the player's data
	fseek($unitFile, $pGameID*$defaultBlockSize);
	fwrite($unitFile, pack("i*", 1, $postVals[1], $postVals[1])); // Status, Race, Culture
	fseek($unitFile, $pGameID*$defaultBlockSize+36);
	fwrite($unitFile, pack("i", $townSlot));  // Record the town slot
	fseek($unitFile, $pGameID*$defaultBlockSize+72);
	fwrite($unitFile, pack("i", $charSlot));  // Record the characters slot
	fseek($unitFile, $pGameID*$defaultBlockSize+40);
	fwrite($unitFile, pack("i", $townID)); // Population

	// Record character as faction leader
	fseek($unitFile, $pGameID*$defaultBlockSize+48);
	fwrite($unitFile, pack("i", $newCharID));

	// Set settlment Data as applicable

	// get new index for next unit


	// Add char data to the town character slot
	$dat = pack('i*', -9, $newCharID);
	fseek($gameSlot, $townData[13]*40+4);
	fwrite($gameSlot, $dat);

	// Record leadership of the town in the character's position information
	$newDat = pack('i*', -1*$townID, 10) ;
	writeBlocktoSlot($gamePath.'/gameSlots.slt', $positionSlot, $newDat, $gameSlot, 40); // writeBlocktoSlot($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)

	// Create units based on the following unit IDs
	$unitSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	$unitInf = explode("<->", file_get_contents($scnPath.'/units.desc'));
	$makeTypes = [1, 2];
	for($i=0; $i<sizeof($makeTypes); $i++) {
		$newId = $unitIndex;
		$unitIndex +=4;;

		$thisDtl = explode('<-->', $unitInf[$makeTypes[$i]]);
		$typeParams = explode(",", $thisDtl[1]);

		fseek($unitFile, ($newId)*$defaultBlockSize);
		// Basic parameters
		fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,6,$pGameID, $pGameID,1,$postVals[1],0));
		// Secondary information
		fwrite($unitFile, pack("i*", $makeTypes[$i], 0, $townID));
		fseek($unitFile, ($newId)*$defaultBlockSize+$unitBlockSize-4);
		fwrite($unitFile, pack("i", 9990));

		addDataToSlot($gamePath."/gameSlots.slt", $unitSlot, pack("N", $newId), $gameSlot);
		if (!$startTown) {
			$mapSlot->addItem($newId, $mapSlotFile); // value, file
		} else {
			//addDataToSlot($gamePath."/gameSlots.slt", $townUnitSlot, pack("i", $newId), $gameSlot);
		}
	}

	// If no town is being started, create a resource unit for the player
	if (!$startTown) {
		$newId = $unitIndex;
		$unitIndex +=4;;

		$thisDtl = explode('<-->', $unitInf[6]);
		$typeParams = explode(",", $thisDtl[1]);

		fseek($unitFile, ($newId)*$defaultBlockSize);
		// Basic parameters
		fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,$typeParams[3],$pGameID, $pGameID,1,$postVals[1],0));
		// Secondary information
		fwrite($unitFile, pack("i*", 10, 0, $townID));
		fseek($unitFile, ($newId)*$defaultBlockSize+$unitBlockSize-4);
		fwrite($unitFile, pack("i", 9990));

		addDataToSlot($gamePath."/gameSlots.slt", $unitSlot, pack("N", $newId), $gameSlot);
		if (!$startTown) {
			$mapSlot->addItem($newId, $mapSlotFile); // value, file
		} else {
			//addDataToSlot($gamePath."/gameSlots.slt", $townUnitSlot, pack("i", $newId), $gameSlot);
		}
	}

	/*
	// Create a base civilian unit
	$civilianID = $unitIndex+12;
	fseek($unitFile, ($civilianID)*$defaultBlockSize);
	// Basic parameters
	fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,8,$pGameID, $pGameID,1,$postVals[1],0));
	// Secondary information
	fwrite($unitFile, pack("i*", 1, 0, $townID));
	fseek($unitFile, ($civilianID)*$defaultBlockSize+$unitBlockSize-4);
	fwrite($unitFile, pack("i", 9990));

	// Create a base military unit
	$militaryID = $unitIndex+16;
	fseek($unitFile, ($militaryID)*$defaultBlockSize);
	// Basic parameters
	fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,6,$pGameID, $pGameID,1,$postVals[1],0));
	// Secondary information
	fwrite($unitFile, pack("i*", 1, 0, $townID));
	fseek($unitFile, ($militaryID)*$defaultBlockSize+$unitBlockSize-4);
	fwrite($unitFile, pack("i", 9990));

	// Record new units in unit slot
	$unitSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	addDataToSlot($gamePath."/gameSlots.slt", $townSlot, pack("N", $townID), $gameSlot);
	addDataToSlot($gamePath."/gameSlots.slt", $unitSlot, pack("N", $civilianID), $gameSlot);
	addDataToSlot($gamePath."/gameSlots.slt", $unitSlot, pack("N", $militaryID), $gameSlot);
	*/
	// Record city location in the appropriate map slot
	// civilian and military unit are inside of the city to start with so are not added to the slot



	// Add city to map slot file

	//addtoSlotGen($gamePath."/mapSlotFile.slt", $mapSlot, pack("i", $townID), $mapSlotFile, 404);

	// Add units to city unit slot

	// Record the unit slot in the player data
	fseek($unitFile, $pGameID*$defaultBlockSize+84);
	fwrite($unitFile, pack("i", $unitSlot));

	// write to end of block to ensure it is full
	fseek($unitFile, $pGameID*$defaultBlockSize+399);
	fwrite($unitFile, pack("C", 0));

	flock($unitFile, LOCK_UN); // release the lock  on the player File
	fclose($unitFile);
	fclose($gameSlot);
	fclose($mapSlotFile);
}

//echo "<script>window.location.replace('./play.php?gameID=".$gameID."')</script>";



?>
