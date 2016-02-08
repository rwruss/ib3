<?php
/* old stuff
$playerDat = file_get_contents($gamePath."/players.plr", NULL, NULL, $pGameID*200, 200);
$playerStats = unpack("C*", substr($playerDat, 0, 5));
$playerOther = unpack("S*", substr($playerDat, 24, 42));
$playerSlots = unpack("N*", substr($playerDat, 66, 100));

if ($playerOther[15] == 0) {
	if ($playerOther[4] == 0) {
		echo "Your economy is based on the resources that are available in the surrounding countryside.  You don't own 
		any of these lands so you can only scavenge for resources.  If the lands you are on are owned by somebody else 
		they may not appreciate you taking what is theirs...";
		}
	else echo "You have established a town that will contribute to your economy in addition to what you can gather 
	from the surrounding countryside.  However, since you don't own any lands, you will be limited in what you can 
	produce.  Also, if the lands you are on are owned by somebody else they may not appreciate you taking what is theirs...";
	}
else {
	if ($playerOther[4] == 0) {
		echo "Your economy is based on the resources that are available in the surrounding countryside.  These 
		are your lands so you can exploit them to the maximum extent.";
		}
	else echo "You have established a town that will contribute to your economy in addition to what you can gather 
	from the surrounding countryside.  These are your lands so you can exploit them to the maximum extent.";
	}
*/
?>