<?php
include("./slotFunctions.php");
session_start();
if (isset($_SESSION['playerId'])) {
	$playerId = $_SESSION['playerId'];
	echo "conPanePlayer Games<p>
	<a href='javascript:void(0)' onclick=passClick(1009)>Join a Game</a>|
	<a href='javascript:void(0)' onclick=passClick(1010)>Create a new Game</a><hr>";
	
	$pDatFile = fopen("../users/userDat.dat", "rb");
	fseek($pDatFile, $playerId*500);
	$playerDat = fread($pDatFile, 500);
	$gameSlot = unpack("N", substr($playerDat, 8, 4));
	echo $gameSlot[1];
	if ($gameSlot[1] == 0) echo "Not playing any games now. (".$gameSlot[1].")";
	else {
		//echo "Read Slot ".$gameSlot[1]."<br>";
		$slotFile = fopen("../users/userSlots.slt", "rb");
		//$gameList = read_slot($slotFile, $gameSlot[1], 40);
		$gameList = unpack("N*", readSlotData($slotFile, $gameSlot[1], 40));
		fclose($slotFile);
		print_r($gameList);
		for ($i=1; $i<=sizeof($gameList); $i++) {
			if ($gameList[$i] > 0) echo "Game #".$gameList[$i]." - <a href='javascript:void(0)'; onclick=\x22passClick('1012,".$gameList[$i]."')\x22>Load Game</a><hr>";
			}
		}
	}
else include("../scripts/1002.php");

/*
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
		echo "Next Slot: ".$next_slot;
		}
	return $units_a;
	}
*/
?>