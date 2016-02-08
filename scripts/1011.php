<?php

echo "conPaneProcess Game Creation";
include("./slotFunctions.php");
session_start();
// Get new game ID
$numGames = filesize("../games/gameList.lst")/4;
$newId = $numGames+1;

$gameListFile = fopen("../games/gameList.lst", "ab");
fwrite($gameListFile, pack("N", $newId));

echo "Create game ".$newId;
mkdir("../games/".$newId);

$openGamesFile = fopen("../games/openGames.dat", "ab");
fwrite($openGamesFile, pack("N*", $newId, time()));
fclose($openGamesFile);


// Add game to players list of games
$uDatFile = fopen("../users/userDat.dat", "r+b");
fseek($uDatFile, $_SESSION['playerId']*500);
$uDat = fread($uDatFile, 500);
$gameSlot = unpack("N", substr($uDat, 8, 4));

// Copy over game files from scenario folder
if ($handle = opendir("../scenarios/1")) 
	{
    echo "Entries:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) 
		{
        echo "$entry<br>";
		copy("../scenarios/1/".$entry, "../games/".$newId."/".$entry);
		}
	closedir($handle);
	}
// Add basic player info and unstarted status
$playerFile = fopen("../games/".$newId."/unitDat.dat", "r+b");
fseek($playerFile, 400+399);
fwrite($playerFile, pack("C", 0));
fclose($playerFile);

// Prep game slot file
$gameSlotFile = fopen("../games/".$newId."/gameSlots.slt", "r+b");
fseek($gameSlotFile, 39);
fwrite($gameSlotFile, pack("C", 0));
fseek($gameSlotFile, 0);
	
//Create list of players in game
$pGameID = 1;
$newFile = fopen("../games/".$newId."/players.Dat", "wb");
fwrite($newFile, pack("N*", $_SESSION['playerId'], 1));
fclose($newFile);

// Update parameters file
$paramFile = fopen("../games/".$newId."/params.ini", "r+b");
fwrite($paramFile, pack("N", time()));
fseek($paramFile, 199);
fwrite($paramFile, pack("C", 0));
fclose($paramFile);

// add Game ID to player game list
if ($gameSlot[1] == 0) {
	echo "get new Slot";
	$uSlot = fopen("../users/userSlots.slt", "r+b");
	$newSlot = startASlot($uSlot, "../users/userSlots.slt");
	
	fseek($uDatFile, $_SESSION['playerId']*500+8);	
	fwrite($uDatFile, pack("N", $newSlot));
	
	addDataToSlot("../users/userSlots.slt", $newSlot, pack("N", $newId), $uSlot);
	fclose($uSlot);
	}
else {
	echo "Add game to slot";
	$uSlot = fopen("../users/userSlots.slt", "r+b");
	
	addDataToSlot("../users/userSlots.slt", $gameSlot[1], pack("N", $newId), $uSlot);
	fclose($uSlot);
	}
fclose($gameSlotFile);
fclose($uDatFile);
	
?>