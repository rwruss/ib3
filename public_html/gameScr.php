<?php

class player {
	function player($itemArray) {
		$this->status = $itemArray[1];
		$this->intelSlot = $itemArray[24];
		$this->unitSlot = $itemArray[22];
	}
}

session_start();
$gameID = $_GET['gid'];
if ($gameID != $_SESSION['instance']) {echo "<script>alert('Game mismatch')</script>";exit;}
if (!isset($_SESSION['gameIDs'][$gameID])) echo "<script>window.location.replace('./index.php')</script>";
$pGameID = $_SESSION['gameIDs'][$gameID];
$postVals = explode(",", $_POST['val1']);

$gamePath = "../games/".$gameID;
include("../gameScripts/".$postVals[0].".php");

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