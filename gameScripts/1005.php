<?php
/* Old stuff
$playerDat = file_get_contents($gamePath."/players.plr", NULL, NULL, $pGameID*200, 200);
$playerStats = unpack("C*", substr($playerDat, 0, 5));
$playerOther = unpack("S*", substr($playerDat, 24, 42));
$playerSlots = unpack("N*", substr($playerDat, 66, 100));

if ($playerOther[4] == 0) {
	echo "You do not have a settlement established at this time.  Your faction leader can estbalish a setllment at his 
	current location.  Once you have a permanent settlement you can begin laying claim to lands nearby.<br>
	
	If you are satisfied with the location of your faction leader's location, click below to begin the establishment 
	of your settlement.
	
	If you would like to join an existing town, simply move your faction leader into the town and select join town.<p>
	<a href='javascript:void(0);' onclick=startTask();scrMod(1013)>Establish Settlement</a><br>";
	}
else echo "<a href='javascript:void(0);' onclick=\x22makeBox('townDtl', '1014,".$playerOther[4]."', 500, 500, 200, 50)\x22>View Settlement</a><br>";

*/

?>