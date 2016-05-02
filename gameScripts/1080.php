<?php

// Get list of known plots
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*$defaultBlockSize);
$pDat = unpack('i*', fread($unitFile, 200));

// Show list that char can be invited to
$plotList = new itemList($pDat[20], $slotFile, 40);

echo '<script>
trg = document.getElementById("plotInviteContent")';
for ($i=0; $i<sizeof($plotList->slotData); $i++) {
	echo 'plotSummary({desc : "What\'d you do, man?", button : "Invite"})';
}
echo '</script>';

fclose($unitFile);

?>