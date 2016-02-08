<?php
/* old stuff
// Read technologies to determine available tasks
$playerFile = fopen($gamePath."/players.plr", "r+b");
fseek($playerFile, $pGameID*200);
$playerDat = fread($playerFile, 200);
$playerStats = unpack("C*", substr($playerDat, 0, 5));
$playerOther = unpack("S*", substr($playerDat, 24, 42));
$playerSlots = unpack("N*", substr($playerDat, 66, 100));

if ($playerSlots[19] > 0) {
	$slot_handle = $gamePath."/gameSlots.slt";
	$slot_file = fopen($slot_handle, "r+b");
	$techDat = read_slot($slot_file, $playerSlots[19], 40);
	}
else $techDat = array_fill(0,100,0);
$techDat[0] = 1;

$jobList = array("Forage", 0, 0, 0, 0);
for ($i=0; $i<1; $i+=5) {
	$techCheck = $techDat[$jobList[$i+1]]*$techDat[$jobList[$i+2]]*$techDat[$jobList[$i+3]]*$techDat[$jobList[$i+4]];
	if ($techCheck > 0) {
		echo $jobList[$i];
		}
	}

*/
?>