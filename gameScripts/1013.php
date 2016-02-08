<?php

include("./slotFunctions.php");
/* old stuff
$playerFile = fopen($gamePath."/players.plr", "r+b");
fseek($playerFile, $pGameID*200);
$playerDat = fread($playerFile, 200);
$playerStats = unpack("C*", substr($playerDat, 0, 5));
$playerOther = unpack("S*", substr($playerDat, 24, 42));
$playerSlots = unpack("N*", substr($playerDat, 66, 100));

$leaderDat = file_get_contents($gamePath."/chars.dat", NULL, NULL, $playerSlots[1]*200, 200);
$leaderS = unpack("S*", substr($leaderDat, 20, 50));

$numTowns = filesize($gamePath."/towns.tn")/2000;
$townFile = fopen($gamePath."/towns.tn", "r+b");
$newID = max(1, $numTowns);

// Need to check for town conflicts, land use, land ownership etc.

echo "<script>alert('Establish Town at (".$leaderS[6].", ".$leaderS[7].")')</script>";

fseek($playerFile, $pGameID*200+26);
fwrite($playerFile, pack("S*", $leaderS[6], $leaderS[7], $newID));
fclose($playerFile);

$slot_handle = $gamePath."/gameSlots.slt";
$slot_file = fopen($slot_handle, "r+b");

fseek($townFile, $newID*2000+1999);
fwrite($townFile, pack("C", 0));
fseek($townFile, $newID*2000);
$townMemberSlot = startASlot($slot_file, $slot_handle);
fwrite($townFile, pack("N", $townMemberSlot));
addDataToSlot($slot_handle, $townMemberSlot, pack("v*", $playerSlots[1],1), $slot_file);
fclose($townFile);
fclose($slot_file);
*/
?>