<?php

include('./slotFunctions.php');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');


// Get list of known plots
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$pDat = unpack('i*', fread($unitFile, 200));

// Show list that char can be invited to
echo 'Read slot '.$pDat[20];
$plotList = new itemSlot($pDat[20], $slotFile, 40);
print_r($plotList->slotData);
$useList = array_filter($plotList->slotData);
print_r($useList);
echo '<script>
trg = document.getElementById("plotInviteContent");';
for ($i=1; $i<=sizeof($useList); $i++) {
	echo 'plotSummary({desc : "What\'d you do, man?", button : "Invite", id:'.$useList[$i].'}, trg);';
}
echo '</script>';

fclose($unitFile);
fclose($slotFile);

?>
