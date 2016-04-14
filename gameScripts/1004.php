<?php

include("./slotFunctions.php");
// Check to see if player is already in game
$playerList = unpack("i*", file_get_contents("../games/".$gameID."/players.dat"));
$idSpot = array_search($_SESSION['playerId'], $playerList);

$startLocation = [4800, 5260];

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
		if ($pGameSlot[1] == 0) {
			echo "get new Slot";
			$uSlot = fopen("../users/userSlots.slt", "r+b");
			$newSlot = startASlot($uSlot, "../users/userSlots.slt");

			fseek($uDatFile, $_SESSION['playerId']*500+8);
			fwrite($uDatFile, pack("N", $newSlot));

			addDataToSlot("../users/userSlots.slt", $newSlot, pack("N", $gameID), $uSlot);
			fclose($uSlot);
			}
		else {
			echo "Add game to slot";
			$uSlot = fopen("../users/userSlots.slt", "r+b");

			addDataToSlot("../users/userSlots.slt", $pGameSlot[1], pack("N", $gameID), $uSlot);
			fclose($uSlot);
			}
		fclose($uDatFile);

		// Add basic player info and unstarted status
		$pGameID = $unitIndex;
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
	$newCharID = $unitIndex+4;
	fseek($unitFile, $newCharID*$defaultBlockSize);
	fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1, 1, $postVals[1], $postVals[1], 0)); //x, y, icon, type, status, race, culture
	fseek($unitFile, $newCharID*$defaultBlockSize+36);
	//fwrite($unitFile, pack("i*", , 1, 1, $pGameID, $pGameID, 1, 1, 1)); // first name, second name, honoriffic, A controller, R controller, x Loc, yLoc
	fseek($unitFile, $newCharID*$defaultBlockSize+$unitBlockSize-1);
	fwrite($unitFile, pack("C", 0));

	// Add character to player slot
	$newSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	addDataToSlot($gamePath."/gameSlots.slt", $newSlot, pack("N", $newCharID), $gameSlot);

	// Record first city locatin in a new settlments slot
	$townSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	//addDataToSlot($gamePath."/gameSlots.slt", $townSlot, pack("N", $newCharID), $gameSlot);

	// Make a position slot to hold this character's leadership position in the town
	$positionSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	fseek($unitFile, $newCharID*$defaultBlockSize+48);
	fwrite($unitFile, pack('i', $positionSlot));


	// This records data in the player's data
	fseek($unitFile, $pGameID*$defaultBlockSize);
	fwrite($unitFile, pack("i*", 1, $postVals[1], $postVals[1])); // Status, Race, Culture
	fseek($unitFile, $pGameID*$defaultBlockSize+36);
	fwrite($unitFile, pack("i", $townSlot));  // Record the town slot
	fseek($unitFile, $pGameID*$defaultBlockSize+72);
	fwrite($unitFile, pack("i", $newSlot));  // Record the characters slot
	//fseek($playerFile, $pGameID*200+26);
	//fwrite($playerFile, pack("i", 100)); // Population

	// Record character as faction leader
	fseek($unitFile, $pGameID*$defaultBlockSize+48);
	fwrite($unitFile, pack("i", $newCharID));

	// Set settlment Data as applicable

	// get new index for next unit
	// Create a new town
	$townID = $unitIndex+8;
	fseek($unitFile, $townID*$defaultBlockSize);
	fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,1,$pGameID, $pGameID,1,$postVals[1],0));
	fseek($unitFile, $townID*$defaultBlockSize+$unitBlockSize-4);
	fwrite($unitFile, pack("i", 9990));

	// Create a credential list for the town and record this player as having full cred.
	$credListSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	fseek($unitFile, $townID*$defaultBlockSize+72);
	fwrite($unitFile, pack('i', $credListSlot));

	echo 'credintial slot:'.$credListSlot.'<br>';
	writeBlocktoSlot($gamePath."/gameSlots.slt", $credListSlot, pack('i*', -9, $pGameID), $gameSlot, 40); // ($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)

	// Make a chars slot for the new town and record the player's faction leader as the town's leader
	$townCharSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	fseek($unitFile, $townID*$defaultBlockSize+48);
	fwrite($unitFile, pack('i', $townCharSlot));
	echo 'Town Char Slot is '.$townCharSlot.'<br>';

	// Make a units slot for the new town
	$townUnitSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	fseek($unitFile, $townID*$defaultBlockSize+68);
	fwrite($unitFile, pack('i', $townUnitSlot));
	echo 'Town Unit Slot is '.$townUnitSlot.'<br>';

	// Make a resource slot for the new town
	$rscSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	fseek($unitFile, $townID*$defaultBlockSize+40);
	fwrite($unitFile, pack('i', $rscSlot));
	echo 'Town RSC Slot is '.$rscSlot.'<br>';

	// Make a task slot for the new town
	$taskSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	fseek($unitFile, $townID*$defaultBlockSize+80);
	fwrite($unitFile, pack('i', $taskSlot));
	echo 'Town task Slot is '.$taskSlot.'<br>';

	// Add char data to the town character slot
	$dat = pack('i*', -9, $newCharID);
	fseek($gameSlot, $townCharSlot*40+4);
	fwrite($gameSlot, $dat);

	// Record leadership of the town in the character's position information
	$newDat = pack('i*', -1*$townID, 10) ;
	writeBlocktoSlot($gamePath.'/gameSlots.slt', $positionSlot, $newDat, $gameSlot, 40); // writeBlocktoSlot($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)

	// Create a base civilian unit
	$civilianID = $townID+4;
	fseek($unitFile, ($civilianID)*$defaultBlockSize);
	// Basic parameters
	fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,8,$pGameID, $pGameID,1,$postVals[1],0));
	// Secondary information
	fwrite($unitFile, pack("i*", 1, 0, $townID));
	fseek($unitFile, ($civilianID)*$defaultBlockSize+$unitBlockSize-4);
	fwrite($unitFile, pack("i", 9990));

	// Create a base military unit
	$militaryID = $townID+8;
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

	// Record city location in the appropriate map slot
	// civilian and military unit are inside of the city to start with so are not added to the slot
	$mapSlot = floor($startLocation[1]/120)*120+floor($startLocation[0]/120);
	echo 'Record units at slot'.$mapSlot;
	$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'w+b');
	fseek($mapSlotFile, 120*90*404-4);
	fwrite($mapSlotFile, pack('i', 4));

	// Add city to map slot file
	$mapSlot = new itemSlot($mapSlot, $mapSlotFile, 404); /// start, file, size
	$mapSlot->addItem($townID, $mapSlot, $gamePath.'/mapSlotFile.slt'); // value, file, handle
	//addtoSlotGen($gamePath."/mapSlotFile.slt", $mapSlot, pack("i", $townID), $mapSlotFile, 404);

	// Add units to city unit slot
	addDataToSlot($gamePath."/gameSlots.slt", $townUnitSlot, pack("i", $civilianID), $gameSlot);
	addDataToSlot($gamePath."/gameSlots.slt", $townUnitSlot, pack("i", $militaryID), $gameSlot);

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

echo "<script>window.location.replace('./play.php?gameID=".$gameID."')</script>";

?>
