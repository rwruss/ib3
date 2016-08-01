<?php

include('./slotFunctions.php');
include('./cityClass.php');
include('./unitClass.php');
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
		fflush($unitFile);

		// Add player to list of players for this game
		$pListFile = fopen("../games/".$gameID."/players.dat", "ab");
		fwrite($pListFile, pack("i*", $_SESSION['playerId'], $pGameID*-1));
		fclose($pListFile);
	} else {
		echo 'Already in game';
		$pGameID = intval($playerList[$idSpot+1]*-1);
	}


	//$pGameID = $_SESSION['gameIDs'][$_GET['gid']];


	$gameSlot = fopen($gamePath."/gameSlots.slt", "r+b");
	// Create player character
	$newCharID = $unitIndex;
	$unitIndex +=4;
	fseek($unitFile, $newCharID*$defaultBlockSize);
	fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1, 4, $postVals[1], $postVals[1], 1, 0, 0, 1));
	//fseek($unitFile, $newCharID*$defaultBlockSize+36);
	//fwrite($unitFile, pack("i*", , 1, 1, $pGameID, $pGameID, 1, 1, 1)); // first name, second name, honoriffic, A controller, R controller, x Loc, yLoc
	fseek($unitFile, $newCharID*$defaultBlockSize+$unitBlockSize-1);
	fwrite($unitFile, pack("C", 0));

	// Add character to player slot
	$charSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	addDataToSlot($gamePath."/gameSlots.slt", $charSlot, pack("i", $newCharID), $gameSlot);

	echo 'Char slot is '.$charSlot.'<br>';

	// Make a position slot to hold this character's leadership position in the town
	$positionSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	fseek($unitFile, $newCharID*$defaultBlockSize+48);
	fwrite($unitFile, pack('i', $positionSlot));

	// Create map slot file IF it doesn't already exist
	$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');
	//fseek($mapSlotFile, 120*90*404-4);
	//fwrite($mapSlotFile, pack('i', 0));

	$mapSlotNum = floor($startLocation[1]/120)*120+floor($startLocation[0]/120);
	echo 'Record units at slot'.$mapSlotNum;

	$mapSlot = new blockSlot($mapSlotNum, $mapSlotFile, 404); /// start, file, size

	// Create a unit slot for the player
	$unitSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
	echo 'Unit slot is '.$unitSlot.'<br>';
	$unitList = new itemSlot($unitSlot, $gameSlot, 40);

	// Create a new town
	$townID = 0;
	$armyID = 0;
	$townSlot = 0;
	if ($startTown) {
		$townID = $unitIndex;
		$unitIndex +=4;
		$townDtls = [$startLocation[1], $startLocation[2], $pGameID, $postVals[1]];
		$townData = newTown($townID, $unitFile, $gameSlot, $townDtls);

		// Record first city location in a new settlments slot
		$townSlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");

		$mLoc = sizeof($mapSlot->slotData);
		for ($i=1; $i<=sizeof($mapSlot->slotData); $i+=2) {
			if ($mapSlot->slotData[$i] == 0) {
				$mLoc = $i;
				break;
			}
		}

		$mapSlot->addItem($mapSlotFile, pack('i*', $townID, 1), $mLoc); // file, bin value, loc
	} else {
		// Create a new army unit for the items to be grouped into
		$armyID = $unitIndex;
		$unitIndex += 4;

		$armySlot = startASlot($gameSlot, $gamePath."/gameSlots.slt");
		fseek($unitFile, $armyID*$defaultBlockSize);
		fwrite($unitFile, pack('i*', $startLocation[0],$startLocation[1],1,3,$pGameID, $pGameID,1,$postVals[1],0));
		fseek($unitFile, $armyID*$defaultBlockSize + 52);
		fwrite($unitFile, pack('i', $armySlot));

		$mLoc = sizeof($mapSlot->slotData);
		for ($i=1; $i<=sizeof($mapSlot->slotData); $i+=2) {
			if ($mapSlot->slotData[$i] == 0) {
				$mLoc = $i;
				break;
			}
		}

		$mapSlot->addItem($mapSlotFile, pack('i*', $armyID, 0), $mLoc); // file, bin value, loc
		$armyObj = new itemSlot($armySlot, $gameSlot, 40);
		$unitList->addItem($armyID, $gameSlot);
	}
	echo 'Army ID is '.$armyID.'<p>';
	// This records data in the player's data
	/*
	fseek($unitFile, $pGameID*$defaultBlockSize);
	fwrite($unitFile, pack("i*", 1, $postVals[1], $postVals[1], 13)); // Status, Race, Culture, Type
	fseek($unitFile, $pGameID*$defaultBlockSize+36);
	fwrite($unitFile, pack("i", $townSlot));  // Record the town slot
	fseek($unitFile, $pGameID*$defaultBlockSize+72);
	fwrite($unitFile, pack("i", $charSlot));  // Record the characters slot
	fseek($unitFile, $pGameID*$defaultBlockSize+40);
	fwrite($unitFile, pack("i", $townID)); // Population

	// Record character as faction leader
	fseek($unitFile, $pGameID*$defaultBlockSize+48);
	fwrite($unitFile, pack("i", $newCharID));
	*/

	$newPlayer = new unit($pGameID, $unitFile, 400);

	$newPlayer->unitDat[1] = 1;  // Status
	$newPlayer->unitDat[2] = $postVals[1];  // Race
	$newPlayer->unitDat[3] = $postVals[1];  // Culture
	$newPlayer->unitDat[4] = 13;  // Player type number
	$newPlayer->unitDat[10] = $townSlot;  // Record the town slot
	$newPlayer->unitDat[19] = $charSlot;  // Record the char slot
	$newPlayer->unitDat[11] = $townID;  // Primary City

	$newPlayer->saveAll($unitFile);

	// Set settlment Data as applicable

	// get new index for next unit


	// Add char data to the town character slot
	if ($startTown) {
		$dat = pack('i*', -9, $newCharID);
		fseek($gameSlot, $townData[13]*40+4);
		fwrite($gameSlot, $dat);
	}

	// Record leadership of the town in the character's position information
	$newDat = pack('i*', -1*$townID, 10) ;
	writeBlocktoSlot($gamePath.'/gameSlots.slt', $positionSlot, $newDat, $gameSlot, 40); // writeBlocktoSlot($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)

	// Create units based on the following unit IDs

	$unitInf = explode("<->", file_get_contents($scnPath.'/units.desc'));
	$makeTypes = [1, 2];
	for($i=0; $i<sizeof($makeTypes); $i++) {

		$newId = $unitIndex;
		$unitIndex +=4;;
		echo 'Make unit type '.$makeTypes[$i].' at unit #'.$newId.'<br>';
		$thisDtl = explode('<-->', $unitInf[$makeTypes[$i]]);
		$typeParams = explode(",", $thisDtl[1]);
		fseek($unitFile, ($newId)*$defaultBlockSize+$unitBlockSize-4);
		fwrite($unitFile, pack("i", 0));

		$newUnit = new unit($newId, $unitFile, 400);
		$newUnit->unitDat[1] = $startLocation[0]; // X Loc
		$newUnit->unitDat[2] = $startLocation[1]; // Y Loc
		$newUnit->unitDat[3] = 1; // Icon
		$newUnit->unitDat[4] = $typeParams[3]; // Unit Type
		$newUnit->unitDat[5] = $pGameID; // Owner
		$newUnit->unitDat[6] = $pGameID; // Controller
		$newUnit->unitDat[7] = 1; // Status
		$newUnit->unitDat[8] = $postVals[1]; // Culture
		$newUnit->unitDat[10] = $makeTypes[$i]; // Troop Type
		$newUnit->unitDat[12] = $townID; // Current Loc
		$newUnit->unitDat[15] = $armyID; // Army ID
		$newUnit->unitDat[17] = $thisDtl[10]; // action point regeneration

		$newUnit->saveAll($unitFile);
		/*
		fseek($unitFile, ($newId)*$defaultBlockSize);
		// Basic parameters
		fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,$typeParams[3],$pGameID, $pGameID,1,$postVals[1],0));
		// Secondary information
		fwrite($unitFile, pack("i*", $makeTypes[$i], 0, $townID));


		fseek($unitFile, ($newId)*$defaultBlockSize+56);
		fwrite($unitFile, pack('i', $armyID));
		*/
		//addDataToSlot($gamePath."/gameSlots.slt", $unitSlot, pack("N", $newId), $gameSlot);
		echo 'Record unit #'.$newId.' in unit slot<br>';
		$unitList->addItem($newId, $gameSlot);
		print_r($unitList->slotData);
		if (!$startTown) {
			echo 'Record in army slot<br>';
			$armyObj->addItem($newId, $gameSlot);
			//->addItem($newId, $mapSlotFile); // value, file
		} else {
			//addDataToSlot($gamePath."/gameSlots.slt", $townUnitSlot, pack("i", $newId), $gameSlot);
		}
	}

	// If no town is being started, create a resource unit for the player
	if (!$startTown) {
		/*
		$newId = $unitIndex;
		$unitIndex +=4;;

		$thisDtl = explode('<-->', $unitInf[6]);
		$typeParams = explode(",", $thisDtl[1]);

		fseek($unitFile, ($newId)*$defaultBlockSize);
		// Basic parameters
		fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,$typeParams[3],$pGameID, $pGameID,1,$postVals[1],0));
		// Secondary information
		fwrite($unitFile, pack("i*", 6, 0, $townID));
		fseek($unitFile, ($newId)*$defaultBlockSize+$unitBlockSize-4);
		fwrite($unitFile, pack("i", 9990));
		*/
		$newId = $unitIndex;
		$unitIndex +=4;;
		echo 'Make unit type 6 at unit #'.$newId.'<br>';
		$thisDtl = explode('<-->', $unitInf[6]);
		$typeParams = explode(",", $thisDtl[1]);
		fseek($unitFile, ($newId)*$defaultBlockSize+$unitBlockSize-4);
		fwrite($unitFile, pack("i", 0));

		$newUnit = new unit($newId, $unitFile, 400);
		$newUnit->unitDat[1] = $startLocation[0]; // X Loc
		$newUnit->unitDat[2] = $startLocation[1]; // Y Loc
		$newUnit->unitDat[3] = 1; // Icon
		$newUnit->unitDat[4] = $typeParams[3]; // Unit Type
		$newUnit->unitDat[5] = $pGameID; // Owner
		$newUnit->unitDat[6] = $pGameID; // Controller
		$newUnit->unitDat[7] = 1; // Status
		$newUnit->unitDat[8] = $postVals[1]; // Culture
		$newUnit->unitDat[10] = 6; // Troop Type
		$newUnit->unitDat[12] = $townID; // Current Loc
		$newUnit->unitDat[15] = 0; // Army ID
		$newUnit->unitDat[17] = $thisDtl[10]; // action point regeneration
		$newUnit->unitDat[25] = 100; // Population

		$newUnit->saveAll($unitFile);

		//addDataToSlot($gamePath."/gameSlots.slt", $unitSlot, pack("N", $newId), $gameSlot);
		echo 'Record unit #'.$newId.' in unit slot<br>';
		$unitList->addItem($newId, $gameSlot);
		print_r($unitList->slotData);
		if (!$startTown) {
			$mLoc = sizeof($mapSlot->slotData);
			for ($i=1; $i<=sizeof($mapSlot->slotData); $i+=2) {
				if ($mapSlot->slotData[$i] == 0) {
					$mLoc = $i;
					break;
				}
			}
			//$mapSlot->addItem($mapSlotFile, pack('i*', $newId, 1));
			$mLoc = $mapSlot->findLoc(0, 2);
			$mapSlot->addItem($mapSlotFile, pack('i*', $newId, 1), $mLoc); // file, bin value, loc

			//$mapSlot->addItem($newId, $mapSlotFile); // value, file
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
	//fseek($unitFile, $pGameID*$defaultBlockSize+84);
	//fwrite($unitFile, pack("i", $unitSlot));
	$newPlayer->unitDat[22] = $unitSlot;
	$newPlayer->saveAll($unitFile);

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
