<?php
/* Old stuff
$playerDat = file_get_contents($gamePath."/players.plr", NULL, NULL, $pGameID*200, 200);
$playerStats = unpack("C*", substr($playerDat, 0, 5));
$playerOther = unpack("s*", substr($playerDat, 24, 42));
$playerSlots = unpack("N*", substr($playerDat, 66, 100));


if ($playerDat[3] == 0) echo "You are an independent Lord<hr>";
else echo "Your Lord is ".$playerDat[3]."<hr>";
if ($playerSlots[11] == 0) echo "You do not have any diplomatic relationships at this time";
else {
	$diplFile = fopen("gameSlots.slt", "rb");
	$diplDat = read_slot($diplFile, $playerSlots[11], 40);
	$diplA = unpack("v*", $diplDat);
	$numActions = $diplDat/10;
	for ($i=$numActions; $i>0; $i--) {
		echo "Diplomatic Action";
		}
	}
*/
?>