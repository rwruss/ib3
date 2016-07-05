<?php

//postVals 1 = offset group

date_default_timezone_set('America/Chicago');
session_start();
if (isset($_SESSION['playerId'])) {
	echo 'conPane<a href="javascript:void(0)" onclick=passClick("1009,'.(max(0, $postVals[1]-1)).'")>Previous</a>|<a href="javascript:void(0)" onclick=passClick("1009,'.($postVals[1]+1).'")>Next</a>';
	$playerId = $_SESSION['playerId'];

	// Get games player is inf
	$pDatFile = fopen("../users/userDat.dat", "rb");
	fseek($pDatFile, $playerId*500);
	$playerDat = fread($pDatFile, 500);
	fclose($pDatFile);
	$gameSlot = unpack("N", substr($playerDat, 8, 4));
	echo $gameSlot[1];
	$gameList = array();
	if ($gameSlot[1] > 0) {
		//echo "Read Slot ".$gameSlot[1]."<br>";
		$slotFile = fopen("../users/userSlots.slt", "rb");
		$gameList = read_slot($slotFile, $gameSlot[1], 40);
		fclose($slotFile);
		}

	// Get list of open games
	$openGamesFile = fopen("../games/openGames.dat", "rb");
	fseek($openGamesFile, 2*40*$postVals[1]);
	$testDat = fread($openGamesFile, 2*10*4);
	$openDat = unpack("N*", $testDat);
	fclose($openGamesFile);
	$showGames = min(10, sizeof($openDat)/2);

	for ($i=0; $i<$showGames; $i++) {
		$gamePlayers = filesize("../games/".$openDat[$i*2+1]."/players.dat")/4;
		echo "<div style='border:1px solid #000000;'>Game ".$openDat[$i*2+1]." - Started ".date("m/d",$openDat[$i*2+2])."<br>
		Players: ".$gamePlayers."<br>";
		if (array_search($openDat[$i*2+1], $gameList)) echo "alreay in this game</div>";
		else echo "<a href='javascript:void(0)' onclick=\x22passClick('1013,".$openDat[$i*2+1]."')\x22>Join this game</a></div>";
		}
	if ($showGames == 0) echo "No open Games - <a href='javascript:void(0)' onclick=passClick(1010)>Create One!</a>";
	}
else include("../scripts/1002.php");

function read_slot($file, $slot_num, $slot_size)
	{
	$next_slot = $slot_num;
	$units_a = array();
	//echo "Next slot = ".$next_slot."<br>";
	while ($next_slot > 0)
		{
		fseek($file, $next_slot*$slot_size);
		$slot_dat = fread($file, $slot_size);
		$units_a = array_merge($units_a, unpack("N*", substr($slot_dat, 4)));

		$slot_check = unpack("N", $slot_dat);
		$next_slot = $slot_check[1];
		//echo "Next Slot: ".$next_slot;
		}
	return $units_a;
	}

?>
