<?php
/* Old stuff
$playerDat = file_get_contents($gamePath."/players.plr", NULL, NULL, $pGameID*200, 200);
$playerStats = unpack("C*", substr($playerDat, 0, 5));
$playerOther = unpack("s*", substr($playerDat, 24, 42));
$playerSlots = unpack("N*", substr($playerDat, 66, 100));

echo "Faction Laws<br>
	<b>Tax Rate</b><br>
	This is the amount of tax that all of your direct vassals will be taxed per day.<br>
	Current Rate:
	<hr>
	<b>Current Religion</b><br>
	This is the state religion for your realm.  Note that having a different religion than your vassals/liege will 
	affect your relationship with them.  This will also contribute to your score based on the overall performance of 
	all members of this religion.<br>
	Current Religion:".$playerStats[4]."<br>
	You may change your religion at any time but it does take some time to effect the change.<br>
	<a href='javascript:void(0);' onclick=\x22makeBox('relChange', 1010, 500, 500, 200, 50)\x22>Change Religion</a>";
*/
?>